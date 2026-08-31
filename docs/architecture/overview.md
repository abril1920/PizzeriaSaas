# Arquitectura de software

## 1. Decisión arquitectónica

El producto se construirá como un **monolito modular**: una API REST en Laravel y una SPA independiente en React. Esta decisión mantiene el despliegue y el desarrollo inicial simples, sin impedir separar servicios en el futuro cuando exista una necesidad real de escala.

PostgreSQL será una única base de datos compartida. El aislamiento multiempresa se implementará con una columna `empresa_id` en los datos operativos y se impondrá en todas las capas de la aplicación.

```text
Navegador
    |
    v
React SPA (apps/web/)
    | HTTPS + JSON + token Sanctum
    v
Laravel API (apps/api/)
    |-- autenticacion y autorizacion
    |-- contexto de empresa
    |-- modulos de negocio
    |-- transacciones
    v
PostgreSQL (database/)
```

## 2. Estructura del repositorio

```text
PizzeriaSaas/
|-- apps/
|   |-- api/                         # Aplicacion Laravel
|   |   |-- app/
|   |   |   |-- Domain/              # Reglas de negocio por modulo
|   |   |   |-- Http/Controllers/Api/# Adaptadores HTTP delgados
|   |   |   |-- Http/Requests/       # Validacion de entradas
|   |   |   |-- Models/              # Modelos Eloquent
|   |   |   |   `-- Support/Tenancy/ # Contexto y alcance de empresa
|   |   |-- database/migrations/     # Fuente de verdad evolutiva
|   |   |-- database/seeders/
|   |   `-- tests/Feature/ y tests/Unit/
|   `-- web/                         # Aplicacion React
|       |-- src/app/                 # Rutas, sesion y API
|       |-- src/features/            # UI y estado por modulo
|       |-- src/shared/              # Componentes y utilidades compartidas
|       `-- src/pages/               # Composicion de rutas
|-- database/
|   |-- snapshots/                   # Referencias SQL no ejecutables
|   |   `-- pizzeria_saas_postgresql.sql
|   `-- diagrams/
|-- docs/
|   |-- architecture/overview.md     # Este documento
|   `-- product/project-overview.md  # Requisitos funcionales
|-- infra/docker/                    # Dockerfiles y configuracion futura
`-- compose.yaml                     # Servicios locales
```

El archivo SQL actual sirve como referencia y snapshot inicial. La fuente de verdad evolutiva del esquema será `apps/api/database/migrations`; los datos base vivirán en seeders.

## 3. Modulos de negocio

| Modulo | Responsabilidad | Entidades principales |
| --- | --- | --- |
| Identidad y empresa | Sesion, perfil, usuarios, roles, permisos y configuracion | Empresa, Usuario, Rol, Permiso |
| Catalogo | Carta, categorias y precios | CategoriaProducto, Producto |
| Inventario | Insumos, proveedores, recetas, existencias y movimientos | Ingrediente, Proveedor, Receta, Existencia, MovimientoInventario |
| Clientes | Datos e historial comercial | Cliente |
| Pedidos | Flujo de cocina y estados del pedido | Pedido, PedidoDetalle |
| Ventas | Cobro, comprobante, pagos y anulaciones | Venta, VentaDetalle, Pago |
| Analitica | KPIs y reportes de solo lectura | Consultas y vistas de reporte |

Los modulos se comunican mediante Actions o Services dentro del mismo proceso. No se accederan tablas ajenas desde controladores; cada operacion se inicia en el modulo responsable.

## 4. Backend Laravel

Cada endpoint sigue este flujo:

```text
Ruta -> middleware auth:sanctum -> resolver empresa -> permiso/policy
     -> Form Request -> Controller -> Action o Service -> Modelo/Repositorio
     -> API Resource -> respuesta JSON
```

- Los controladores reciben la solicitud, delegan el caso de uso y devuelven recursos JSON; no contienen reglas de negocio ni SQL.
- Los Form Requests validan formato, campos permitidos y relaciones. Las reglas de pertenencia a la empresa se validan en el servidor.
- Las Actions representan operaciones con efecto de negocio, por ejemplo `CrearPedido`, `CambiarEstadoPedido`, `RegistrarMovimientoInventario` y `ConfirmarVenta`.
- Los Services contienen logica reutilizable y coordinan varias entidades, como calcular totales o descontar receta.
- Las Policies y el middleware de permisos deciden si el usuario puede ejecutar una accion concreta.
- Los API Resources son el contrato de salida. Los nombres de campos y codigos de error deben ser consistentes y versionables bajo `/api/v1`.

### Contexto de empresa y aislamiento

`EmpresaContext` se crea despues de autenticar al usuario y obtiene el identificador desde `auth()->user()->empresa_id`. El frontend no envia ni puede cambiar este valor.

Los modelos que pertenecen a una empresa implementaran un contrato o trait `BelongsToEmpresa`. El trait aplica un Global Scope por `empresa_id` y rellena el campo al crear registros. Esto reduce errores accidentales, pero no reemplaza la validacion explicita de relaciones.

Reglas obligatorias:

