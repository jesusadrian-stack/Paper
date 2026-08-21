<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/AnalisisIA.php';
require_once __DIR__ . '/../Models/RecomendacionIA.php';
require_once __DIR__ . '/../Services/AIService.php';

class AnalisisIAController {
    private AnalisisIA $analisisModel;
    private RecomendacionIA $recomendacionModel;
    private AIService $aiService;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->analisisModel = new AnalisisIA();
        $this->recomendacionModel = new RecomendacionIA();
        $this->aiService = new AIService();
    }

    public function index(): void {
        $analisisList = $this->analisisModel->getAll();
        $recomendacionesPendientes = $this->recomendacionModel->getAll(true, 10);
        $totalPendientes = $this->recomendacionModel->countPending();
        require_once VIEWS_PATH . '/IA/index.php';
    }

    public function show(int $id): void {
        $analisis = $this->analisisModel->getById($id);
        if (!$analisis) {
            setFlashMessage('danger', 'Análisis no encontrado.');
            redirect('ia');
        }

        $recomendaciones = $this->recomendacionModel->getByAnalysisId($id);
        require_once VIEWS_PATH . '/IA/analisis.php';
    }

    public function generar(string $tipo): void {
        if ($tipo === 'reabastecimiento') {
            $res = $this->aiService->generarAnalisisReabastecimiento($_SESSION['user_id']);
        } elseif ($tipo === 'tendencias') {
            $res = $this->aiService->generarAnalisisTendenciasVentas($_SESSION['user_id']);
        } else {
            setFlashMessage('danger', 'Tipo de análisis de IA no reconocido.');
            redirect('ia');
        }

        if ($res['success']) {
            setFlashMessage('success', "Análisis '{$res['titulo']}' generado con éxito.");
            redirect("ia/show?id={$res['id_analisis']}");
        } else {
            setFlashMessage('danger', 'No fue posible generar el análisis.');
            redirect('ia');
        }
    }
}
