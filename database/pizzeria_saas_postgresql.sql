-- ============================================================
-- SaaS MULTIEMPRESA PARA PIZZERÍAS
-- Motor: PostgreSQL 14+
-- Modelo: tenant compartido mediante empresa_id
-- ============================================================

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ============================================================
-- 1. EMPRESAS Y SEGURIDAD
-- ============================================================

CREATE TABLE empresas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nombre VARCHAR(150) NOT NULL,
    nit VARCHAR(40) NOT NULL UNIQUE,
    direccion VARCHAR(250),
    telefono VARCHAR(40),
    correo VARCHAR(160),
    logo_url TEXT,
    moneda CHAR(3) NOT NULL DEFAULT 'COP',
    zona_horaria VARCHAR(80) NOT NULL DEFAULT 'America/Bogota',
    configuracion JSONB NOT NULL DEFAULT '{}'::jsonb,
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVA'
        CHECK (estado IN ('ACTIVA','INACTIVA','SUSPENDIDA')),
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    actualizado_en TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE usuarios (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    correo VARCHAR(160) NOT NULL,
    telefono VARCHAR(40),
    foto_url TEXT,
    password_hash TEXT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVO'
        CHECK (estado IN ('ACTIVO','INACTIVO','BLOQUEADO')),
    ultimo_acceso TIMESTAMPTZ,
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    actualizado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT uq_usuario_correo_empresa UNIQUE (empresa_id, correo)
);

CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(250),
    es_sistema BOOLEAN NOT NULL DEFAULT FALSE,
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT uq_rol_empresa UNIQUE (empresa_id, nombre)
);

CREATE TABLE permisos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    codigo VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(250)
);

CREATE TABLE usuario_roles (
    usuario_id UUID NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    rol_id UUID NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (usuario_id, rol_id)
);

CREATE TABLE rol_permisos (
    rol_id UUID NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permiso_id UUID NOT NULL REFERENCES permisos(id) ON DELETE CASCADE,
    PRIMARY KEY (rol_id, permiso_id)
);

-- ============================================================
-- 2. CLIENTES
-- ============================================================

CREATE TABLE clientes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    nombre VARCHAR(120) NOT NULL,
    apellido VARCHAR(120),
    telefono VARCHAR(40),
    correo VARCHAR(160),
    direccion VARCHAR(250),
    notas TEXT,
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVO'
        CHECK (estado IN ('ACTIVO','INACTIVO')),
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    actualizado_en TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_clientes_empresa_telefono ON clientes(empresa_id, telefono);
CREATE INDEX idx_clientes_empresa_correo ON clientes(empresa_id, correo);

-- ============================================================
-- 3. CARTA / PRODUCTOS
-- ============================================================

CREATE TABLE categorias_producto (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(250),
    orden_visual INT NOT NULL DEFAULT 0,
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVA'
        CHECK (estado IN ('ACTIVA','INACTIVA')),
    CONSTRAINT uq_categoria_empresa UNIQUE (empresa_id, nombre)
);

CREATE TABLE productos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    categoria_id UUID REFERENCES categorias_producto(id) ON DELETE SET NULL,
    sku VARCHAR(60),
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio NUMERIC(14,2) NOT NULL CHECK (precio >= 0),
    costo_estimado NUMERIC(14,2) CHECK (costo_estimado IS NULL OR costo_estimado >= 0),
    imagen_url TEXT,
    controla_inventario BOOLEAN NOT NULL DEFAULT TRUE,
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVO'
        CHECK (estado IN ('ACTIVO','INACTIVO','AGOTADO')),
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    actualizado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT uq_producto_sku_empresa UNIQUE (empresa_id, sku)
);

CREATE INDEX idx_productos_empresa_categoria ON productos(empresa_id, categoria_id);
CREATE INDEX idx_productos_empresa_estado ON productos(empresa_id, estado);

-- ============================================================
-- 4. PROVEEDORES E INVENTARIO
-- ============================================================

