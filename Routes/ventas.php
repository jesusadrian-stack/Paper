<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: ventas.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP para el Punto de Venta (POS),
 * procesamiento transaccional de ventas, consulta de facturas e impresión de tickets.
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador / POS JS ──> Router (HTTP GET/POST) ──> VentaController ──> Modelos (Venta, DetalleVenta, Producto, MovimientoInventario, Cuenta) / Vistas (Views/Ventas)
 */

// Importa el controlador de ventas y facturación.
// CONEXIÓN: Conecta con el archivo físico: /Controllers/VentaController.php
require_once __DIR__ . '/../Controllers/VentaController.php';

/**
 * RUTA 1: Listado General e Historial de Ventas
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la tabla de todas las ventas realizadas, número de factura, cliente, total, fecha y estado.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ventas
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: VentaController
 *   - Método Invocado: index()
 *   - Modelos Conectados: 'models/Venta.php' (método allWithClientAndUser)
 *   - Vista Conectada: Renderiza 'Views/Ventas/index.php'
 */
Router::get('ventas', ['VentaController', 'index']);

/**
 * RUTA 2: Pantalla del Punto de Venta (POS) Interactivo
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga la interfaz rápida de venta con catálogo visual, lector de código de barras y carrito interactivo.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ventas/create
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: VentaController
 *   - Método Invocado: create()
 *   - Frontend Conectado: Carga e inicializa el script interactivo 'Public/js/pos.js'
 *   - Modelos Conectados: 'models/Producto.php' (productos activos con stock) y 'models/Cliente.php' (clientes registrados)
 *   - Vista Conectada: Renderiza 'Views/Ventas/create.php'
 */
Router::get('ventas/create', ['VentaController', 'create']);

/**
 * RUTA 3: Procesar y Guardar Venta Transaccional (POST / AJAX)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Recibe el carrito desde el POS (JSON o POST), guarda la venta, descuenta stock y registra ingreso en caja.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST (Petición AJAX / Fetch desde pos.js o formulario HTML)
 *   - URL en Navegador / JS: Petición enviada a 'ventas/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: VentaController
 *   - Método Invocado: store()
 *   - Modelos y Acciones Transaccionales Conectadas:
 *     1. 'models/Venta.php' -> Genera número de comprobante e inserta cabecera de la venta.
 *     2. 'models/DetalleVenta.php' -> Inserta cada artículo con cantidad, precio unitario y subtotal.
 *     3. 'models/Producto.php' -> Reduce las existencias (stock) de cada producto vendido.
 *     4. 'models/MovimientoInventario.php' -> Registra la salida en el kárdex con tipo 'VENTA'.
 *     5. 'models/Cuenta.php' y 'models/MovimientoCuenta.php' -> Aumenta el saldo de la caja de papelería.
 *   - Respuesta Conectada: Devuelve JSON con `{ success: true, venta_id: X }` para imprimir ticket o redirige.
 */
Router::post('ventas/store', ['VentaController', 'store']);

/**
 * RUTA 4: Ver Detalle de una Venta / Factura Digital
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la factura completa con desglose de productos, cliente, método de pago y totales.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ventas/show?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: VentaController
 *   - Método Invocado: show()
 *   - Modelos Conectados: 'models/Venta.php' (findById($id)) y 'models/DetalleVenta.php' (getItemsByVentaId($id))
 *   - Vista Conectada: Renderiza 'Views/Ventas/show.php'
 */
Router::get('ventas/show', ['VentaController', 'show']);

/**
 * RUTA 5: Impresión de Ticket Térmico de Venta
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Genera una vista compacta optimizada para impresoras térmicas de 58mm / 80mm con auto-impresión (window.print).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ventas/ticket?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: VentaController
 *   - Método Invocado: ticket()
 *   - Modelos Conectados: 'models/Venta.php' y 'models/DetalleVenta.php'
 *   - Vista Conectada: Renderiza 'Views/Ventas/ticket.php' (plantilla térmica limpia sin barras laterales)
 */
Router::get('ventas/ticket', ['VentaController', 'ticket']);
