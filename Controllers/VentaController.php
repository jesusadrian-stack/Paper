<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Models/Venta.php';
require_once __DIR__ . '/../Models/DetalleVenta.php';
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Services/VentaService.php';

class VentaController {
    private Venta $ventaModel;
    private DetalleVenta $detalleVentaModel;
    private Cliente $clienteModel;
    private Producto $productoModel;
    private VentaService $ventaService;

    public function __construct() {
        AuthMiddleware::check();
        $this->ventaModel = new Venta();
        $this->detalleVentaModel = new DetalleVenta();
        $this->clienteModel = new Cliente();
        $this->productoModel = new Producto();
        $this->ventaService = new VentaService();
    }

    public function index(): void {
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? '');
        $fechaFin = sanitize($_GET['fecha_fin'] ?? '');
        $userId = !empty($_GET['usuario']) ? (int)$_GET['usuario'] : null;

        $isAdmin = ($_SESSION['user_role'] ?? '') === 'ADMINISTRADOR';
        if (!$isAdmin) {
            // Trabajador solo ve o filtra
        }

        $ventas = $this->ventaModel->getAll($fechaInicio, $fechaFin, $userId);
        require_once VIEWS_PATH . '/Ventas/index.php';
    }

    public function create(): void {
        $clientes = $this->clienteModel->getAll(true);
        $productos = $this->productoModel->getAll(true);
        require_once VIEWS_PATH . '/Ventas/create.php';
    }

    public function store(): void {
        // Soporta tanto petición JSON (Fetch / AJAX POS) como POST tradicional
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $isJson = strpos($contentType, 'application/json') !== false;

        if ($isJson) {
            $input = json_decode(file_get_contents('php://input'), true);
            $clientId = !empty($input['id_cliente']) ? (int)$input['id_cliente'] : null;
            $items = $input['items'] ?? [];
        } else {
            $clientId = !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null;
            $rawItems = $_POST['items'] ?? [];
            $items = is_array($rawItems) ? $rawItems : json_decode($rawItems, true);
        }

        if (empty($items) || !is_array($items)) {
            if ($isJson) {
                jsonResponse(['success' => false, 'message' => 'El carrito está vacío.'], 400);
            }
            setFlashMessage('danger', 'El carrito está vacío.');
            redirect('ventas/create');
        }

        $result = $this->ventaService->procesarVenta($_SESSION['user_id'], $clientId, $items);

        if ($isJson) {
            jsonResponse($result, $result['success'] ? 200 : 400);
        }

        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            redirect("ventas/show?id={$result['id_venta']}");
        } else {
            setFlashMessage('danger', $result['message']);
            redirect('ventas/create');
        }
    }

    public function show(int $id): void {
        $venta = $this->ventaModel->getById($id);
        if (!$venta) {
            setFlashMessage('danger', 'Venta no encontrada.');
            redirect('ventas');
        }

        $detalles = $this->detalleVentaModel->getBySaleId($id);
        require_once VIEWS_PATH . '/Ventas/show.php';
    }

    public function ticket(int $id): void {
        $venta = $this->ventaModel->getById($id);
        if (!$venta) {
            die('Venta no encontrada.');
        }

        $detalles = $this->detalleVentaModel->getBySaleId($id);
        require_once VIEWS_PATH . '/Ventas/ticket.php';
    }
}
