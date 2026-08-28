<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: web.php (Enrutador Central y Despachador Principal)
 * ============================================================================
 * 
 * PROPÓSITO:
 * Es el núcleo del sistema de rutas. Define la clase estática `Router` encargada de:
 * 1. Registrar rutas con métodos HTTP (GET y POST).
 * 2. Analizar la URL solicitada por el usuario desde el navegador.
 * 3. Despachar la petición instanciando dinámicamente el controlador correspondiente
 *    e invocando el método de acción necesario.
 * 4. Cargar modularmente todos los sub-archivos de rutas de la carpeta /Routes.
 * 
 * MAPA DE FLUJO GENERAL DE UNA PETICIÓN:
 * 1. Usuario hace clic o envía formulario en el navegador (ej: http://localhost/Paper/ventas/create).
 * 2. El servidor Apache redirige la petición a `public/index.php`.
 * 3. `public/index.php` incluye a `Routes/web.php` y llama a `Router::resolve()`.
 * 4. `Router::resolve()` busca qué controlador y método corresponden a la URL.
 * 5. Se ejecuta el controlador, consulta los modelos (base de datos) y retorna la vista al usuario.
 */

class Router {
    /**
     * Almacén en memoria de todas las rutas registradas en el sistema.
     * Estructura:
     * [
     *   'GET'  => [ 'ruta1' => ['Controlador', 'metodo'], ... ],
     *   'POST' => [ 'ruta2' => ['Controlador', 'metodo'], ... ]
     * ]
     */
    private static array $routes = [];

    /**
     * MÉTODO: Router::get
     * ------------------------------------------------------------------------
     * - ¿PARA QUÉ SIRVE?: Registra una ruta accesible exclusivamente vía peticiones HTTP GET (enlaces, URLs directas, navegación).
     * - ¿QUÉ CONECTA CON QUÉ?:
     *   - Conecta: La URL indicada en $path ──> con el Controlador y Método especificado en $handler.
     * 
     * @param string $path URL relativa (ej: 'productos', 'clientes/create').
     * @param array|callable $handler Arreglo ['NombreControlador', 'nombreMetodo'] o función closure.
     */
    public static function get(string $path, array|callable $handler): void {
        self::addRoute('GET', $path, $handler);
    }

    /**
     * MÉTODO: Router::post
     * ------------------------------------------------------------------------
     * - ¿PARA QUÉ SIRVE?: Registra una ruta accesible mediante peticiones HTTP POST (envío de formularios, peticiones AJAX de guardado).
     * - ¿QUÉ CONECTA CON QUÉ?:
     *   - Conecta: La URL del formulario en $path ──> con el Controlador y Método procesador en $handler.
     * 
     * @param string $path URL relativa (ej: 'ventas/store', 'auth/login').
     * @param array|callable $handler Arreglo ['NombreControlador', 'nombreMetodo'] o función closure.
     */
    public static function post(string $path, array|callable $handler): void {
        self::addRoute('POST', $path, $handler);
    }

    /**
     * MÉTODO: Router::addRoute (Interno)
     * ------------------------------------------------------------------------
     * - ¿PARA QUÉ SIRVE?: Normaliza la URL (eliminando barras sobrantes) y la indexa dentro del array estático de rutas.
     * - ¿QUÉ CONECTA CON QUÉ?:
     *   - Conecta: El método HTTP ('GET'|'POST') y el path normalizado con su handler en `self::$routes`.
     */
    private static function addRoute(string $method, string $path, array|callable $handler): void {
        $path = trim($path, '/');
        self::$routes[$method][$path] = $handler;
    }

    /**
     * MÉTODO: Router::resolve (Despachador)
     * ------------------------------------------------------------------------
     * - ¿PARA QUÉ SIRVE?: Analiza la petición del navegador actual, busca la ruta y la ejecuta. Si no existe, muestra el error 404.
     * - ¿QUÉ CONECTA CON QUÉ?:
     *   - Conecta: La petición del navegador ($_SERVER['REQUEST_METHOD'] y $_SERVER['REQUEST_URI']) 
     *              ──> con el Handler correspondiente mediante `self::executeHandler()`.
     */
    public static function resolve(): void {
        // Obtiene el método de la petición actual (GET, POST, etc.)
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // 1. Detectar si la ruta viene como parámetro de consulta explícito: ?r=productos/create
        $uri = $_GET['r'] ?? null;

        // 2. Si no viene como ?r=, analizar la URL limpia amigable desde REQUEST_URI
        if ($uri === null) {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            $parsedUrl = parse_url($requestUri, PHP_URL_PATH);
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $baseDir = dirname($scriptName);
            $baseDir = str_replace('\\', '/', $baseDir);

            // Eliminar el subdirectorio de la aplicación para dejar solo la ruta relativa
            if (strpos($parsedUrl, $baseDir) === 0) {
                $uri = substr($parsedUrl, strlen($baseDir));
            } else {
                $uri = $parsedUrl;
            }
        }

        // Limpiar diagonales iniciales y finales
        $uri = trim($uri, '/');
        // Eliminar 'index.php' si viene en la ruta
        $uri = preg_replace('#^index\.php/?#', '', $uri);
        $uri = trim($uri, '/');

        // Si la ruta solicitada está vacía, se asume la ruta por defecto: 'dashboard'
        if ($uri === '') {
            $uri = 'dashboard';
        }

        // 3. Búsqueda directa: ¿Existe coincidencia exacta en las rutas registradas?
        if (isset(self::$routes[$method][$uri])) {
            $handler = self::$routes[$method][$uri];
            self::executeHandler($handler);
            return;
        }

        // 4. Búsqueda con parámetros dinámicos tipo {id}: (ej: ventas/show/{id})
        foreach (self::$routes[$method] ?? [] as $pattern => $handler) {
            $patternRegex = '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $pattern) . '$#';
            if (preg_match($patternRegex, $uri, $matches)) {
                array_shift($matches); // Quita la coincidencia completa
                self::executeHandler($handler, $matches);
                return;
            }
        }

        // 5. Si ninguna ruta coincide, emitir código HTTP 404 y renderizar pantalla de error
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>404 No Encontrado</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='d-flex align-items-center justify-content-center vh-100 bg-light'><div class='text-center p-5 card shadow-sm'><h1>404</h1><p class='lead'>La página solicitada no existe o fue movida.</p><a href='" . url('dashboard') . "' class='btn btn-primary'>Ir al Inicio</a></div></body></html>";
        exit;
    }

    /**
     * MÉTODO: Router::executeHandler (Ejecutor de Controladores)
     * ------------------------------------------------------------------------
     * - ¿PARA QUÉ SIRVE?: Instancia la clase del controlador e invoca su método enviándole los parámetros necesarios.
     * - ¿QUÉ CONECTA CON QUÉ?:
     *   - Conecta: El array [Controlador, Metodo] ──> Instancia el objeto `new $controllerClass()` 
     *     ──> Ejecuta el método `$controllerInstance->$actionMethod($params)`.
     * 
     * @param array|callable $handler Manejador de la ruta.
     * @param array $params Parámetros capturados en la URL.
     */
    private static function executeHandler(array|callable $handler, array $params = []): void {
        // Caso A: Si el handler es una función anónima (Closure)
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        // Caso B: Si el handler es un arreglo [NombreControlador, nombreMetodo]
        [$controllerClass, $actionMethod] = $handler;
        $controllerInstance = new $controllerClass();

        if (!empty($params)) {
            // Si la ruta capturó parámetros dinámicos por URL (ej: /show/12)
            call_user_func_array([$controllerInstance, $actionMethod], $params);
        } else {
            // Si el parámetro viene como query string tradicional ?id=12
            $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
            if ($id !== null && method_exists($controllerInstance, $actionMethod)) {
                $ref = new ReflectionMethod($controllerInstance, $actionMethod);
                if ($ref->getNumberOfParameters() > 0) {
                    $controllerInstance->$actionMethod($id);
                    return;
                }
            }
            // Ejecutar el método del controlador sin parámetros
            $controllerInstance->$actionMethod();
        }
    }
}

// ============================================================================
// CARGA Y CONEXIÓN DE CONTROLADORES PRINCIPALES Y SUB-ARCHIVOS DE RUTAS
// ============================================================================

// Controlador del Tablero Principal
// CONEXIÓN: Conecta con /Controllers/DashboardController.php
require_once __DIR__ . '/../Controllers/DashboardController.php';

// Carga de Sub-archivos de rutas especializadas por módulo de negocio:
require_once __DIR__ . '/auth.php';           // CONEXIÓN: Rutas de Login, Registro y Cierre de Sesión
require_once __DIR__ . '/usuarios.php';       // CONEXIÓN: Rutas de Usuarios y Roles del Sistema
require_once __DIR__ . '/productos.php';      // CONEXIÓN: Rutas de Catálogo, Categorías y Precios
require_once __DIR__ . '/inventario.php';     // CONEXIÓN: Rutas de Entradas, Salidas, Ajustes y Kárdex
require_once __DIR__ . '/clientes.php';       // CONEXIÓN: Rutas de Clientes y Búsqueda API
require_once __DIR__ . '/ventas.php';         // CONEXIÓN: Rutas del Punto de Venta (POS) y Tickets
require_once __DIR__ . '/cuentas.php';        // CONEXIÓN: Rutas de Finanzas (Papelería y Corresponsal)
require_once __DIR__ . '/corresponsal.php';   // CONEXIÓN: Rutas de Depósitos, Retiros y Comisiones
require_once __DIR__ . '/transferencias.php'; // CONEXIÓN: Rutas de Transferencias entre Cuentas Internas
require_once __DIR__ . '/reportes.php';       // CONEXIÓN: Rutas de Estadísticas y Balances
require_once __DIR__ . '/ia.php';             // CONEXIÓN: Rutas de Análisis Predictivo con IA

// ============================================================================
// RUTAS DEL TABLERO PRINCIPAL (DASHBOARD)
// ============================================================================

/**
 * RUTA: Raíz del Sitio
 * - ¿PARA QUÉ SIRVE?: Redirige la vista inicial al Tablero de Control.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL: http://localhost/Paper/
 *   - Controlador: DashboardController -> index()
 *   - Vista: Renderiza 'Views/Dashboard/index.php'
 */
Router::get('', ['DashboardController', 'index']);

/**
 * RUTA: Dashboard
 * - ¿PARA QUÉ SIRVE?: Muestra las tarjetas de resumen (ventas de hoy, stock crítico, transacciones bancarias y accesos directos).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL: http://localhost/Paper/dashboard
 *   - Controlador: DashboardController -> index()
 *   - Modelos Conectados: 'models/Venta.php', 'models/Producto.php', 'models/Cuenta.php', 'models/TransaccionCorresponsal.php'
 *   - Vista: Renderiza 'Views/Dashboard/index.php'
 */
Router::get('dashboard', ['DashboardController', 'index']);
