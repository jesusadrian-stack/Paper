<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Models/AlertaInventario.php';

class AlertaInventarioController {
    private AlertaInventario $alertaModel;

    public function __construct() {
        AuthMiddleware::check();
        $this->alertaModel = new AlertaInventario();
    }

    public function index(): void {
        $alertas = $this->alertaModel->getAll();
        require_once VIEWS_PATH . '/Inventario/alertas.php';
    }

    public function resolve(int $id): void {
        $this->alertaModel->markAsResolved($id);
        setFlashMessage('success', 'Alerta marcada como atendida.');
        redirect('alertas');
    }
}
