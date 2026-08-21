<?php
require_once __DIR__ . '/../Controllers/InventarioController.php';
require_once __DIR__ . '/../Controllers/AlertaInventarioController.php';

Router::get('inventario', ['InventarioController', 'index']);
Router::get('inventario/entrada', ['InventarioController', 'entrada']);
Router::post('inventario/entrada', ['InventarioController', 'storeEntrada']);
Router::get('inventario/salida', ['InventarioController', 'salida']);
Router::post('inventario/salida', ['InventarioController', 'storeSalida']);
Router::get('inventario/ajuste', ['InventarioController', 'ajuste']);
Router::post('inventario/ajuste', ['InventarioController', 'storeAjuste']);
Router::get('inventario/historial', ['InventarioController', 'historial']);

Router::get('alertas', ['AlertaInventarioController', 'index']);
Router::get('alertas/resolve', ['AlertaInventarioController', 'resolve']);
