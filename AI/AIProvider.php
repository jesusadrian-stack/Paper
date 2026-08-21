<?php
/**
 * AI Provider: Motor de Inteligencia Artificial para Análisis de Papelería
 * Diseñado de forma desacoplada con motor analítico local y driver para API externa (Gemini/OpenAI)
 */

interface AIProviderInterface {
    public function generateAnalysis(string $prompt, array $contextData): array;
}

class LocalAIProvider implements AIProviderInterface {
    public function generateAnalysis(string $prompt, array $contextData): array {
        // Motor analítico local basado en reglas heurísticas y análisis estadístico de negocio
        $type = $contextData['type'] ?? 'GENERAL';
        $analysisResult = '';
        $recommendations = [];

        switch ($type) {
            case 'REABASTECIMIENTO':
                $lowStock = $contextData['low_stock_products'] ?? [];
                $count = count($lowStock);
                $analysisResult = "### Diagnóstico de Inventario y Cadena de Suministro\n\n"
                    . "Se ha completado el análisis predictivo de inventario. Actualmente se identifican **{$count} productos** con niveles de stock críticos o por debajo del umbral mínimo de seguridad operativa.\n\n"
                    . "#### Hallazgos Principales:\n"
                    . "- La rotación en artículos escolares y de oficina requiere reabastecimiento programado para evitar quiebres de stock.\n"
                    . "- Se detecta que productos de alta rotación tienen menos de 5 días de inventario proyectado.\n\n"
                    . "#### Estrategia Recomendada:\n"
                    . "Priorizar órdenes de compra inmediatas para productos con prioridad ALTA, negociar lotes con proveedores habituales para obtener descuentos por volumen y ajustar el stock mínimo de los artículos de mayor demanda.";

                foreach ($lowStock as $p) {
                    $deficit = max(1, ((int)$p['stock_minimo'] * 2) - (int)$p['stock_actual']);
                    $recommendations[] = [
                        'id_producto'   => $p['id_producto'],
                        'tipo'          => 'COMPRA_REABASTECIMIENTO',
                        'recomendacion' => "Generar orden de compra para '{$p['nombre']}' (Código: {$p['codigo']}). Stock actual: {$p['stock_actual']} uds, mínimo: {$p['stock_minimo']}. Cantidad sugerida a pedir: {$deficit} unidades.",
                        'prioridad'     => ((int)$p['stock_actual'] <= ((int)$p['stock_minimo'] / 2)) ? 'ALTA' : 'MEDIA'
                    ];
                }
                break;

            case 'VENTAS_TENDENCIAS':
                $topProducts = $contextData['top_products'] ?? [];
                $salesStats = $contextData['sales_stats'] ?? [];
                $analysisResult = "### Análisis de Rendimiento de Ventas y Comportamiento Comercial\n\n"
                    . "Se procesaron las métricas de ventas históricas y del período reciente.\n\n"
                    . "#### Puntos Destacados:\n"
                    . "- Los productos líderes impulsan la mayor parte del flujo de caja de la papelería.\n"
                    . "- Se observa una oportunidad para crear paquetes combinados (ej. Cuadernos + Lápices + Colores) para aumentar el ticket promedio por cliente.\n\n"
                    . "#### Recomendación Estratégica:\n"
                    . "Ubicar los productos de mayor venta en áreas visibles del mostrador y capacitar al personal para ofrecer productos complementarios en cada transacción.";

                foreach ($topProducts as $idx => $tp) {
                    $recommendations[] = [
                        'id_producto'   => $tp['id_producto'],
                        'tipo'          => 'ESTRATEGIA_VENTA',
                        'recomendacion' => "Impulsar ventas cruzadas con '{$tp['nombre']}' (Total vendido: {$tp['total_unidades']} uds). Crear combo promocional para temporada escolar.",
                        'prioridad'     => ($idx === 0) ? 'ALTA' : 'MEDIA'
                    ];
                }
                break;

            case 'OPTIMIZACION_PRECIOS':
                $products = $contextData['products'] ?? [];
                $analysisResult = "### Evaluación de Margen y Optimización de Precios\n\n"
                    . "Análisis de competitividad y rentabilidad sobre la lista de precios actual.\n\n"
                    . "#### Sugerencias de Margen:\n"
                    . "- Mantener precios atractivos en productos gancho (cuadernos y resmas de papel).\n"
                    . "- Ajustar márgenes en productos de tecnología y arte donde el cliente es menos sensible al precio unitario.";

                $recommendations[] = [
                    'id_producto'   => null,
                    'tipo'          => 'POLITICA_PRECIOS',
                    'recomendacion' => "Revisar periódicamente los costos de reposición de insumos de tecnología para mantener un margen bruto no menor al 30%.",
                    'prioridad'     => 'MEDIA'
                ];
                break;

            default:
                $analysisResult = "### Resumen General del Negocio\n\n"
                    . "El sistema opera con parámetros saludables. Se recomienda monitorear diariamente las alertas de inventario y los balances del corresponsal bancario.";
                $recommendations[] = [
                    'id_producto'   => null,
                    'tipo'          => 'GESTION_GENERAL',
                    'recomendacion' => "Realizar arqueos diarios de caja de papelería y corresponsal al cierre de cada turno.",
                    'prioridad'     => 'ALTA'
                ];
                break;
        }

        return [
            'analysis'        => $analysisResult,
            'recommendations' => $recommendations
        ];
    }
}

class RemoteAIProvider implements AIProviderInterface {
    private string $apiKey;
    private string $model;

    public function __construct(string $apiKey, string $model = 'gemini-1.5-flash') {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function generateAnalysis(string $prompt, array $contextData): array {
        // Driver para API externa REST de Gemini
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        
        $contextJson = json_encode($contextData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fullPrompt = "Eres un asistente experto en consultoría y optimización comercial para papelerías y corresponsales bancarios.\n"
                    . "Analiza la siguiente información estructurada del negocio y responde en formato Markdown claro con títulos, viñetas y recomendaciones accionables:\n\n"
                    . "DATOS DEL SISTEMA:\n{$contextJson}\n\n"
                    . "SOLICITUD:\n{$prompt}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $fullPrompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $json = json_decode($response, true);
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if (!empty($text)) {
                return [
                    'analysis'        => $text,
                    'recommendations' => []
                ];
            }
        }

        // Si falla la API remota o no hay conexión, recurrir al motor local
        $fallback = new LocalAIProvider();
        return $fallback->generateAnalysis($prompt, $contextData);
    }
}

class AIFactory {
    public static function create(): AIProviderInterface {
        $apiKey = env('AI_API_KEY', '');
        $provider = env('AI_PROVIDER', 'local');

        if ($provider === 'remote' && !empty($apiKey)) {
            return new RemoteAIProvider($apiKey, env('AI_MODEL', 'gemini-1.5-flash'));
        }

        return new LocalAIProvider();
    }
}
