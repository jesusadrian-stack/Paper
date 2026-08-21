-- ==========================================================
-- BASE DE DATOS: papeleria_corresponsal
-- MOTOR: InnoDB | CHAR: utf8mb4 | COLLATE: utf8mb4_unicode_ci
-- Compatible con MySQL 8+ y MariaDB en Laragon
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `papeleria_corresponsal`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `papeleria_corresponsal`;

-- ----------------------------------------------------------
-- 1. TABLA: rol
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rol` (
    `id_rol` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` ENUM('ADMINISTRADOR','TRABAJADOR') NOT NULL UNIQUE,
    `descripcion` VARCHAR(200) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. TABLA: usuario
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario` (
    `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
    `id_rol` INT NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) NOT NULL,
    `documento` VARCHAR(30) NOT NULL UNIQUE,
    `telefono` VARCHAR(20) NULL,
    `correo` VARCHAR(150) UNIQUE,
    `nombre_usuario` VARCHAR(50) NOT NULL UNIQUE,
    `contrasena` VARCHAR(255) NOT NULL,
    `estado` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ultimo_acceso` DATETIME NULL,
    CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol`(`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. TABLA: categoria
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categoria` (
    `id_categoria` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) NULL,
    `estado` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. TABLA: producto
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `producto` (
    `id_producto` INT AUTO_INCREMENT PRIMARY KEY,
    `id_categoria` INT NOT NULL,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` VARCHAR(255) NULL,
    `precio` DECIMAL(12,2) NOT NULL,
    `stock_actual` INT NOT NULL DEFAULT 0,
    `stock_minimo` INT NOT NULL DEFAULT 0,
    `estado` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME NULL,
    CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria`(`id_categoria`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. TABLA: historial_precio
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `historial_precio` (
    `id_historial_precio` INT AUTO_INCREMENT PRIMARY KEY,
    `id_producto` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `precio_anterior` DECIMAL(12,2) NULL,
    `precio_nuevo` DECIMAL(12,2) NOT NULL,
    `fecha_cambio` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_historial_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto`(`id_producto`) ON UPDATE CASCADE,
    CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. TABLA: movimiento_inventario
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `movimiento_inventario` (
    `id_movimiento_inventario` INT AUTO_INCREMENT PRIMARY KEY,
    `id_producto` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `tipo` ENUM('ENTRADA','SALIDA','AJUSTE') NOT NULL,
    `cantidad` INT NOT NULL,
    `stock_anterior` INT NOT NULL,
    `stock_nuevo` INT NOT NULL,
    `motivo` VARCHAR(255) NULL,
    `fecha_movimiento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mov_inv_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto`(`id_producto`) ON UPDATE CASCADE,
    CONSTRAINT `fk_mov_inv_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 7. TABLA: cliente
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cliente` (
    `id_cliente` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo_identificacion` ENUM('CC','CE','NIT','TI','PASAPORTE') NOT NULL,
    `numero_identificacion` VARCHAR(30) NOT NULL UNIQUE,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) NULL,
    `telefono` VARCHAR(20) NULL,
    `correo` VARCHAR(150) NULL,
    `direccion` VARCHAR(200) NULL,
    `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `estado` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 8. TABLA: venta
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `venta` (
    `id_venta` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `id_cliente` INT NULL,
    `fecha_venta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `subtotal` DECIMAL(12,2) NOT NULL,
    `total` DECIMAL(12,2) NOT NULL,
    `estado` ENUM('COMPLETADA','CANCELADA') NOT NULL DEFAULT 'COMPLETADA',
    CONSTRAINT `fk_venta_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE,
    CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente`(`id_cliente`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 9. TABLA: detalle_venta
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `detalle_venta` (
    `id_detalle_venta` INT AUTO_INCREMENT PRIMARY KEY,
    `id_venta` INT NOT NULL,
    `id_producto` INT NOT NULL,
    `cantidad` INT NOT NULL,
    `precio_unitario` DECIMAL(12,2) NOT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL,
    CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`id_venta`) REFERENCES `venta`(`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto`(`id_producto`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 10. TABLA: cuenta
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cuenta` (
    `id_cuenta` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo` ENUM('PAPELERIA','CORRESPONSAL') NOT NULL UNIQUE,
    `nombre` VARCHAR(100) NOT NULL,
    `saldo` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `estado` ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 11. TABLA: movimiento_cuenta
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `movimiento_cuenta` (
    `id_movimiento_cuenta` INT AUTO_INCREMENT PRIMARY KEY,
    `id_cuenta` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `id_venta` INT NULL,
    `tipo` ENUM('INGRESO','EGRESO','DEPOSITO','RETIRO') NOT NULL,
    `concepto` VARCHAR(255) NOT NULL,
    `valor` DECIMAL(14,2) NOT NULL,
    `saldo_anterior` DECIMAL(14,2) NOT NULL,
    `saldo_nuevo` DECIMAL(14,2) NOT NULL,
    `fecha_movimiento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mov_cuenta` FOREIGN KEY (`id_cuenta`) REFERENCES `cuenta`(`id_cuenta`) ON UPDATE CASCADE,
    CONSTRAINT `fk_mov_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE,
    CONSTRAINT `fk_mov_venta` FOREIGN KEY (`id_venta`) REFERENCES `venta`(`id_venta`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 12. TABLA: transferencia
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `transferencia` (
    `id_transferencia` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `id_cuenta_origen` INT NOT NULL,
    `id_cuenta_destino` INT NOT NULL,
    `valor` DECIMAL(14,2) NOT NULL,
    `concepto` VARCHAR(255) NULL,
    `fecha_transferencia` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_transf_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE,
    CONSTRAINT `fk_transf_origen` FOREIGN KEY (`id_cuenta_origen`) REFERENCES `cuenta`(`id_cuenta`) ON UPDATE CASCADE,
    CONSTRAINT `fk_transf_destino` FOREIGN KEY (`id_cuenta_destino`) REFERENCES `cuenta`(`id_cuenta`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 13. TABLA: operacion_corresponsal
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `operacion_corresponsal` (
    `id_operacion` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `id_cliente` INT NULL,
    `id_cuenta` INT NOT NULL,
    `tipo` ENUM('DEPOSITO','RETIRO') NOT NULL,
    `valor` DECIMAL(14,2) NOT NULL,
    `referencia` VARCHAR(100) NULL,
    `descripcion` VARCHAR(255) NULL,
    `fecha_operacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_operacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE,
    CONSTRAINT `fk_operacion_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente`(`id_cliente`) ON UPDATE CASCADE,
    CONSTRAINT `fk_operacion_cuenta` FOREIGN KEY (`id_cuenta`) REFERENCES `cuenta`(`id_cuenta`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 14. TABLA: alerta_inventario
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `alerta_inventario` (
    `id_alerta` INT AUTO_INCREMENT PRIMARY KEY,
    `id_producto` INT NOT NULL,
    `fecha_alerta` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `stock_actual` INT NOT NULL,
    `stock_minimo` INT NOT NULL,
    `mensaje` VARCHAR(255) NOT NULL,
    `atendida` BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT `fk_alerta_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto`(`id_producto`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 15. TABLA: analisis_ia
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `analisis_ia` (
    `id_analisis` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NULL,
    `tipo` VARCHAR(100) NOT NULL,
    `titulo` VARCHAR(200) NOT NULL,
    `resultado` TEXT NOT NULL,
    `fecha_analisis` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_analisis_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 16. TABLA: recomendacion_ia
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `recomendacion_ia` (
    `id_recomendacion` INT AUTO_INCREMENT PRIMARY KEY,
    `id_analisis` INT NOT NULL,
    `id_producto` INT NULL,
    `id_cliente` INT NULL,
    `tipo` VARCHAR(100) NOT NULL,
    `recomendacion` TEXT NOT NULL,
    `prioridad` VARCHAR(20) NULL,
    `fecha_recomendacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atendida` BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT `fk_rec_analisis` FOREIGN KEY (`id_analisis`) REFERENCES `analisis_ia`(`id_analisis`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rec_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto`(`id_producto`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_rec_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente`(`id_cliente`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
