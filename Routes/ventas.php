<?php
require_once __DIR__ . '/../Controllers/VentaController.php';

Router::get('ventas', ['VentaController', 'index']);
Router::get('ventas/create', ['VentaController', 'create']);
Router::post('ventas/store', ['VentaController', 'store']);
Router::get('ventas/show', ['VentaController', 'show']);
Router::get('ventas/ticket', ['VentaController', 'ticket']);
