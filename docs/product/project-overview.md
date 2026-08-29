# SaaS Multiempresa para Gestión de Pizzerías

## 1. Visión general

Este proyecto consiste en desarrollar una plataforma web **SaaS multiempresa** orientada a la gestión integral de pizzerías. La aplicación permitirá que múltiples empresas utilicen el mismo sistema sin compartir información entre sí.

Cada empresa tendrá sus propios usuarios, roles, permisos, productos, categorías, ingredientes, inventario, clientes, pedidos, ventas, pagos, reportes y configuración.

El objetivo es construir una solución empresarial completa que pueda evolucionar desde un proyecto de portafolio hasta una aplicación SaaS real.

## 2. Objetivos

El sistema debe permitir:

- Registrar y administrar empresas.
- Administrar usuarios por empresa.
- Controlar permisos según el rol.
- Gestionar la carta de productos.
- Gestionar recetas e ingredientes.
- Controlar inventario.
- Registrar pedidos.
- Controlar estados de preparación.
- Registrar ventas y pagos.
- Administrar clientes.
- Consultar métricas.
- Generar reportes.
- Detectar inventario bajo.
- Mantener separados los datos de cada empresa.

## 3. Stack tecnológico

### Frontend
- React.
- React Router.
- Axios o Fetch.
- Librería de gráficos para dashboards.

### Backend
- Laravel.
- API REST.
- Laravel Sanctum para autenticación.
- Policies, Gates o middleware para autorización.
- Form Requests para validación.
- Services o Actions para lógica de negocio compleja.

### Base de datos
- PostgreSQL.

### Infraestructura local
- Docker.
- Docker Compose.

### Control de versiones
- Git.
- GitHub.
- GitHub Desktop.

## 4. Arquitectura general

```text
Usuario
   │
   ▼
React
   │ HTTP / JSON
   ▼
Laravel API
   │
   ├── Autenticación
   ├── Autorización
   ├── Reglas de negocio
   └── Servicios
   │
   ▼
PostgreSQL
```

Arquitectura local:

```text
PC
│
├── apps/web/
│   └── React
├── apps/api/
│   └── Laravel
└── Docker
    └── PostgreSQL
```

## 5. Concepto multiempresa

El sistema debe funcionar como una arquitectura **multi-tenant**.

```text
Sistema
│
├── Empresa A
│   ├── Usuarios
│   ├── Clientes
│   ├── Productos
│   ├── Inventario
│   ├── Pedidos
│   └── Ventas
├── Empresa B
│   ├── Usuarios
│   ├── Clientes
│   ├── Productos
│   ├── Inventario
│   ├── Pedidos
│   └── Ventas
└── Empresa C
    ├── Usuarios
    ├── Clientes
    ├── Productos
    ├── Inventario
    ├── Pedidos
    └── Ventas
```

Cada registro operativo debe estar relacionado con una empresa mediante `empresa_id`.

Ejemplos:

```text
productos.empresa_id
clientes.empresa_id
pedidos.empresa_id
ventas.empresa_id
ingredientes.empresa_id
```

El backend debe obtener la empresa desde el usuario autenticado. Nunca se debe confiar en un `empresa_id` enviado libremente desde el frontend.

## 6. Empresas

Cada empresa representa una pizzería que utiliza el sistema.

Datos principales:
- ID.
- Nombre.
- NIT.
- Dirección.
- Teléfono.
- Correo.
- Logo.
- Estado.
- Configuración.
- Fecha de creación.

## 7. Usuarios

Una empresa puede tener múltiples usuarios.

Ejemplo:

```text
Pizzería La 85
│
├── Administrador
├── Cajero
├── Mesero
└── Encargado de inventario
```

Cada usuario debe contener nombre, apellido, correo, teléfono, foto, contraseña, estado, empresa y roles. El usuario debe poder modificar su propio perfil.

## 8. Roles y permisos

Se recomienda modelar:

```text
usuarios
   │
   ▼
usuario_roles
   │
   ▼
roles
   │
   ▼
rol_permisos
   │
   ▼
permisos
```

