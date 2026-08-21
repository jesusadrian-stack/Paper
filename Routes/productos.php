<?php
require_once __DIR__ . '/../Controllers/ProductoController.php';
require_once __DIR__ . '/../Controllers/CategoriaController.php';
require_once __DIR__ . '/../Controllers/HistorialPrecioController.php';

Router::get('productos', ['ProductoController', 'index']);
Router::get('productos/show', ['ProductoController', 'show']);
Router::get('productos/create', ['ProductoController', 'create']);
Router::post('productos/store', ['ProductoController', 'store']);
Router::get('productos/edit', ['ProductoController', 'edit']);
Router::post('productos/update', ['ProductoController', 'update']);
Router::get('productos/toggle', ['ProductoController', 'toggle']);
Router::get('productos/search-api', ['ProductoController', 'searchApi']);

Router::get('categorias', ['CategoriaController', 'index']);
Router::get('categorias/create', ['CategoriaController', 'create']);
Router::post('categorias/store', ['CategoriaController', 'store']);
Router::get('categorias/edit', ['CategoriaController', 'edit']);
Router::post('categorias/update', ['CategoriaController', 'update']);
Router::get('categorias/toggle', ['CategoriaController', 'toggle']);

Router::get('productos/historial-precios', ['HistorialPrecioController', 'index']);