CREATE TABLE proveedores (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    nombre VARCHAR(150) NOT NULL,
    nit VARCHAR(40),
    contacto VARCHAR(120),
    telefono VARCHAR(40),
    correo VARCHAR(160),
    direccion VARCHAR(250),
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVO'
        CHECK (estado IN ('ACTIVO','INACTIVO')),
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE ingredientes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    proveedor_id UUID REFERENCES proveedores(id) ON DELETE SET NULL,
    codigo VARCHAR(60),
    nombre VARCHAR(150) NOT NULL,
    unidad_medida VARCHAR(20) NOT NULL,
    stock_minimo NUMERIC(14,3) NOT NULL DEFAULT 0 CHECK (stock_minimo >= 0),
    costo_unitario NUMERIC(14,4) NOT NULL DEFAULT 0 CHECK (costo_unitario >= 0),
    estado VARCHAR(20) NOT NULL DEFAULT 'ACTIVO'
        CHECK (estado IN ('ACTIVO','INACTIVO')),
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    actualizado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT uq_ingrediente_codigo_empresa UNIQUE (empresa_id, codigo)
);

-- Existencia actual separada del historial de movimientos.
CREATE TABLE inventario_existencias (
    ingrediente_id UUID PRIMARY KEY REFERENCES ingredientes(id) ON DELETE CASCADE,
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    cantidad NUMERIC(14,3) NOT NULL DEFAULT 0,
    actualizado_en TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE movimientos_inventario (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    ingrediente_id UUID NOT NULL REFERENCES ingredientes(id) ON DELETE RESTRICT,
    usuario_id UUID REFERENCES usuarios(id) ON DELETE SET NULL,
    tipo VARCHAR(20) NOT NULL
        CHECK (tipo IN ('ENTRADA','SALIDA','AJUSTE_POSITIVO','AJUSTE_NEGATIVO')),
    cantidad NUMERIC(14,3) NOT NULL CHECK (cantidad > 0),
    costo_unitario NUMERIC(14,4) CHECK (costo_unitario IS NULL OR costo_unitario >= 0),
    motivo VARCHAR(250),
    referencia_tipo VARCHAR(40),
    referencia_id UUID,
    fecha TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_mov_inv_empresa_fecha ON movimientos_inventario(empresa_id, fecha DESC);
CREATE INDEX idx_mov_inv_ingrediente_fecha ON movimientos_inventario(ingrediente_id, fecha DESC);

-- Receta: cuánta materia prima consume una unidad del producto.
CREATE TABLE producto_ingredientes (
    producto_id UUID NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    ingrediente_id UUID NOT NULL REFERENCES ingredientes(id) ON DELETE RESTRICT,
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    cantidad NUMERIC(14,3) NOT NULL CHECK (cantidad > 0),
    PRIMARY KEY (producto_id, ingrediente_id)
);

-- ============================================================
-- 5. PEDIDOS
-- ============================================================

CREATE TABLE pedidos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    numero BIGINT GENERATED BY DEFAULT AS IDENTITY,
    cliente_id UUID REFERENCES clientes(id) ON DELETE SET NULL,
    usuario_id UUID REFERENCES usuarios(id) ON DELETE SET NULL,
    tipo VARCHAR(20) NOT NULL DEFAULT 'MOSTRADOR'
        CHECK (tipo IN ('MOSTRADOR','DOMICILIO','RECOGER','MESA')),
    estado VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE'
        CHECK (estado IN ('PENDIENTE','EN_PREPARACION','LISTO','ENTREGADO','CANCELADO')),
    direccion_entrega VARCHAR(250),
    observaciones TEXT,
    subtotal NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (subtotal >= 0),
    descuento NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (descuento >= 0),
    impuesto NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (impuesto >= 0),
    total NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (total >= 0),
    creado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    actualizado_en TIMESTAMPTZ NOT NULL DEFAULT now(),
    entregado_en TIMESTAMPTZ,
    CONSTRAINT uq_pedido_numero_empresa UNIQUE (empresa_id, numero)
);

CREATE TABLE pedido_detalles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pedido_id UUID NOT NULL REFERENCES pedidos(id) ON DELETE CASCADE,
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    producto_id UUID REFERENCES productos(id) ON DELETE SET NULL,
    producto_nombre VARCHAR(150) NOT NULL,
    cantidad NUMERIC(12,3) NOT NULL CHECK (cantidad > 0),
    precio_unitario NUMERIC(14,2) NOT NULL CHECK (precio_unitario >= 0),
    descuento NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (descuento >= 0),
    subtotal NUMERIC(14,2) NOT NULL CHECK (subtotal >= 0),
    observaciones VARCHAR(300)
);

