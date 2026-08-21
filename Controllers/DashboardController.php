<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/Venta.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../Models/AlertaInventario.php';
require_once __DIR__ . '/../Models/OperacionCorresponsal.php';
require_once __DIR__ . '/../Models/RecomendacionIA.php';
require_once __DIR__ . '/../Models/DetalleVenta.php';

class DashboardController {
    private Producto $productoModel;
    private Venta $ventaModel;
    private Cuenta $cuentaModel;
    private Cliente $clienteModel;
    private AlertaInventario $alertaModel;
    private OperacionCorresponsal $operacionModel;
    private RecomendacionIA $recomendacionModel;
    private DetalleVenta $detalleVentaModel;

    public function __construct() {
        AuthMiddleware::check();
        $this->productoModel = new Producto();
        $this->ventaModel = new Venta();
        $this->cuentaModel = new Cuenta();
        $this->clienteModel = new Cliente();
        $this->alertaModel = new AlertaInventario();
        $this->operacionModel = new OperacionCorresponsal();
        $this->recomendacionModel = new RecomendacionIA();
        $this->detalleVentaModel = new DetalleVenta();
    }

    public function index(): void {
        $isAdmin = ($_SESSION['user_role'] ?? '') === 'ADMINISTRADOR';
        $userId = $_SESSION['user_id'];

        $totalProductos = $this->productoModel->countTotal();
        $stockBajoCount = $this->productoModel->countLowStock();
        $ventasHoy = $this->ventaModel->getTodayStats();
        $totalClientes = $this->clienteModel->countTotal();
        $alertasCount = $this->alertaModel->countPending();

        $cuentaPapeleria = $this->cuentaModel->getByTipo('PAPELERIA');
        $cuentaCorresponsal = $this->cuentaModel->getByTipo('CORRESPONSAL');

        $saldoPapeleria = (float)($cuentaPapeleria['saldo'] ?? 0);
        $saldoCorresponsal = (float)($cuentaCorresponsal['saldo'] ?? 0);

        $ventasRecientes = $this->ventaModel->getAll(null, null, $isAdmin ? null : $userId, 5);
        $operacionesHoy = $this->operacionModel->getTodayStats();

        $salesChartData = $this->ventaModel->getSalesLastDays(7);
        $topProducts = $this->detalleVentaModel->getTopSellingProducts(5);
        $alertasPendientes = $this->alertaModel->getAll(true);

        require_once VIEWS_PATH . '/Dashboard/index.php';
    }
}
