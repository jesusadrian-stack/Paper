<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/RecomendacionIA.php';

class RecomendacionIAController {
    private RecomendacionIA $recomendacionModel;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->recomendacionModel = new RecomendacionIA();
    }

    public function index(): void {
        $filter = sanitize($_GET['estado'] ?? 'pendientes');
        $onlyPending = ($filter === 'pendientes');
        $recomendaciones = $this->recomendacionModel->getAll($onlyPending, 100);
        require_once VIEWS_PATH . '/IA/recomendaciones.php';
    }

    public function resolve(int $id): void {
        $this->recomendacionModel->markAsResolved($id);
        setFlashMessage('success', 'Recomendación marcada como atendida.');
        redirect('ia/recomendaciones');
    }
}