CREATE INDEX idx_pedidos_empresa_estado ON pedidos(empresa_id, estado);
CREATE INDEX idx_pedidos_empresa_fecha ON pedidos(empresa_id, creado_en DESC);

-- ============================================================
-- 6. VENTAS Y PAGOS
-- ============================================================

CREATE TABLE ventas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    numero BIGINT GENERATED BY DEFAULT AS IDENTITY,
    pedido_id UUID REFERENCES pedidos(id) ON DELETE SET NULL,
    cliente_id UUID REFERENCES clientes(id) ON DELETE SET NULL,
    usuario_id UUID REFERENCES usuarios(id) ON DELETE SET NULL,
    subtotal NUMERIC(14,2) NOT NULL CHECK (subtotal >= 0),
    descuento NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (descuento >= 0),
    impuesto NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (impuesto >= 0),
    total NUMERIC(14,2) NOT NULL CHECK (total >= 0),
    estado VARCHAR(20) NOT NULL DEFAULT 'PAGADA'
        CHECK (estado IN ('PENDIENTE','PAGADA','ANULADA','REEMBOLSADA')),
    fecha TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT uq_venta_numero_empresa UNIQUE (empresa_id, numero),
    CONSTRAINT uq_venta_pedido UNIQUE (pedido_id)
);

CREATE TABLE venta_detalles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    venta_id UUID NOT NULL REFERENCES ventas(id) ON DELETE CASCADE,
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    producto_id UUID REFERENCES productos(id) ON DELETE SET NULL,
    producto_nombre VARCHAR(150) NOT NULL,
    cantidad NUMERIC(12,3) NOT NULL CHECK (cantidad > 0),
    precio_unitario NUMERIC(14,2) NOT NULL CHECK (precio_unitario >= 0),
    descuento NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (descuento >= 0),
    subtotal NUMERIC(14,2) NOT NULL CHECK (subtotal >= 0)
);

CREATE TABLE pagos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    empresa_id UUID NOT NULL REFERENCES empresas(id) ON DELETE CASCADE,
    venta_id UUID NOT NULL REFERENCES ventas(id) ON DELETE CASCADE,
    metodo VARCHAR(30) NOT NULL
        CHECK (metodo IN ('EFECTIVO','TARJETA','TRANSFERENCIA','OTRO')),
    monto NUMERIC(14,2) NOT NULL CHECK (monto > 0),
    referencia VARCHAR(120),
    fecha TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_ventas_empresa_fecha ON ventas(empresa_id, fecha DESC);
CREATE INDEX idx_ventas_empresa_estado ON ventas(empresa_id, estado);
CREATE INDEX idx_pagos_venta ON pagos(venta_id);

-- ============================================================
-- 7. VISTAS PARA DASHBOARD / REPORTES
-- ============================================================

CREATE OR REPLACE VIEW vw_ventas_diarias AS
SELECT
    empresa_id,
    (fecha AT TIME ZONE 'America/Bogota')::date AS fecha,
    COUNT(*) FILTER (WHERE estado = 'PAGADA') AS cantidad_ventas,
    COALESCE(SUM(total) FILTER (WHERE estado = 'PAGADA'), 0) AS total_ventas
FROM ventas
GROUP BY empresa_id, (fecha AT TIME ZONE 'America/Bogota')::date;

