<?php
/**
 * Punto de entrada público de la aplicación
 */

// Cargar configuración global
require_once __DIR__ . '/../Config/config.php';

// Cargar enrutador y procesar la petición
require_once __DIR__ . '/../Routes/web.php';

Router::resolve();
