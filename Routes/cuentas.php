<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: cuentas.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP para la gestión de cuentas
 * financieras independientes (Cuenta Papelería vs. Cuenta Corresponsal) y sus
 * movimientos de entrada/salida de dinero.
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador ──> Router (HTTP GET/POST) ──> CuentaController / MovimientoCuentaController ──> Modelos (Cuenta, MovimientoCuenta) / Vistas (Views/Cuentas)
 */

// Importa los controladores financieros necesarios para gestionar cuentas y movimientos de dinero.
// CONEXIÓN: Conecta con /Controllers/CuentaController.php y /Controllers/MovimientoCuentaController.php
require_once __DIR__ . '/../Controllers/CuentaController.php';
require_once __DIR__ . '/../Controllers/MovimientoCuentaController.php';

/**
 * RUTA 1: Resumen General de Cuentas y Saldos
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Presenta la vista general del estado financiero con el saldo actual de cada cuenta (Papelería y Corresponsal).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/cuentas
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CuentaController
 *   - Método Invocado: index()
 *   - Modelo Conectado: Invoca a 'models/Cuenta.php' (método allWithBalance)
 *   - Vista Conectada: Renderiza 'Views/Cuentas/index.php'
 */
Router::get('cuentas', ['CuentaController', 'index']);

/**
 * RUTA 2: Vista de Movimientos de la Cuenta Papelería
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra los ingresos por ventas de mercancía, gastos directos y balance exclusivo del negocio de papelería.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/cuentas/papeleria
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CuentaController
 *   - Método Invocado: papeleria()
 *   - Modelo Conectado: Invoca a 'models/Cuenta.php' y 'models/MovimientoCuenta.php' (filtrando por cuenta_id de papelería)
 *   - Vista Conectada: Renderiza 'Views/Cuentas/papeleria.php'
 */
Router::get('cuentas/papeleria', ['CuentaController', 'papeleria']);

/**
 * RUTA 3: Vista de Movimientos de la Cuenta Corresponsal Bancario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra el saldo bancario, ingresos por depósitos recibidos, egresos por retiros y comisiones acumuladas.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/cuentas/corresponsal
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CuentaController
 *   - Método Invocado: corresponsal()
 *   - Modelo Conectado: Invoca a 'models/Cuenta.php' y 'models/MovimientoCuenta.php' (filtrando por cuenta_id de corresponsal)
 *   - Vista Conectada: Renderiza 'Views/Cuentas/corresponsal.php'
 */
Router::get('cuentas/corresponsal', ['CuentaController', 'corresponsal']);

/**
 * RUTA 4: Listado General de Todos los Movimientos Financieros
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra el libro diario financiero con todos los ingresos, egresos y traslados con filtros por fecha.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/cuentas/movimientos
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CuentaController
 *   - Método Invocado: movimientos()
 *   - Modelo Conectado: Invoca a 'models/MovimientoCuenta.php' (método allWithAccountDetails)
 *   - Vista Conectada: Renderiza 'Views/Cuentas/movimientos.php'
 */
Router::get('cuentas/movimientos', ['CuentaController', 'movimientos']);

/**
 * RUTA 5: Registrar Movimiento Manual en Caja / Cuenta (Ingreso o Egreso)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Permite registrar gastos manuales (pago de servicios, arriendo) o ingresos adicionales ajustando el saldo.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario modal/pantalla apuntando a 'cuentas/movimientos/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: MovimientoCuentaController
 *   - Método Invocado: store()
 *   - Modelos Conectados:
 *     1. 'models/MovimientoCuenta.php' -> Inserta el registro en la tabla 'movimientos_cuenta'.
 *     2. 'models/Cuenta.php' -> Suma o resta el saldo según sea tipo 'INGRESO' o 'EGRESO'.
 *   - Redirección Conectada: Redirige a 'cuentas/movimientos' con notificación de éxito.
 */
Router::post('cuentas/movimientos/store', ['MovimientoCuentaController', 'store']);
