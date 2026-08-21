<?php
/**
 * Enrutador Principal del Sistema Web
 */

class Router {
    private static array $routes = [];

    public static function get(string $path, array|callable $handler): void {
        self::addRoute('GET', $path, $handler);
    }

    public static function post(string $path, array|callable $handler): void {
        self::addRoute('POST', $path, $handler);
    }

    private static function addRoute(string $method, string $path, array|callable $handler): void {
        $path = trim($path, '/');
        self::$routes[$method][$path] = $handler;
    }

    public static function resolve(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Detectar ruta solicitada desde $_GET['r'], PATH_INFO o REQUEST_URI
        $uri = $_GET['r'] ?? null;

        if ($uri === null) {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            $parsedUrl = parse_url($requestUri, PHP_URL_PATH);
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $baseDir = dirname($scriptName);
            $baseDir = str_replace('\\', '/', $baseDir);

            if (strpos($parsedUrl, $baseDir) === 0) {
                $uri = substr($parsedUrl, strlen($baseDir));
            } else {
                $uri = $parsedUrl;
            }
        }

        $uri = trim($uri, '/');
        // Eliminar prefijo index.php si viene en la URI
        $uri = preg_replace('#^index\.php/?#', '', $uri);
        $uri = trim($uri, '/');

        // Ruta por defecto si está vacía
        if ($uri === '') {
            $uri = 'dashboard';
        }

        // Buscar coincidencia exacta
        if (isset(self::$routes[$method][$uri])) {
            $handler = self::$routes[$method][$uri];
            self::executeHandler($handler);
            return;
        }

        // Si no se encuentra, buscar con parámetro id (ej: ventas/show?id=1 o ventas/show/1)
        foreach (self::$routes[$method] ?? [] as $pattern => $handler) {
            $patternRegex = '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $pattern) . '$#';
            if (preg_match($patternRegex, $uri, $matches)) {
                array_shift($matches);
                self::executeHandler($handler, $matches);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>404 No Encontrado</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='d-flex align-items-center justify-content-center vh-100 bg-light'><div class='text-center p-5 card shadow-sm'><h1>404</h1><p class='lead'>La página solicitada no existe o fue movida.</p><a href='" . url('dashboard') . "' class='btn btn-primary'>Ir al Inicio</a></div></body></html>";
        exit;
    }

    private static function executeHandler(array|callable $handler, array $params = []): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        [$controllerClass, $actionMethod] = $handler;
        $controllerInstance = new $controllerClass();

        if (!empty($params)) {
            call_user_func_array([$controllerInstance, $actionMethod], $params);
        } else {
            $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
            if ($id !== null && method_exists($controllerInstance, $actionMethod)) {
                $ref = new ReflectionMethod($controllerInstance, $actionMethod);
                if ($ref->getNumberOfParameters() > 0) {
                    $controllerInstance->$actionMethod($id);
                    return;
                }
            }
            $controllerInstance->$actionMethod();
        }
    }
}

// Cargar sub-rutas y controladores principales
require_once __DIR__ . '/../Controllers/DashboardController.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/usuarios.php';
require_once __DIR__ . '/productos.php';
require_once __DIR__ . '/inventario.php';
require_once __DIR__ . '/clientes.php';
require_once __DIR__ . '/ventas.php';
require_once __DIR__ . '/cuentas.php';
require_once __DIR__ . '/corresponsal.php';
require_once __DIR__ . '/transferencias.php';
require_once __DIR__ . '/reportes.php';
require_once __DIR__ . '/ia.php';

// Ruta dashboard y raíz
Router::get('', ['DashboardController', 'index']);
Router::get('dashboard', ['DashboardController', 'index']);
