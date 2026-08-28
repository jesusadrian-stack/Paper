<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: ia.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP del módulo de Inteligencia 
 * Artificial: análisis predictivos de inventario, detección de anomalías y recomendaciones.
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador ──> Router (HTTP GET) ──> AnalisisIAController / RecomendacionIAController ──> Servicios IA / Modelos (AnalisisIA, RecomendacionIA) / Vistas (Views/IA)
 */

// Importa los controladores que manejan los diagnósticos y recomendaciones de IA.
// CONEXIÓN: Conecta con /Controllers/AnalisisIAController.php y /Controllers/RecomendacionIAController.php
require_once __DIR__ . '/../Controllers/AnalisisIAController.php';
require_once __DIR__ . '/../Controllers/RecomendacionIAController.php';

/**
 * RUTA 1: Panel Principal del Módulo de IA
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra el centro de control de IA con el historial de diagnósticos y accesos para generar nuevos análisis.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ia
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: AnalisisIAController
 *   - Método Invocado: index()
 *   - Modelo Conectado: Invoca a 'models/AnalisisIA.php' (método allWithStats)
 *   - Vista Conectada: Renderiza 'Views/IA/index.php'
 */
Router::get('ia', ['AnalisisIAController', 'index']);

/**
 * RUTA 2: Ver Detalle de un Análisis de IA Específico
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Permite consultar los hallazgos completos, gráficos y sugerencias generadas por un análisis previo.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ia/show?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: AnalisisIAController
 *   - Método Invocado: show()
 *   - Modelo Conectado: Invoca a 'models/AnalisisIA.php' (método findById($id)) y 'models/RecomendacionIA.php'
 *   - Vista Conectada: Renderiza 'Views/IA/show.php'
 */
Router::get('ia/show', ['AnalisisIAController', 'show']);

/**
 * RUTA 3: Disparar Generación de Análisis con Inteligencia Artificial
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Ejecuta el motor analítico que recopila datos de ventas, kárdex y finanzas para procesar con IA.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ia/generar?tipo=reabastecimiento (o ventas / anomalias)
 *   - Enrutador: Router::get(...) utilizando una función anónima (Closure)
 *   - Lógica Interna del Closure:
 *     1. Instancia $controller = new AnalisisIAController();
 *     2. Limpia el parámetro tipo recibido con sanitize($_GET['tipo'] ?? 'reabastecimiento');
 *     3. Llama a $controller->generar($tipo);
 *   - Modelos / Servicios Conectados: Conecta con 'models/Producto.php', 'models/Venta.php' y 'models/AnalisisIA.php'.
 *   - Redirección Conectada: Redirige a 'ia/show?id=X' con el nuevo reporte creado.
 */
Router::get('ia/generar', function() {
    $controller = new AnalisisIAController();
    $tipo = sanitize($_GET['tipo'] ?? 'reabastecimiento');
    $controller->generar($tipo);
});

/**
 * RUTA 4: Listado de Recomendaciones Inteligentes Activas
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la lista de acciones prácticas sugeridas por la IA (ej: "Pedir 50 resmas", "Ajustar precio").
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ia/recomendaciones
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: RecomendacionIAController
 *   - Método Invocado: index()
 *   - Modelo Conectado: Invoca a 'models/RecomendacionIA.php' (método getPendingRecommendations)
 *   - Vista Conectada: Renderiza 'Views/IA/recomendaciones.php'
 */
Router::get('ia/recomendaciones', ['RecomendacionIAController', 'index']);

/**
 * RUTA 5: Resolver o Aplicar una Recomendación de IA
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Marca una sugerencia de IA como atendida o aplicada para retirarla de la lista de pendientes.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/ia/recomendaciones/resolve?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: RecomendacionIAController
 *   - Método Invocado: resolve()
 *   - Modelo Conectado: Invoca a 'models/RecomendacionIA.php' (método markAsResolved($id))
 *   - Base de Datos: Actualiza la columna 'estado' a 'APLICADA' en la tabla 'recomendaciones_ia'
 *   - Redirección Conectada: Redirige a 'ia/recomendaciones'.
 */
Router::get('ia/recomendaciones/resolve', ['RecomendacionIAController', 'resolve']);