Roles iniciales:

### Administrador
- Gestionar usuarios.
- Gestionar roles.
- Gestionar productos.
- Gestionar categorías.
- Gestionar inventario.
- Consultar ventas.
- Consultar reportes.
- Gestionar clientes.
- Configurar empresa.

### Cajero
- Crear ventas.
- Consultar productos.
- Consultar pedidos.
- Registrar pagos.

### Mesero
- Crear pedidos.
- Modificar pedidos.
- Consultar estados.

### Inventario
- Gestionar ingredientes.
- Registrar entradas.
- Registrar salidas.
- Registrar ajustes.
- Consultar existencias.

## 9. Productos y carta

### Categorías
- Pizzas.
- Bebidas.
- Entradas.
- Postres.
- Combos.

### Productos
Un producto representa algo que puede venderse.

Campos sugeridos:
- Nombre.
- Descripción.
- Precio.
- Categoría.
- Imagen.
- Estado.
- Empresa.

## 10. Ingredientes

Ejemplos:
- Harina.
- Mozzarella.
- Jamón.
- Piña.
- Salsa.
- Pepperoni.

Campos sugeridos:
- Nombre.
- Unidad de medida.
- Stock actual.
- Stock mínimo.
- Costo promedio.
- Proveedor.
- Estado.
- Empresa.

## 11. Recetas

Un producto puede utilizar múltiples ingredientes y un ingrediente puede utilizarse en múltiples productos.

```text
PRODUCTOS
   │ N:M
   ▼
PRODUCTO_INGREDIENTES
   ▲
   │
INGREDIENTES
```

Ejemplo:

```text
Pizza Hawaiana
│
├── 250 g Masa
├── 150 g Mozzarella
├── 80 g Jamón
├── 70 g Piña
└── 60 ml Salsa
```

La tabla intermedia debe almacenar la cantidad requerida de cada ingrediente.

## 12. Inventario

El inventario debe manejar existencias y movimientos.

Tipos de movimientos:
- Entrada.
- Salida.
- Ajuste.

Cada movimiento debe registrar ingrediente, empresa, tipo, cantidad, stock anterior, stock posterior, motivo, usuario responsable y fecha.

## 13. Descuento automático de inventario

Cuando una venta se complete, el backend debe consultar las recetas de los productos vendidos.

Ejemplo para 2 pizzas hawaianas:

```text
Masa        -500 g
Mozzarella  -300 g
Jamón       -160 g
Piña        -140 g
Salsa       -120 ml
```

El sistema debe:
1. Consultar la receta.
2. Calcular cantidades.
3. Validar existencias.
4. Registrar movimientos.
5. Actualizar stock.

Todo debe ejecutarse dentro de una transacción de base de datos.

## 14. Clientes

Campos:
- Nombre.
- Teléfono.
- Correo.
- Dirección.
- Empresa.
- Estado.
- Fecha de creación.

Debe ser posible consultar historial de pedidos, historial de compras, total gastado y última compra.

## 15. Pedidos

Un pedido representa el proceso operativo de preparación.

Estados:

```text
Pendiente
   ↓
En preparación
   ↓
Listo
   ↓
Entregado
```

También puede existir `Cancelado`.

Cada pedido debe registrar empresa, cliente, usuario, estado, subtotal, descuento, total y fecha.

## 16. Detalle de pedido

Cada pedido puede tener múltiples productos.

Cada detalle debe guardar:
- Producto.
- Cantidad.
- Precio unitario.
- Subtotal.
- Observaciones.

## 17. Pedido vs venta

**Pedido:** representa la operación de cocina.

**Venta:** representa la transacción comercial y financiera.

Un pedido puede generar una venta.

## 18. Ventas

Cada venta debe registrar:
- Empresa.
- Cliente.
- Usuario responsable.
- Pedido relacionado.
- Subtotal.
- Descuento.
- Impuestos.
- Total.
- Fecha.
- Estado.