CREATE OR REPLACE VIEW vw_productos_mas_vendidos AS
SELECT
    vd.empresa_id,
    vd.producto_id,
    vd.producto_nombre,
    SUM(vd.cantidad) AS unidades_vendidas,
    SUM(vd.subtotal) AS ingresos
FROM venta_detalles vd
JOIN ventas v ON v.id = vd.venta_id
WHERE v.estado = 'PAGADA'
GROUP BY vd.empresa_id, vd.producto_id, vd.producto_nombre;

CREATE OR REPLACE VIEW vw_stock_bajo AS
SELECT
    i.empresa_id,
    i.id AS ingrediente_id,
    i.nombre,
    i.unidad_medida,
    COALESCE(e.cantidad, 0) AS cantidad_actual,
    i.stock_minimo
FROM ingredientes i
LEFT JOIN inventario_existencias e ON e.ingrediente_id = i.id
WHERE i.estado = 'ACTIVO'
  AND COALESCE(e.cantidad, 0) <= i.stock_minimo;

CREATE OR REPLACE VIEW vw_ventas_por_metodo_pago AS
SELECT
    p.empresa_id,
    p.metodo,
    COUNT(*) AS cantidad_pagos,
    SUM(p.monto) AS total
FROM pagos p
JOIN ventas v ON v.id = p.venta_id
WHERE v.estado = 'PAGADA'
GROUP BY p.empresa_id, p.metodo;

-- ============================================================
-- 8. ÍNDICES DE TENANT / CONSULTAS FRECUENTES
-- ============================================================

CREATE INDEX idx_usuarios_empresa ON usuarios(empresa_id);
CREATE INDEX idx_roles_empresa ON roles(empresa_id);
CREATE INDEX idx_clientes_empresa ON clientes(empresa_id);
CREATE INDEX idx_categorias_empresa ON categorias_producto(empresa_id);
CREATE INDEX idx_ingredientes_empresa ON ingredientes(empresa_id);
CREATE INDEX idx_existencias_empresa ON inventario_existencias(empresa_id);
CREATE INDEX idx_producto_ingredientes_empresa ON producto_ingredientes(empresa_id);

-- ============================================================
-- 9. DATOS BASE DE PERMISOS (EJEMPLO)
-- ============================================================

INSERT INTO permisos (codigo, descripcion) VALUES
('usuarios.ver', 'Consultar usuarios'),
('usuarios.gestionar', 'Crear, editar y desactivar usuarios'),
('productos.ver', 'Consultar productos y carta'),
('productos.gestionar', 'Crear y editar productos'),
('inventario.ver', 'Consultar inventario'),
('inventario.gestionar', 'Registrar movimientos y gestionar insumos'),
('pedidos.ver', 'Consultar pedidos'),
('pedidos.gestionar', 'Crear y actualizar pedidos'),
('ventas.ver', 'Consultar ventas'),
('ventas.registrar', 'Registrar ventas y pagos'),
('reportes.ver', 'Consultar reportes'),
('empresa.configurar', 'Modificar configuración de empresa')
ON CONFLICT (codigo) DO NOTHING;    

-- ============================================================
-- NOTAS DE IMPLEMENTACIÓN
-- ============================================================
-- 1) Todas las consultas del backend deben filtrarse por empresa_id.
-- 2) empresa_id NO debe recibirse libremente desde el frontend: debe salir
--    del usuario autenticado / token / sesión.
-- 3) Laravel puede aplicar un Global Scope por empresa_id.
-- 4) Al confirmar una venta, una transacción de BD debería:
--      a. crear la venta y sus detalles;
--      b. registrar pagos;
--      c. descontar ingredientes según producto_ingredientes;
--      d. crear movimientos_inventario tipo SALIDA;
--      e. actualizar inventario_existencias;
--      f. confirmar COMMIT o hacer ROLLBACK completo.
-- 5) Para aislamiento adicional a nivel PostgreSQL puede habilitarse RLS.
