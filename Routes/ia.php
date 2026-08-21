<?php
require_once __DIR__ . '/../Controllers/AnalisisIAController.php';
require_once __DIR__ . '/../Controllers/RecomendacionIAController.php';

Router::get('ia', ['AnalisisIAController', 'index']);
Router::get('ia/show', ['AnalisisIAController', 'show']);
Router::get('ia/generar', function() {
    $controller = new AnalisisIAController();
    $tipo = sanitize($_GET['tipo'] ?? 'reabastecimiento');
    $controller->generar($tipo);
});
Router::get('ia/recomendaciones', ['RecomendacionIAController', 'index']);
Router::get('ia/recomendaciones/resolve', ['RecomendacionIAController', 'resolve']);