## 19. Detalle de venta

Cada detalle almacena producto, cantidad, precio unitario, descuento y subtotal. El precio debe guardarse en el detalle para preservar el valor histórico incluso si el producto cambia de precio después.

## 20. Pagos

Una venta puede tener uno o varios pagos.

Ejemplo:

```text
Venta: $100.000
├── Efectivo: $40.000
└── Tarjeta:  $60.000
```

Métodos iniciales:
- Efectivo.
- Tarjeta.
- Transferencia.
- Otros.

## 21. Dashboard

Métricas iniciales:
- Ventas del día.
- Ventas de la semana.
- Ventas del mes.
- Número de pedidos.
- Ticket promedio.
- Productos más vendidos.
- Métodos de pago.
- Stock bajo.
- Ingresos por periodo.

Filtros:
- Hoy.
- Semana.
- Mes.
- Rango personalizado.

## 22. Reportes

No es necesario crear una tabla `reportes`. Los reportes se generan desde los datos existentes.

Reportes iniciales:
- Ventas por día.
- Ventas por mes.
- Ventas por usuario.
- Productos más vendidos.
- Productos menos vendidos.
- Métodos de pago.
- Inventario bajo.
- Movimientos de inventario.
- Clientes con más compras.
- Ingresos por rango.

Exportaciones futuras:
- PDF.
- Excel.
- CSV.

## 23. Flujo principal

```text
Usuario inicia sesión
        ↓
Sistema identifica empresa
        ↓
Usuario crea pedido
        ↓
Agrega productos
        ↓
Pedido pasa a preparación
        ↓
Pedido está listo
        ↓
Cliente paga
        ↓
Se genera venta
        ↓
Se registran pagos
        ↓
Se consultan recetas
        ↓
Se descuentan ingredientes
        ↓
Se registran movimientos
        ↓
Se actualiza inventario
        ↓
Dashboard y reportes
```

## 24. Seguridad

### Autenticación
- Correo.
- Contraseña.
- Laravel Sanctum.

### Autorización
Cada endpoint debe validar:
1. Usuario autenticado.
2. Empresa del usuario.
3. Permiso necesario.

## 25. Seguridad multiempresa

Regla crítica: un usuario nunca debe acceder a registros de otra empresa.

Incorrecto:

```text
GET /api/productos?empresa_id=2
```

Correcto:

```text
Usuario autenticado
        ↓
empresa_id obtenido en backend
        ↓
consulta filtrada por empresa_id
```

## 26. API REST sugerida

### Autenticación
```text
POST /api/login
POST /api/logout
GET  /api/me
```

### Empresa
```text
GET /api/empresa
PUT /api/empresa
```

### Usuarios
```text
GET    /api/usuarios
POST   /api/usuarios
GET    /api/usuarios/{id}
PUT    /api/usuarios/{id}
DELETE /api/usuarios/{id}
```

### Productos
```text
GET    /api/productos
POST   /api/productos
GET    /api/productos/{id}
PUT    /api/productos/{id}
DELETE /api/productos/{id}
```

### Categorías
```text
GET    /api/categorias
POST   /api/categorias
PUT    /api/categorias/{id}
DELETE /api/categorias/{id}
```

### Ingredientes
```text
GET    /api/ingredientes
POST   /api/ingredientes
PUT    /api/ingredientes/{id}
DELETE /api/ingredientes/{id}
```

### Inventario
```text
GET  /api/inventario
POST /api/inventario/entrada
POST /api/inventario/salida
POST /api/inventario/ajuste
GET  /api/inventario/movimientos
```

### Clientes
```text
GET    /api/clientes
POST   /api/clientes
GET    /api/clientes/{id}
PUT    /api/clientes/{id}
DELETE /api/clientes/{id}
```

### Pedidos
```text
GET   /api/pedidos
POST  /api/pedidos
GET   /api/pedidos/{id}
PUT   /api/pedidos/{id}
PATCH /api/pedidos/{id}/estado
```

