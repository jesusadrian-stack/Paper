<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: transferencias.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP para el traslado de dinero
 * entre las cuentas del sistema (Papelería <-> Corresponsal Bancario).
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador ──> Router (HTTP GET/POST) ──> TransferenciaController ──> Modelos (Transferencia, Cuenta, MovimientoCuenta) / Vistas (Views/Transferencias)
 */

// Importa el controlador de transferencias internas entre cuentas.
// CONEXIÓN: Conecta con /Controllers/TransferenciaController.php
require_once __DIR__ . '/../Controllers/TransferenciaController.php';

/**
 * RUTA 1: Listado e Historial de Transferencias
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la tabla histórica con todos los traslados de fondos realizados, origen, destino, monto y usuario responsable.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/transferencias
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: TransferenciaController
 *   - Método Invocado: index()
 *   - Modelo Conectado: 'models/Transferencia.php' (método allWithAccountNames)
 *   - Vista Conectada: Renderiza 'Views/Transferencias/index.php'
 */
Router::get('transferencias', ['TransferenciaController', 'index']);

/**
 * RUTA 2: Formulario de Nueva Transferencia
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga la pantalla para seleccionar la cuenta origen, cuenta destino, monto y concepto del traslado.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/transferencias/create
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: TransferenciaController
 *   - Método Invocado: create()
 *   - Modelo Conectado: 'models/Cuenta.php' (obtiene las cuentas activas y sus saldos actuales)
 *   - Vista Conectada: Renderiza 'Views/Transferencias/create.php'
 */
Router::get('transferencias/create', ['TransferenciaController', 'create']);

/**
 * RUTA 3: Procesar y Ejecutar la Transferencia entre Cuentas
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Valida saldo suficiente en la cuenta origen, resta el monto en origen, lo suma en destino y crea los registros transaccionales.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'transferencias/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: TransferenciaController
 *   - Método Invocado: store()
 *   - Modelos Conectados:
 *     1. 'models/Transferencia.php' -> Registra el traspaso en la tabla 'transferencias'.
 *     2. 'models/Cuenta.php' -> Debita la cuenta origen y acredita la cuenta destino.
 *     3. 'models/MovimientoCuenta.php' -> Crea el movimiento de EGRESO en origen y de INGRESO en destino.
 *   - Redirección Conectada: Redirige a 'transferencias' con confirmación de éxito.
 */
Router::post('transferencias/store', ['TransferenciaController', 'store']);
