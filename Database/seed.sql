-- ==========================================================
-- DATOS INICIALES (SEED): papeleria_corresponsal
-- Compatible con MySQL 8+ y MariaDB en Laragon
-- ==========================================================

USE `papeleria_corresponsal`;

-- Desactivar temporalmente revisión de llaves foráneas para reinserción segura
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `recomendacion_ia`;
TRUNCATE TABLE `analisis_ia`;
TRUNCATE TABLE `alerta_inventario`;
TRUNCATE TABLE `operacion_corresponsal`;
TRUNCATE TABLE `transferencia`;
TRUNCATE TABLE `movimiento_cuenta`;
TRUNCATE TABLE `cuenta`;
TRUNCATE TABLE `detalle_venta`;
TRUNCATE TABLE `venta`;
TRUNCATE TABLE `cliente`;
TRUNCATE TABLE `movimiento_inventario`;
TRUNCATE TABLE `historial_precio`;
TRUNCATE TABLE `producto`;
TRUNCATE TABLE `categoria`;
TRUNCATE TABLE `usuario`;
TRUNCATE TABLE `rol`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insertar Roles
INSERT INTO `rol` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'ADMINISTRADOR', 'Administrador con acceso y control total del sistema'),
(2, 'TRABAJADOR', 'Trabajador para ventas, corresponsal y consulta de inventario');

-- 2. Insertar Usuarios Iniciales
-- Contraseña admin: 12345 (admin@papeleria.com o admin)
-- Contraseña trabajador: trabajador123
INSERT INTO `usuario` (`id_usuario`, `id_rol`, `nombre`, `apellido`, `documento`, `telefono`, `correo`, `nombre_usuario`, `contrasena`, `estado`, `fecha_registro`) VALUES
(1, 1, 'Carlos', 'Administrador', '100100200', '3001234567', 'admin@papeleria.com', 'admin', '$2y$10$i0jJpYLHqTHMKUYhXB00NeESTVfDy2hoCccYXNUPLWcNgeroM5TGe', 'ACTIVO', NOW()),
(2, 2, 'Laura', 'Vendedora', '100200300', '3109876543', 'trabajador@papeleria.com', 'trabajador', '$2y$10$RcDhBJFOwqz3gRVzdHpJLOJBSbms5sIwzoAlFLei7nDGcg.NuLvHO', 'ACTIVO', NOW());

-- 3. Insertar Cuentas Principales
INSERT INTO `cuenta` (`id_cuenta`, `tipo`, `nombre`, `saldo`, `estado`, `fecha_creacion`) VALUES
(1, 'PAPELERIA', 'Cuenta Principal Papelería', 1500000.00, 'ACTIVO', NOW()),
(2, 'CORRESPONSAL', 'Cuenta Corresponsal Bancario', 3000000.00, 'ACTIVO', NOW());

-- Registrar movimientos iniciales de apertura de cuentas
INSERT INTO `movimiento_cuenta` (`id_cuenta`, `id_usuario`, `tipo`, `concepto`, `valor`, `saldo_anterior`, `saldo_nuevo`, `fecha_movimiento`) VALUES
(1, 1, 'INGRESO', 'Saldo inicial de apertura de caja de papelería', 1500000.00, 0.00, 1500000.00, NOW()),
(2, 1, 'INGRESO', 'Fondo inicial para operaciones de corresponsal bancario', 3000000.00, 0.00, 3000000.00, NOW());