- Toda consulta operativa se inicia ya acotada al `EmpresaContext`.
- Los route model bindings de recursos multiempresa deben buscar usando `empresa_id` y devolver `404` si el recurso no pertenece a la empresa actual.
- Una relacion como producto-categoria, pedido-cliente o receta-producto-ingrediente debe comprobar que ambos extremos pertenecen a la misma empresa.
- Ningun endpoint acepta `empresa_id` en su body o query string, salvo las funciones futuras y aisladas del superadministrador SaaS.
- Se deben crear restricciones compuestas en las migraciones, o triggers equivalentes, para impedir relaciones cruzadas. Las FK simples actuales validan que el ID exista, pero no que comparta `empresa_id`.
- Como defensa adicional posterior se evaluara Row Level Security (RLS) en PostgreSQL, una vez que la conexion pueda establecer el tenant de forma segura por solicitud.

### Contrato API

La API se publicara bajo `/api/v1`. Las colecciones usarán paginacion, filtros declarados y ordenacion limitada a campos permitidos. La respuesta de error tendra una forma unica:

```json
{
  "message": "Los datos enviados no son validos.",
  "errors": {
    "campo": ["Mensaje de validacion"]
  }
}
```

Las fechas se transportan en ISO 8601 UTC. La interfaz muestra importes con la moneda y zona horaria configuradas para la empresa.

## 5. Frontend React

React se organiza por funcionalidad, no por tipo tecnico global. Cada carpeta en `src/features/` puede contener pantalla, formulario, cliente API, hooks y pruebas del modulo correspondiente.

`AuthProvider` mantiene la sesion y los permisos devueltos por `GET /api/v1/me`. Un componente de ruta protegida exige autenticacion y otro exige el permiso requerido. Estas validaciones mejoran la experiencia, pero la autorizacion definitiva siempre esta en Laravel.

El cliente HTTP centraliza URL base, credenciales/token, tratamiento de `401` y normalizacion de errores. Ningun componente conoce ni manipula `empresa_id`.

## 6. Flujos transaccionales

La confirmacion de venta es el flujo critico. Se implementa en una unica Action y dentro de `DB::transaction()`:

```text
Validar pedido y pertenencia a empresa
    -> bloquear existencias requeridas
    -> validar stock disponible
    -> crear venta y sus detalles historicos
    -> crear pagos
    -> crear movimientos de salida
    -> actualizar existencias
    -> marcar pedido entregado o facturado
    -> confirmar transaccion
```

Si cualquiera de los pasos falla, se revierte toda la operacion. El bloqueo de existencias debe impedir que dos ventas simultaneas consuman el mismo inventario. Una anulacion o reembolso futuro debe crear movimientos compensatorios, nunca editar el historial existente.

## 7. Datos, estados y auditoria

- Los importes monetarios usan `numeric`, nunca `float`; las cantidades de inventario usan una precision separada.
- Detalles de pedido y venta conservan nombre y precio del producto para mantener el historial.
- Los estados se representaran con PHP Enums y se validaran sus transiciones en el dominio: `pending -> preparing -> ready -> delivered`, con `cancelled` segun reglas definidas.
- Usuarios, productos, clientes e ingredientes utilizaran eliminacion logica cuando el historial deba preservarse. Las ventas, pagos y movimientos de inventario no se eliminan.
- Las entidades operativas incluiran `created_at`, `updated_at`, `created_by` y `updated_by` cuando aplique. Las migraciones adaptaran los nombres actuales en espanol o configuraran los timestamps de Eloquent de manera coherente; no se mezclaran ambas convenciones sin una decision explicita.

## 8. Seguridad y observabilidad

- Laravel Sanctum autentica las solicitudes; contrasenas con hash seguro y limitacion de intentos de inicio de sesion.
- Los permisos se verifican en rutas y Policies; el rol Admin no se interpreta solo en el frontend.
- Las operaciones sensibles registran usuario, empresa, referencia y resultado en logs estructurados, sin contrasenas, tokens ni datos de pago sensibles.
- Variables de entorno y secretos quedan fuera de Git. Docker expone PostgreSQL solo para desarrollo local.

## 9. Pruebas y limites de calidad

Las pruebas Feature cubren autenticacion, permisos, aislamiento de empresas, contratos HTTP y flujos de pedido/venta. Las pruebas Unit cubren calculos, transiciones de estado y recetas. Antes de integrar una funcionalidad se deben cubrir, como minimo, estos escenarios:

- Un usuario de Empresa A no puede leer ni modificar recursos de Empresa B.
- Un usuario sin permiso recibe `403` al intentar una accion protegida.
- Un pedido solo acepta productos y cliente de su empresa.
- Una venta descuenta las cantidades correctas y crea el movimiento asociado.
- Un fallo al cobrar o descontar inventario no deja venta, pago ni stock parcialmente modificados.

## 10. Orden de implementacion

1. Inicializar Laravel, React, configuracion local y migraciones a partir del modelo actual.
2. Implementar autenticacion, `EmpresaContext`, roles, permisos y pruebas de aislamiento.
3. Construir catalogo, ingredientes, existencias y movimientos manuales.
4. Incorporar clientes y pedidos con transiciones de estado.
5. Implementar ventas, pagos, recetas y descuento transaccional de inventario.
6. Exponer dashboard y reportes de lectura; despues, exportaciones y capacidades SaaS globales.

Esta secuencia evita construir indicadores o automatizaciones sobre operaciones que aun no son consistentes ni estan aisladas por empresa.