### Ventas
```text
GET  /api/ventas
POST /api/ventas
GET  /api/ventas/{id}
```

### Dashboard y reportes
```text
GET /api/dashboard
GET /api/reportes/ventas
GET /api/reportes/productos
GET /api/reportes/inventario
GET /api/reportes/clientes
```

## 27. Estructura recomendada del repositorio

```text
pizzeria-saas/
│
├── apps/
│   ├── api/
│   │   └── Laravel
│   └── web/
│       └── React
├── database/
│   ├── snapshots/pizzeria_saas_postgresql.sql
│   └── diagrams/
├── docs/
│   ├── product/project-overview.md
│   ├── architecture/overview.md
│   └── database/
├── infra/docker/
├── compose.yaml
├── .gitignore
└── README.md
```

## 28. Backend Laravel

Estructura sugerida:

```text
app/
│
├── Models/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Services/
├── Policies/
└── Actions/
```

Evitar colocar toda la lógica de negocio dentro de los controladores.

## 29. Migraciones Laravel

Aunque existe un SQL inicial, la versión definitiva debe manejarse mediante migraciones Laravel.

La base debe poder reconstruirse con:

```bash
php artisan migrate
```

## 30. Seeders

Crear datos iniciales para roles y permisos.

Roles:
- Admin.
- Cajero.
- Mesero.
- Inventario.

Ejemplos de permisos:

```text
usuarios.ver
usuarios.crear
usuarios.editar
usuarios.eliminar
productos.ver
productos.crear
productos.editar
productos.eliminar
ventas.ver
ventas.crear
inventario.ver
inventario.gestionar
reportes.ver
```

## 31. Docker

Inicialmente:

```text
Docker Compose
└── PostgreSQL
```

En el futuro:

```text
Docker Compose
├── PostgreSQL
├── Laravel
├── React
└── Redis
```

## 32. Variables de entorno

