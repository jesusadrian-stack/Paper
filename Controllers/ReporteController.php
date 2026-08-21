<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Services/ReporteService.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Usuario.php';

class ReporteController {
    private ReporteService $reporteService;
    private Categoria $categoriaModel;
    private Usuario $usuarioModel;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->reporteService = new ReporteService();
        $this->categoriaModel = new Categoria();
        $this->usuarioModel = new Usuario();
    }

    public function index(): void {
        require_once VIEWS_PATH . '/Reportes/index.php';
    }

    public function ventas(): void {
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? date('Y-m-01'));
        $fechaFin = sanitize($_GET['fecha_fin'] ?? date('Y-m-d'));
        $userId = !empty($_GET['usuario']) ? (int)$_GET['usuario'] : null;

        $reporte = $this->reporteService->getReporteVentas($fechaInicio, $fechaFin, $userId);
        $usuarios = $this->usuarioModel->getAll();

        require_once VIEWS_PATH . '/Reportes/ventas.php';
    }

    public function inventario(): void {
        $categoriaId = !empty($_GET['categoria']) ? (int)$_GET['categoria'] : null;
        $onlyLowStock = !empty($_GET['stock_bajo']);

        $reporte = $this->reporteService->getReporteInventario($categoriaId, $onlyLowStock);
        $categorias = $this->categoriaModel->getAll();

        require_once VIEWS_PATH . '/Reportes/inventario.php';
    }

    public function finanzas(): void {
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? date('Y-m-01'));
        $fechaFin = sanitize($_GET['fecha_fin'] ?? date('Y-m-d'));

        $reporte = $this->reporteService->getReporteFinanzas($fechaInicio, $fechaFin);

        require_once VIEWS_PATH . '/Reportes/finanzas.php';
    }
}
