<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: reportes.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP para la visualización de 
 * reportes comerciales, rotación y valorización de inventario y balances financieros.
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador ──> Router (HTTP GET) ──> ReporteController ──> Modelos (Venta, Producto, MovimientoCuenta, TransaccionCorresponsal) / Vistas (Views/Reportes)
 */

// Importa el controlador general de reportes estadísticos.
// CONEXIÓN: Conecta con /Controllers/ReporteController.php
require_once __DIR__ . '/../Controllers/ReporteController.php';

/**
 * RUTA 1: Centro Principal de Reportes
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra el panel interactivo con accesos y resúmenes ejecutivos de todas las áreas del negocio.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/reportes
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ReporteController
 *   - Método Invocado: index()
 *   - Vista Conectada: Renderiza 'Views/Reportes/index.php'
 */
Router::get('reportes', ['ReporteController', 'index']);

/**
 * RUTA 2: Reporte Detallado de Ventas
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Gráficos de tendencias, ventas por día/mes, productos más vendidos y rendimiento por cajero/vendedor.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/reportes/ventas
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ReporteController
 *   - Método Invocado: ventas()
 *   - Modelos Conectados: 'models/Venta.php' y 'models/DetalleVenta.php'
 *   - Vista Conectada: Renderiza 'Views/Reportes/ventas.php'
 */
Router::get('reportes/ventas', ['ReporteController', 'ventas']);

/**
 * RUTA 3: Reporte de Inventario y Existencias
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra el valor monetario del almacén (costo vs. venta estimada), productos sin rotación y agotados.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/reportes/inventario
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ReporteController
 *   - Método Invocado: inventario()
 *   - Modelos Conectados: 'models/Producto.php' y 'models/MovimientoInventario.php'
 *   - Vista Conectada: Renderiza 'Views/Reportes/inventario.php'
 */
Router::get('reportes/inventario', ['ReporteController', 'inventario']);

/**
 * RUTA 4: Reporte Financiero y Balance Global
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra ingresos totales, costos, utilidades netas y comisiones ganadas por el corresponsal bancario.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/reportes/finanzas
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ReporteController
 *   - Método Invocado: finanzas()
 *   - Modelos Conectados: 'models/Cuenta.php', 'models/MovimientoCuenta.php' y 'models/TransaccionCorresponsal.php'
 *   - Vista Conectada: Renderiza 'Views/Reportes/finanzas.php'
 */
Router::get('reportes/finanzas', ['ReporteController', 'finanzas']);