-- 4. Insertar Categorías
INSERT INTO `categoria` (`id_categoria`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Útiles escolares', 'Cuadernos, lápices, colores, borradores y reglas escolares', 'ACTIVO'),
(2, 'Papelería', 'Hojas resma, cartulinas, carpetas, sobres y papel continuo', 'ACTIVO'),
(3, 'Oficina', 'Grapadoras, perforadoras, clips, archivadores y marcadores', 'ACTIVO'),
(4, 'Arte', 'Pinturas acrílicas, pinceles, lienzos y blocs de dibujo', 'ACTIVO'),
(5, 'Tecnología', 'Memorias USB, cables USB, adaptadores, mouse y auriculares', 'ACTIVO'),
(6, 'Aseo', 'Alcohol antiséptico, gel antibacterial y toallas de papel', 'ACTIVO');

-- 5. Insertar Productos de Ejemplo
INSERT INTO `producto` (`id_producto`, `id_categoria`, `codigo`, `nombre`, `descripcion`, `precio`, `stock_actual`, `stock_minimo`, `estado`, `fecha_registro`) VALUES
(1, 1, 'ESC001', 'Cuaderno Cuadriculado 100H', 'Cuaderno cosido cuadriculado 100 hojas norma', 3500.00, 45, 10, 'ACTIVO', NOW()),
(2, 1, 'ESC002', 'Lápiz Grafito HB #2', 'Lápiz de madera Faber Castell grafito #2', 1200.00, 120, 20, 'ACTIVO', NOW()),
(3, 1, 'ESC003', 'Caja de Colores x12', 'Colores largos x12 unidades doble punta', 9800.00, 8, 10, 'ACTIVO', NOW()),
(4, 2, 'PAP001', 'Resma Papel Carta 75g', 'Resma de 500 hojas tamaño carta reprograf', 18500.00, 25, 5, 'ACTIVO', NOW()),
(5, 2, 'PAP002', 'Carpeta Plastificada Tamaño Oficio', 'Carpeta legajadora con gancho plástico', 2200.00, 60, 15, 'ACTIVO', NOW()),
(6, 3, 'OFI001', 'Caja de Grapas 26/6', 'Caja con 5000 grapas estándar 26/6', 4500.00, 18, 5, 'ACTIVO', NOW()),
(7, 3, 'OFI002', 'Perforadora Metálica 2 Huecos', 'Perforadora de escritorio capacidad 20 hojas', 14500.00, 6, 4, 'ACTIVO', NOW()),
(8, 4, 'ART001', 'Set de Pinturas Acrílicas x6', 'Tubos de acrílico colores primarios y secundarios', 16000.00, 12, 5, 'ACTIVO', NOW()),
(9, 5, 'TEC001', 'Memoria USB Kingston 32GB 3.0', 'Pendrive 32GB USB 3.0 alta velocidad', 24000.00, 3, 5, 'ACTIVO', NOW()),
(10, 5, 'TEC002', 'Cable USB Tipo C a USB-A 1m', 'Cable de carga rápida trenzado de nylon', 11000.00, 15, 6, 'ACTIVO', NOW());

-- 6. Generar Movimientos de Inventario Iniciales
INSERT INTO `movimiento_inventario` (`id_producto`, `id_usuario`, `tipo`, `cantidad`, `stock_anterior`, `stock_nuevo`, `motivo`, `fecha_movimiento`) VALUES
(1, 1, 'ENTRADA', 45, 0, 45, 'Inventario inicial de apertura', NOW()),
(2, 1, 'ENTRADA', 120, 0, 120, 'Inventario inicial de apertura', NOW()),
(3, 1, 'ENTRADA', 8, 0, 8, 'Inventario inicial de apertura', NOW()),
(4, 1, 'ENTRADA', 25, 0, 25, 'Inventario inicial de apertura', NOW()),
(5, 1, 'ENTRADA', 60, 0, 60, 'Inventario inicial de apertura', NOW()),
(6, 1, 'ENTRADA', 18, 0, 18, 'Inventario inicial de apertura', NOW()),
(7, 1, 'ENTRADA', 6, 0, 6, 'Inventario inicial de apertura', NOW()),
(8, 1, 'ENTRADA', 12, 0, 12, 'Inventario inicial de apertura', NOW()),
(9, 1, 'ENTRADA', 3, 0, 3, 'Inventario inicial de apertura', NOW()),
(10, 1, 'ENTRADA', 15, 0, 15, 'Inventario inicial de apertura', NOW());

-- 7. Generar Historial de Precios Inicial
INSERT INTO `historial_precio` (`id_producto`, `id_usuario`, `precio_anterior`, `precio_nuevo`, `fecha_cambio`) VALUES
(1, 1, NULL, 3500.00, NOW()),
(2, 1, NULL, 1200.00, NOW()),
(3, 1, NULL, 9800.00, NOW()),
(4, 1, NULL, 18500.00, NOW()),
(5, 1, NULL, 2200.00, NOW()),
(6, 1, NULL, 4500.00, NOW()),
(7, 1, NULL, 14500.00, NOW()),
(8, 1, NULL, 16000.00, NOW()),
(9, 1, NULL, 24000.00, NOW()),
(10, 1, NULL, 11000.00, NOW());

-- 8. Generar Alertas para Productos con Stock Bajo (producto 3 y 9)
INSERT INTO `alerta_inventario` (`id_producto`, `stock_actual`, `stock_minimo`, `mensaje`, `atendida`, `fecha_alerta`) VALUES
(3, 8, 10, 'Stock crítico: Caja de Colores x12 tiene 8 unidades (mínimo 10)', 0, NOW()),
(9, 3, 5, 'Stock crítico: Memoria USB Kingston 32GB 3.0 tiene 3 unidades (mínimo 5)', 0, NOW());

-- 9. Insertar Clientes Iniciales
INSERT INTO `cliente` (`id_cliente`, `tipo_identificacion`, `numero_identificacion`, `nombre`, `apellido`, `telefono`, `correo`, `direccion`, `estado`, `fecha_registro`) VALUES
(1, 'CC', '22222222', 'Cliente', 'General / Mostrador', '3000000000', 'general@papeleria.com', 'Local Comercial', 'ACTIVO', NOW()),
(2, 'CC', '1015456789', 'Juan Pablo', 'Martínez Gómez', '3157894561', 'juan.martinez@gmail.com', 'Calle 45 # 12-34', 'ACTIVO', NOW()),
(3, 'NIT', '900123456-1', 'Colegio San Francisco de Asís', 'S.A.S.', '6012345678', 'contacto@sanfrancisco.edu.co', 'Carrera 15 # 80-20', 'ACTIVO', NOW()),
(4, 'CC', '52896321', 'María Camila', 'Rodríguez Silva', '3206549871', 'mcrodriguez@outlook.com', 'Avenida Siempre Viva 742', 'ACTIVO', NOW());
