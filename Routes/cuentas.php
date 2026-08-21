<?php
require_once __DIR__ . '/../Controllers/CuentaController.php';
require_once __DIR__ . '/../Controllers/MovimientoCuentaController.php';

Router::get('cuentas', ['CuentaController', 'index']);
Router::get('cuentas/papeleria', ['CuentaController', 'papeleria']);
Router::get('cuentas/corresponsal', ['CuentaController', 'corresponsal']);
Router::get('cuentas/movimientos', ['CuentaController', 'movimientos']);
Router::post('cuentas/movimientos/store', ['MovimientoCuentaController', 'store']);
