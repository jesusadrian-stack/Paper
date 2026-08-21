<?php
require_once __DIR__ . '/../Controllers/CorresponsalController.php';

Router::get('corresponsal', ['CorresponsalController', 'index']);
Router::get('corresponsal/deposito', ['CorresponsalController', 'deposito']);
Router::get('corresponsal/retiro', ['CorresponsalController', 'retiro']);
Router::post('corresponsal/store', ['CorresponsalController', 'store']);
Router::get('corresponsal/historial', ['CorresponsalController', 'historial']);
