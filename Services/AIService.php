<?php
require_once __DIR__ . '/../AI/AIProvider.php';
require_once __DIR__ . '/../Models/AnalisisIA.php';
require_once __DIR__ . '/../Models/RecomendacionIA.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/DetalleVenta.php';
require_once __DIR__ . '/../Models/Venta.php';

class AIService {
    private AIProviderInterface $provider;
    private AnalisisIA $analisisModel;
    private RecomendacionIA $recomendacionModel;
    private Producto $productoModel;
    private DetalleVenta $detalleVentaModel;
    private Venta $ventaModel;

    public function __construct() {
        $this->provider = AIFactory::create();
        $this->analisisModel = new AnalisisIA();
        $this->recomendacionModel = new RecomendacionIA();
        $this->productoModel = new Producto();
        $this->detalleVentaModel = new DetalleVenta();
        $this->ventaModel = new Venta();
    }

    public function generarAnalisisReabastecimiento(int $userId): array {
        $lowStock = $this->productoModel->getLowStockProducts();
        $context = [
            'type'               => 'REABASTECIMIENTO',
            'low_stock_products' => $lowStock
        ];

        $aiOutput = $this->provider->generateAnalysis(
            "Analizar productos con bajo inventario y sugerir reabastecimiento inteligente",
            $context
        );

        $titulo = "Diagnóstico de Reabastecimiento de Inventario (" . date('d/m/Y H:i') . ")";
        $analisisId = $this->analisisModel->create($userId, 'REABASTECIMIENTO', $titulo, $aiOutput['analysis']);

        // Guardar recomendaciones generadas
        if (!empty($aiOutput['recommendations'])) {
            foreach ($aiOutput['recommendations'] as $rec) {
                $this->recomendacionModel->create([
                    'id_analisis'   => $analisisId,
                    'id_producto'   => $rec['id_producto'] ?? null,
                    'id_cliente'    => null,
                    'tipo'          => $rec['tipo'] ?? 'COMPRA',
                    'recomendacion' => $rec['recomendacion'],
                    'prioridad'     => $rec['prioridad'] ?? 'MEDIA'
                ]);
            }
        }

        return [
            'success'     => true,
            'id_analisis' => $analisisId,
            'analisis'    => $aiOutput['analysis'],
            'titulo'      => $titulo
        ];
    }

    public function generarAnalisisTendenciasVentas(int $userId): array {
        $topProducts = $this->detalleVentaModel->getTopSellingProducts(10);
        $salesStats = $this->ventaModel->getTodayStats();
        $context = [
            'type'         => 'VENTAS_TENDENCIAS',
            'top_products' => $topProducts,
            'sales_stats'  => $salesStats
        ];

        $aiOutput = $this->provider->generateAnalysis(
            "Analizar tendencias de ventas de productos y comportamiento comercial",
            $context
        );

        $titulo = "Tendencias Comerciales y Productos Más Vendidos (" . date('d/m/Y H:i') . ")";
        $analisisId = $this->analisisModel->create($userId, 'VENTAS_TENDENCIAS', $titulo, $aiOutput['analysis']);

        if (!empty($aiOutput['recommendations'])) {
            foreach ($aiOutput['recommendations'] as $rec) {
                $this->recomendacionModel->create([
                    'id_analisis'   => $analisisId,
                    'id_producto'   => $rec['id_producto'] ?? null,
                    'id_cliente'    => null,
                    'tipo'          => $rec['tipo'] ?? 'ESTRATEGIA_VENTA',
                    'recomendacion' => $rec['recomendacion'],
                    'prioridad'     => $rec['prioridad'] ?? 'MEDIA'
                ]);
            }
        }

        return [
            'success'     => true,
            'id_analisis' => $analisisId,
            'analisis'    => $aiOutput['analysis'],
            'titulo'      => $titulo
        ];
    }
}
