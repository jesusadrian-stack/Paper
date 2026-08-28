<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: inventario.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP para el control de stock,
 * entradas por compras, salidas por merma, ajustes manuales de conteo físico,
 * kárdex valorizado y sistema de alertas de existencias críticas.
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador ──> Router (HTTP GET/POST) ──> InventarioController / AlertaInventarioController ──> Modelos (MovimientoInventario, Producto, AlertaInventario) / Vistas (Views/Inventario)
 */

// Importa los controladores de inventario y alertas de stock.
// CONEXIÓN: Conecta con /Controllers/InventarioController.php y /Controllers/AlertaInventarioController.php
require_once __DIR__ . '/../Controllers/InventarioController.php';
require_once __DIR__ . '/../Controllers/AlertaInventarioController.php';

/**
 * RUTA 1: Vista General de Inventario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la tabla de existencias actuales, valor total del stock y productos en riesgo de agotarse.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/inventario
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: index()
 *   - Modelos Conectados: 'models/Producto.php' y 'models/MovimientoInventario.php'
 *   - Vista Conectada: Renderiza 'Views/Inventario/index.php'
 */
Router::get('inventario', ['InventarioController', 'index']);

/**
 * RUTA 2: Formulario de Entrada de Mercancía (Compras / Proveedores)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la pantalla para registrar el ingreso de nuevos productos al almacén.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/inventario/entrada
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: entrada()
 *   - Modelo Conectado: 'models/Producto.php' (para seleccionar los productos a abastecer)
 *   - Vista Conectada: Renderiza 'Views/Inventario/entrada.php'
 */
Router::get('inventario/entrada', ['InventarioController', 'entrada']);

/**
 * RUTA 3: Procesar y Registrar la Entrada de Mercancía
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Aumenta el stock físico del producto y añade el registro correspondiente en el kárdex.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'inventario/entrada'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: storeEntrada()
 *   - Modelos Conectados:
 *     1. 'models/Producto.php' -> Incrementa la columna 'stock'.
 *     2. 'models/MovimientoInventario.php' -> Inserta fila con tipo 'ENTRADA' en la tabla 'movimientos_inventario'.
 *   - Redirección Conectada: Redirige a 'inventario' con mensaje de confirmación.
 */
Router::post('inventario/entrada', ['InventarioController', 'storeEntrada']);

/**
 * RUTA 4: Formulario de Salida de Mercancía (Merma / Daño / Uso Interno)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la pantalla para dar de baja productos sin que sea una venta (ej: producto roto o vencido).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/inventario/salida
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: salida()
 *   - Modelo Conectado: 'models/Producto.php'
 *   - Vista Conectada: Renderiza 'Views/Inventario/salida.php'
 */
Router::get('inventario/salida', ['InventarioController', 'salida']);

/**
 * RUTA 5: Procesar y Registrar la Salida de Mercancía
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Descuenta el stock del producto y registra el motivo de la baja en el kárdex.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'inventario/salida'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: storeSalida()
 *   - Modelos Conectados:
 *     1. 'models/Producto.php' -> Decrementa la columna 'stock'.
 *     2. 'models/MovimientoInventario.php' -> Inserta fila con tipo 'SALIDA' y el motivo especificado.
 *   - Redirección Conectada: Redirige a 'inventario' con notificación.
 */
Router::post('inventario/salida', ['InventarioController', 'storeSalida']);

/**
 * RUTA 6: Formulario de Ajuste de Inventario (Conteo Físico)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga la pantalla para conciliar las cantidades reales contadas en estantería contra el sistema.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/inventario/ajuste
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: ajuste()
 *   - Modelo Conectado: 'models/Producto.php'
 *   - Vista Conectada: Renderiza 'Views/Inventario/ajuste.php'
 */
Router::get('inventario/ajuste', ['InventarioController', 'ajuste']);

/**
 * RUTA 7: Procesar y Guardar el Ajuste de Inventario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Sobrescribe o recalcula el stock para que coincida exactamente con el conteo físico y registra la diferencia.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'inventario/ajuste'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: storeAjuste()
 *   - Modelos Conectados:
 *     1. 'models/Producto.php' -> Fija el nuevo valor de stock.
 *     2. 'models/MovimientoInventario.php' -> Inserta registro con tipo 'AJUSTE'.
 *   - Redirección Conectada: Redirige a 'inventario'.
 */
Router::post('inventario/ajuste', ['InventarioController', 'storeAjuste']);

/**
 * RUTA 8: Historial Completo del Kárdex
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Auditoría cronológica de todos los movimientos de mercancía (entradas, ventas, mermas, ajustes).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/inventario/historial
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: InventarioController
 *   - Método Invocado: historial()
 *   - Modelo Conectado: 'models/MovimientoInventario.php' (método allWithProductAndUser)
 *   - Vista Conectada: Renderiza 'Views/Inventario/historial.php'
 */
Router::get('inventario/historial', ['InventarioController', 'historial']);

/**
 * RUTA 9: Panel de Alertas de Stock Bajo
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra los productos cuyo stock actual es menor o igual al stock mínimo configurado.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/alertas
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: AlertaInventarioController
 *   - Método Invocado: index()
 *   - Modelo Conectado: 'models/AlertaInventario.php' (método getActiveAlerts)
 *   - Vista Conectada: Renderiza 'Views/Alertas/index.php'
 */
Router::get('alertas', ['AlertaInventarioController', 'index']);

/**
 * RUTA 10: Resolver Alerta de Stock
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Marca una alerta de stock como atendida una vez que se gestiona la compra.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/alertas/resolve?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: AlertaInventarioController
 *   - Método Invocado: resolve()
 *   - Modelo Conectado: 'models/AlertaInventario.php' (método markResolved($id))
 *   - Base de Datos: Actualiza la tabla 'alertas_inventario'.
 *   - Redirección Conectada: Redirige a 'alertas'.
 */
Router::get('alertas/resolve', ['AlertaInventarioController', 'resolve']);
