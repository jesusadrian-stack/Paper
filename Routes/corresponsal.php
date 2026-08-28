<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: corresponsal.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP relacionadas con las 
 * operaciones del Corresponsal Bancario (depósitos, retiros, comisiones y auditoría).
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador ──> Router (HTTP GET/POST) ──> CorresponsalController ──> Modelos (TransaccionCorresponsal, Cuenta, Cliente) / Vistas (Views/Corresponsal)
 */

// Importa el controlador de corresponsalía bancaria.
// CONEXIÓN: Conecta con el archivo físico: /Controllers/CorresponsalController.php
require_once __DIR__ . '/../Controllers/CorresponsalController.php';

/**
 * RUTA 1: Tablero Principal del Corresponsal Bancario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra el panel de control del corresponsal, saldos disponibles en caja y accesos directos a depósitos y retiros.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/corresponsal
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CorresponsalController
 *   - Método Invocado: index()
 *   - Modelos Conectados: 'models/Cuenta.php' (saldo de corresponsal) y 'models/TransaccionCorresponsal.php' (resumen de operaciones de hoy)
 *   - Vista Conectada: Renderiza 'Views/Corresponsal/index.php'
 */
Router::get('corresponsal', ['CorresponsalController', 'index']);

/**
 * RUTA 2: Formulario de Depósito Bancario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la interfaz para registrar que un cliente entrega efectivo físico para depositarlo en su cuenta bancaria.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/corresponsal/deposito
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CorresponsalController
 *   - Método Invocado: deposito()
 *   - Modelos Conectados: 'models/Cliente.php' (para autocompletar clientes) y 'models/Cuenta.php' (para verificar límite de saldo en corresponsal)
 *   - Vista Conectada: Renderiza 'Views/Corresponsal/deposito.php'
 */
Router::get('corresponsal/deposito', ['CorresponsalController', 'deposito']);

/**
 * RUTA 3: Formulario de Retiro Bancario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la interfaz para entregar efectivo al cliente tras validar su retiro en el banco corresponsal.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/corresponsal/retiro
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CorresponsalController
 *   - Método Invocado: retiro()
 *   - Modelos Conectados: 'models/Cliente.php' y 'models/Cuenta.php' (para asegurar que hay efectivo en caja para entregar)
 *   - Vista Conectada: Renderiza 'Views/Corresponsal/retiro.php'
 */
Router::get('corresponsal/retiro', ['CorresponsalController', 'retiro']);

/**
 * RUTA 4: Procesar Transacción Bancaria (Depósito o Retiro)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Ejecuta la lógica financiera de la transacción: calcula comisiones, crea el registro y ajusta el saldo de la cuenta bancaria de corresponsalía.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'corresponsal/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: CorresponsalController
 *   - Método Invocado: store()
 *   - Modelos Conectados:
 *     1. 'models/TransaccionCorresponsal.php' -> Crea el registro con código de comprobante, tipo, monto y comisión.
 *     2. 'models/Cuenta.php' -> Aumenta o descuenta el saldo de la cuenta bancaria y caja física.
 *   - Base de Datos: Tablas 'transacciones_corresponsal', 'cuentas' y 'movimientos_cuenta'.
 *   - Redirección Conectada: Redirige a 'corresponsal' o muestra el comprobante generado con mensaje de éxito.
 */
Router::post('corresponsal/store', ['CorresponsalController', 'store']);

/**
 * RUTA 5: Historial Completo de Transacciones de Corresponsalía
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la tabla histórica con todos los depósitos, retiros y comisiones ganadas con filtros de fecha.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/corresponsal/historial
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CorresponsalController
 *   - Método Invocado: historial()
 *   - Modelo Conectado: Invoca a 'models/TransaccionCorresponsal.php' (método allWithDetails)
 *   - Vista Conectada: Renderiza 'Views/Corresponsal/historial.php'
 */
Router::get('corresponsal/historial', ['CorresponsalController', 'historial']);
