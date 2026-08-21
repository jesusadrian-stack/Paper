<?php
require_once __DIR__ . '/../Controllers/ReporteController.php';

Router::get('reportes', ['ReporteController', 'index']);
Router::get('reportes/ventas', ['ReporteController', 'ventas']);
Router::get('reportes/inventario', ['ReporteController', 'inventario']);
Router::get('reportes/finanzas', ['ReporteController', 'finanzas']);
