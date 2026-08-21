CREATE TABLE `rol` (
  `id_rol` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` ENUM ('ADMINISTRADOR', 'TRABAJADOR') UNIQUE NOT NULL,
  `descripcion` varchar(200)
);

CREATE TABLE `usuario` (
  `id_usuario` int PRIMARY KEY AUTO_INCREMENT,
  `id_rol` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `documento` varchar(30) UNIQUE NOT NULL,
  `telefono` varchar(20),
  `correo` varchar(150) UNIQUE,
  `nombre_usuario` varchar(50) UNIQUE NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `estado` ENUM ('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_registro` datetime NOT NULL,
  `ultimo_acceso` datetime
);

CREATE TABLE `categoria` (
  `id_categoria` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) UNIQUE NOT NULL,
  `descripcion` varchar(255),
  `estado` ENUM ('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO'
);

CREATE TABLE `producto` (
  `id_producto` int PRIMARY KEY AUTO_INCREMENT,
  `id_categoria` int NOT NULL,
  `codigo` varchar(50) UNIQUE NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255),
  `precio` decimal(12,2) NOT NULL,
  `stock_actual` int NOT NULL DEFAULT 0,
  `stock_minimo` int NOT NULL DEFAULT 0,
  `estado` ENUM ('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_registro` datetime NOT NULL,
  `fecha_actualizacion` datetime
);

CREATE TABLE `historial_precio` (
  `id_historial_precio` int PRIMARY KEY AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  `precio_anterior` decimal(12,2),
  `precio_nuevo` decimal(12,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL
);

CREATE TABLE `movimiento_inventario` (
  `id_movimiento_inventario` int PRIMARY KEY AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_usuario` int NOT NULL,
  `tipo` ENUM ('ENTRADA', 'SALIDA', 'AJUSTE') NOT NULL,
  `cantidad` int NOT NULL,
  `stock_anterior` int NOT NULL,
  `stock_nuevo` int NOT NULL,
  `motivo` varchar(255),
  `fecha_movimiento` datetime NOT NULL
);

CREATE TABLE `cliente` (
  `id_cliente` int PRIMARY KEY AUTO_INCREMENT,
  `tipo_identificacion` ENUM ('CC', 'CE', 'NIT', 'TI', 'PASAPORTE') NOT NULL,
  `numero_identificacion` varchar(30) UNIQUE NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100),
  `telefono` varchar(20),
  `correo` varchar(150),
  `direccion` varchar(200),
  `fecha_registro` datetime NOT NULL,
  `estado` ENUM ('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO'
);

CREATE TABLE `venta` (
  `id_venta` int PRIMARY KEY AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_cliente` int,
  `fecha_venta` datetime NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `estado` ENUM ('COMPLETADA', 'CANCELADA') NOT NULL DEFAULT 'COMPLETADA'
);

CREATE TABLE `detalle_venta` (
  `id_detalle_venta` int PRIMARY KEY AUTO_INCREMENT,
  `id_venta` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
);

CREATE TABLE `cuenta` (
  `id_cuenta` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` ENUM ('PAPELERIA', 'CORRESPONSAL') UNIQUE NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `saldo` decimal(14,2) NOT NULL DEFAULT 0,
  `estado` ENUM ('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `fecha_creacion` datetime NOT NULL
);

CREATE TABLE `movimiento_cuenta` (
  `id_movimiento_cuenta` int PRIMARY KEY AUTO_INCREMENT,
  `id_cuenta` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_venta` int,
  `tipo` ENUM ('INGRESO', 'EGRESO', 'DEPOSITO', 'RETIRO') NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `valor` decimal(14,2) NOT NULL,
  `saldo_anterior` decimal(14,2) NOT NULL,
  `saldo_nuevo` decimal(14,2) NOT NULL,
  `fecha_movimiento` datetime NOT NULL
);

CREATE TABLE `transferencia` (
  `id_transferencia` int PRIMARY KEY AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_cuenta_origen` int NOT NULL,
  `id_cuenta_destino` int NOT NULL,
  `valor` decimal(14,2) NOT NULL,
  `concepto` varchar(255),
  `fecha_transferencia` datetime NOT NULL
);

CREATE TABLE `operacion_corresponsal` (
  `id_operacion` int PRIMARY KEY AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_cliente` int,
  `id_cuenta` int NOT NULL,
  `tipo` ENUM ('INGRESO', 'EGRESO', 'DEPOSITO', 'RETIRO') NOT NULL,
  `valor` decimal(14,2) NOT NULL,
  `referencia` varchar(100),
  `descripcion` varchar(255),
  `fecha_operacion` datetime NOT NULL
);

CREATE TABLE `alerta_inventario` (
  `id_alerta` int PRIMARY KEY AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `fecha_alerta` datetime NOT NULL,
  `stock_actual` int NOT NULL,
  `stock_minimo` int NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `atendida` boolean NOT NULL DEFAULT false
);

CREATE TABLE `analisis_ia` (
  `id_analisis` int PRIMARY KEY AUTO_INCREMENT,
  `id_usuario` int,
  `tipo` ENUM ('PRODUCTO_BAJO_STOCK', 'PRODUCTO_MAS_VENDIDO', 'PRODUCTO_MENOS_VENDIDO', 'REABASTECIMIENTO', 'PREFERENCIA_CLIENTE', 'ANALISIS_VENTAS') NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `resultado` text NOT NULL,
  `fecha_analisis` datetime NOT NULL
);

CREATE TABLE `recomendacion_ia` (
  `id_recomendacion` int PRIMARY KEY AUTO_INCREMENT,
  `id_analisis` int NOT NULL,
  `id_producto` int,
  `id_cliente` int,
  `tipo` ENUM ('PRODUCTO_BAJO_STOCK', 'PRODUCTO_MAS_VENDIDO', 'PRODUCTO_MENOS_VENDIDO', 'REABASTECIMIENTO', 'PREFERENCIA_CLIENTE', 'ANALISIS_VENTAS') NOT NULL,
  `recomendacion` text NOT NULL,
  `prioridad` varchar(20),
  `fecha_recomendacion` datetime NOT NULL,
  `atendida` boolean NOT NULL DEFAULT false
);

ALTER TABLE `usuario` ADD FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);

