<?php
require_once __DIR__ . '/../Controllers/UsuarioController.php';
require_once __DIR__ . '/../Controllers/RolController.php';

Router::get('usuarios', ['UsuarioController', 'index']);
Router::get('usuarios/create', ['UsuarioController', 'create']);
Router::post('usuarios/store', ['UsuarioController', 'store']);
Router::get('usuarios/edit', ['UsuarioController', 'edit']);
Router::post('usuarios/update', ['UsuarioController', 'update']);
Router::get('usuarios/toggle', ['UsuarioController', 'toggle']);

Router::get('roles', ['RolController', 'index']);
