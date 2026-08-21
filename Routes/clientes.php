<?php
require_once __DIR__ . '/../Controllers/ClienteController.php';

Router::get('clientes', ['ClienteController', 'index']);
Router::get('clientes/show', ['ClienteController', 'show']);
Router::get('clientes/create', ['ClienteController', 'create']);
Router::post('clientes/store', ['ClienteController', 'store']);
Router::get('clientes/edit', ['ClienteController', 'edit']);
Router::post('clientes/update', ['ClienteController', 'update']);
Router::get('clientes/toggle', ['ClienteController', 'toggle']);
Router::get('clientes/search-api', ['ClienteController', 'searchApi']);