ALTER TABLE `producto` ADD FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

ALTER TABLE `historial_precio` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

ALTER TABLE `historial_precio` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `movimiento_inventario` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

ALTER TABLE `movimiento_inventario` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `venta` ADD FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);

ALTER TABLE `venta` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `detalle_venta` ADD FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`);

ALTER TABLE `detalle_venta` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

ALTER TABLE `movimiento_cuenta` ADD FOREIGN KEY (`id_cuenta`) REFERENCES `cuenta` (`id_cuenta`);

ALTER TABLE `movimiento_cuenta` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `movimiento_cuenta` ADD FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`);

ALTER TABLE `transferencia` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `transferencia` ADD FOREIGN KEY (`id_cuenta_origen`) REFERENCES `cuenta` (`id_cuenta`);

ALTER TABLE `transferencia` ADD FOREIGN KEY (`id_cuenta_destino`) REFERENCES `cuenta` (`id_cuenta`);

ALTER TABLE `operacion_corresponsal` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `operacion_corresponsal` ADD FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);

ALTER TABLE `operacion_corresponsal` ADD FOREIGN KEY (`id_cuenta`) REFERENCES `cuenta` (`id_cuenta`);

ALTER TABLE `alerta_inventario` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

ALTER TABLE `analisis_ia` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `recomendacion_ia` ADD FOREIGN KEY (`id_analisis`) REFERENCES `analisis_ia` (`id_analisis`);

ALTER TABLE `recomendacion_ia` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

ALTER TABLE `recomendacion_ia` ADD FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);