Ejemplo local:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pizzeria_db
DB_USERNAME=pizzeria_user
DB_PASSWORD=pizzeria_password
```

`.env` nunca debe subirse al repositorio.

## 33. Reglas de negocio importantes

- Todo registro operativo pertenece a una empresa.
- Un usuario pertenece a una empresa.
- Un producto pertenece a la empresa autenticada.
- Un pedido no puede contener productos de otra empresa.
- Una venta debe pertenecer a la misma empresa que el pedido.
- Un movimiento no puede usar ingredientes de otra empresa.
- Una receta solo puede relacionar productos e ingredientes de la misma empresa.

## 34. Transacciones

Operaciones críticas deben ejecutarse en una transacción.

Ejemplo al confirmar venta:

```text
BEGIN
│
├── crear venta
├── crear detalle
├── crear pagos
├── descontar inventario
├── crear movimientos
└── actualizar pedido
│
COMMIT
```

Si algo falla: `ROLLBACK`.

## 35. Auditoría y eliminación lógica

Campos recomendados:

```text
created_at
updated_at
```

Cuando corresponda:

```text
created_by
updated_by
```

Se recomienda `softDeletes` para usuarios, productos, clientes e ingredientes cuando sea necesario conservar historial.

## 36. Estados

Pedido:

```text
pending
preparing
ready
delivered
cancelled
```

Producto:

```text
active
inactive
```

Usuario:

```text
active
inactive
blocked
```

## 37. MVP

La primera versión funcional debe incluir:

### Autenticación
- Login.
- Logout.
- Usuario autenticado.

### Multiempresa
- Empresa asociada al usuario.
- Filtrado obligatorio por empresa.

### Usuarios
- CRUD.
- Roles básicos.

### Productos
- Categorías.
- Productos.

### Ingredientes e inventario
- CRUD de ingredientes.
- Existencias.
- Movimientos.

### Clientes
- CRUD.

### Pedidos
- Crear.
- Editar.
- Cambiar estado.

### Ventas
- Crear venta.
- Registrar pagos.

### Dashboard
- Ventas del día.
- Pedidos del día.
- Producto más vendido.
- Stock bajo.

## 38. Segunda etapa

- Recetas.
- Descuento automático de inventario.
- Múltiples métodos de pago.
- Reportes avanzados.
- Exportación Excel.
- Exportación PDF.
- Historial de clientes.
- Configuración empresarial.

## 39. Tercera etapa SaaS

- Registro de empresas.
- Planes.
- Suscripciones.
- Límites por plan.
- Facturación.
- Periodo de prueba.
- Suspensión automática.
- Panel de administración global.

## 40. Inteligencia artificial

La IA se plantea como una etapa posterior basada en los datos reales de cada empresa.

Ejemplo:

```text
La Pizza Hawaiana representa el 32% de las ventas durante los fines de semana.
Se recomienda aumentar el inventario de jamón, piña y mozzarella los viernes y sábados.
```

Posibles funciones:
- Predicción de demanda.
- Recomendaciones de inventario.
- Análisis de productos.
- Detección de productos poco rentables.
- Resumen automático de ventas.
- Recomendaciones comerciales.

## 41. Panel SaaS global

En una etapa futura:

```text
Superadministrador SaaS
│
├── Empresas
├── Planes
├── Suscripciones
├── Uso
├── Facturación
└── Estado del sistema
```

## 42. Roadmap sugerido

1. **Infraestructura:** Git, Docker, PostgreSQL, Laravel y React.
2. **Autenticación y multiempresa:** empresas, usuarios, roles y permisos.
3. **Catálogo:** categorías y productos.
4. **Inventario:** ingredientes, recetas, existencias y movimientos.
5. **Operación:** clientes, pedidos y estados.
6. **Ventas:** venta, detalle, pagos y descuento de inventario.
7. **Dashboard:** KPIs, gráficos y filtros.
8. **Reportes:** PDF, Excel y CSV.
9. **SaaS:** planes, suscripciones y facturación.
10. **IA:** análisis, predicciones y recomendaciones.

## 43. Criterios de calidad

- Separación entre `apps/web` y `apps/api`.
- API REST consistente.
- Validación de entrada.
- Manejo centralizado de errores.
- Autenticación segura.
- Autorización por permisos.
- Multi-tenancy obligatorio.
- Transacciones para operaciones críticas.
- Migraciones versionadas.
- Seeders.
- Tests.
- Soft deletes cuando corresponda.
- Índices de base de datos.
- Logs.
- Variables sensibles fuera del repositorio.

## 44. Testing

Casos críticos:

```text
Usuario de Empresa A no puede consultar Empresa B.
Usuario sin permiso no puede crear producto.
Venta descuenta correctamente inventario.
Venta fallida revierte cambios.
Pedido solo utiliza productos de la misma empresa.
```

## 45. Resultado final esperado

El proyecto debe permitir que cada pizzería administre:

```text
Empresa
│
├── Usuarios
├── Roles
├── Productos
├── Carta
├── Ingredientes
├── Inventario
├── Clientes
├── Pedidos
├── Ventas
├── Pagos
├── Dashboard
└── Reportes
```

Mientras múltiples empresas utilizan simultáneamente la misma aplicación sin poder acceder a información ajena.

## 46. Meta profesional

El proyecto permitirá demostrar conocimiento práctico en:

- React.
- Laravel.
- APIs REST.
- PostgreSQL.
- Relaciones SQL.
- Autenticación.
- Roles y permisos.
- Multi-tenancy.
- Docker.
- Git/GitHub.
- Arquitectura de software.
- Inventario.
- Pedidos.
- Ventas.
- Reportes.
- Analítica.
- Evolución hacia IA.

## 47. Principio central

> Toda operación empresarial debe ejecutarse en el contexto de la empresa del usuario autenticado.

Este principio debe respetarse en todas las capas:

```text
Frontend
↓
API
↓
Servicios
↓
Modelos
↓
Consultas SQL
↓
Base de datos
```

El sistema debe diseñarse desde el inicio pensando en que múltiples pizzerías utilizarán la misma plataforma de forma segura y completamente aislada.
