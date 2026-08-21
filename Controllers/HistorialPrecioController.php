<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/HistorialPrecio.php';

class HistorialPrecioController {
    private HistorialPrecio $historialPrecioModel;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->historialPrecioModel = new HistorialPrecio();
    }

    public function index(): void {
        $historial = $this->historialPrecioModel->getAll(200);
        require_once VIEWS_PATH . '/Productos/historial_precios.php';
    }
}
