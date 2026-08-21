<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/MovimientoInventario.php';
require_once __DIR__ . '/../Services/InventarioService.php';

class InventarioController {
    private Producto $productoModel;
    private MovimientoInventario $movimientoModel;
    private InventarioService $inventarioService;

    public function __construct() {
        AuthMiddleware::check();
        $this->productoModel = new Producto();
        $this->movimientoModel = new MovimientoInventario();
        $this->inventarioService = new InventarioService();
    }

    public function index(): void {
        $productos = $this->productoModel->getAll(false);
        require_once VIEWS_PATH . '/Inventario/index.php';
    }

    public function entrada(): void {
        RoleMiddleware::adminOnly();
        $productos = $this->productoModel->getAll(true);
        require_once VIEWS_PATH . '/Inventario/entrada.php';
    }

    public function storeEntrada(): void {
        RoleMiddleware::adminOnly();

        $productId = (int)($_POST['id_producto'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $motivo = sanitize($_POST['motivo'] ?? '');

        if ($productId <= 0 || $cantidad <= 0) {
            setFlashMessage('danger', 'Seleccione un producto válido y una cantidad mayor a cero.');
            redirect('inventario/entrada');
        }

        $res = $this->inventarioService->registrarEntrada($productId, $cantidad, $_SESSION['user_id'], $motivo);

        if ($res['success']) {
            setFlashMessage('success', $res['message']);
            redirect('inventario');
        } else {
            setFlashMessage('danger', $res['message']);
            redirect('inventario/entrada');
        }
    }

    public function salida(): void {
        RoleMiddleware::adminOnly();
        $productos = $this->productoModel->getAll(true);
        require_once VIEWS_PATH . '/Inventario/salida.php';
    }

    public function storeSalida(): void {
        RoleMiddleware::adminOnly();

        $productId = (int)($_POST['id_producto'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $motivo = sanitize($_POST['motivo'] ?? '');

        if ($productId <= 0 || $cantidad <= 0) {
            setFlashMessage('danger', 'Seleccione un producto válido y una cantidad mayor a cero.');
            redirect('inventario/salida');
        }

        $res = $this->inventarioService->registrarSalida($productId, $cantidad, $_SESSION['user_id'], $motivo);

        if ($res['success']) {
            setFlashMessage('success', $res['message']);
            redirect('inventario');
        } else {
            setFlashMessage('danger', $res['message']);
            redirect('inventario/salida');
        }
    }

    public function ajuste(): void {
        RoleMiddleware::adminOnly();
        $productos = $this->productoModel->getAll(true);
        require_once VIEWS_PATH . '/Inventario/ajuste.php';
    }

    public function storeAjuste(): void {
        RoleMiddleware::adminOnly();

        $productId = (int)($_POST['id_producto'] ?? 0);
        $stockFisico = (int)($_POST['stock_fisico'] ?? -1);
        $motivo = sanitize($_POST['motivo'] ?? '');

        if ($productId <= 0 || $stockFisico < 0) {
            setFlashMessage('danger', 'Seleccione un producto válido y un stock físico no negativo.');
            redirect('inventario/ajuste');
        }

        $res = $this->inventarioService->registrarAjuste($productId, $stockFisico, $_SESSION['user_id'], $motivo);

        if ($res['success']) {
            setFlashMessage('success', $res['message']);
            redirect('inventario');
        } else {
            setFlashMessage('danger', $res['message']);
            redirect('inventario/ajuste');
        }
    }

    public function historial(): void {
        $productId = !empty($_GET['producto']) ? (int)$_GET['producto'] : null;
        $tipo = sanitize($_GET['tipo'] ?? '');
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? '');
        $fechaFin = sanitize($_GET['fecha_fin'] ?? '');

        $movimientos = $this->movimientoModel->getAll($productId, $tipo, $fechaInicio, $fechaFin, 300);
        $productos = $this->productoModel->getAll(false);

        require_once VIEWS_PATH . '/Inventario/historial.php';
    }
}
