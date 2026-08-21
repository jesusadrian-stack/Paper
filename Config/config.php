<?php
/**
 * Configuración General del Sistema
 */

// Definir ruta raíz del proyecto
define('ROOT_PATH', dirname(__DIR__));
define('VIEWS_PATH', ROOT_PATH . '/Views');

// Cargar variables de entorno desde .env si existe
if (file_exists(ROOT_PATH . '/.env')) {
    $lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Configuración de la aplicación
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    return $value;
}

// Determinar URL base dinámica
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? '') == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = str_replace('\\', '/', $scriptDir);
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');

// Si no está en servidor web, usar env
if (php_sapi_name() === 'cli' || empty($baseUrl)) {
    $baseUrl = rtrim(env('APP_URL', 'http://localhost:8080/Paper/public'), '/');
}

define('BASE_URL', $baseUrl);
define('APP_NAME', env('APP_NAME', 'Papelería y Corresponsal'));
define('APP_TIMEZONE', env('APP_TIMEZONE', 'America/Bogota'));

// Configurar zona horaria
date_default_timezone_set(APP_TIMEZONE);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE && php_sapi_name() !== 'cli') {
    session_start();
}

// Autocarga de clases (Controllers, Models, Services, Middleware, AI)
spl_autoload_register(function ($class) {
    $directories = [
        ROOT_PATH . '/Controllers/',
        ROOT_PATH . '/Models/',
        ROOT_PATH . '/Services/',
        ROOT_PATH . '/Middleware/',
        ROOT_PATH . '/AI/',
        ROOT_PATH . '/Config/'
    ];

    foreach ($directories as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Helpers globales de la aplicación
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '');
}

function asset($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/assets/' . $path;
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function formatMoney($amount) {
    return '$ ' . number_format((float)$amount, 2, ',', '.');
}

function sanitize($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener imagen adaptada para un producto según su código, categoría o nombre
 */
function getProductImage($producto): string {
    if (!empty($producto['imagen']) && file_exists(ROOT_PATH . '/Public/images/' . $producto['imagen'])) {
        return url('images/' . $producto['imagen']);
    }
    $code = strtoupper($producto['codigo'] ?? '');
    $cat = strtoupper($producto['categoria_nombre'] ?? '');
    $name = strtoupper($producto['nombre'] ?? '');

    if (strpos($code, 'ESC') === 0 || strpos($cat, 'ESCOLAR') !== false || strpos($name, 'CUADERNO') !== false) {
        if (strpos($name, 'COLOR') !== false || strpos($name, 'LÁPIZ') !== false || strpos($name, 'LAPIZ') !== false) {
            return url('images/colores.jpg');
        }
        return url('images/cuaderno.jpg');
    }
    if (strpos($code, 'PAP') === 0 || strpos($cat, 'PAPELER') !== false || strpos($name, 'PAPEL') !== false || strpos($name, 'CARPETA') !== false) {
        return url('images/papel.jpg');
    }
    if (strpos($code, 'TEC') === 0 || strpos($cat, 'TECNO') !== false || strpos($name, 'USB') !== false || strpos($name, 'CABLE') !== false) {
        return url('images/tecnologia.jpg');
    }
    if (strpos($code, 'OFI') === 0 || strpos($cat, 'OFICINA') !== false || strpos($name, 'GRAPAS') !== false || strpos($name, 'PERFORADORA') !== false) {
        return url('images/oficina.jpg');
    }
    if (strpos($code, 'ART') === 0 || strpos($cat, 'ARTE') !== false || strpos($name, 'PINTURA') !== false) {
        return url('images/arte.jpg');
    }
    return url('images/utiles.jpg');
}
