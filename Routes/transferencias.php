<?php
require_once __DIR__ . '/../Controllers/TransferenciaController.php';

Router::get('transferencias', ['TransferenciaController', 'index']);
Router::get('transferencias/create', ['TransferenciaController', 'create']);
Router::post('transferencias/store', ['TransferenciaController', 'store']);
