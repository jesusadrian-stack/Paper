<?php
require_once __DIR__ . '/../Controllers/AuthController.php';

Router::get('auth/login', ['AuthController', 'showLogin']);
Router::post('auth/login', ['AuthController', 'login']);
Router::get('auth/register', ['AuthController', 'showRegister']);
Router::post('auth/register', ['AuthController', 'register']);
Router::get('auth/logout', ['AuthController', 'logout']);
