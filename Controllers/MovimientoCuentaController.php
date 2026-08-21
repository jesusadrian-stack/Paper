<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/MovimientoCuenta.php';
require_once __DIR__ . '/../Services/CuentaService.php';

class MovimientoCuentaController {
    private Cuenta $cuentaModel;
    private MovimientoCuenta $movimientoModel;
    private CuentaService $cuentaService;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->cuentaModel = new Cuenta();
        $this->movimientoModel = new MovimientoCuenta();
        $this->cuentaService = new CuentaService();
    }

    public function index(): void {
        $cuentaId = !empty($_GET['cuenta']) ? (int)$_GET['cuenta'] : null;
        $tipo = sanitize($_GET['tipo'] ?? '');
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? '');
        $fechaFin = sanitize($_GET['fecha_fin'] ?? '');

        $cuentas = $this->cuentaModel->getAll();
        $movimientos = $this->movimientoModel->getAll($cuentaId, $tipo, $fechaInicio, $fechaFin, 300);

        require_once VIEWS_PATH . '/Cuentas/movimientos.php';
    }

    public function store(): void {
        $id_cuenta = (int)($_POST['id_cuenta'] ?? 0);
        $tipo = strtoupper(sanitize($_POST['tipo'] ?? ''));
        $valor = (float)($_POST['valor'] ?? 0);
        $concepto = sanitize($_POST['concepto'] ?? '');

        if ($id_cuenta <= 0 || !in_array($tipo, ['INGRESO', 'EGRESO']) || $valor <= 0 || empty($concepto)) {
            setFlashMessage('danger', 'Por favor complete todos los campos requeridos con valores válidos.');
            redirect('cuentas/movimientos');
        }

        $res = $this->cuentaService->registrarMovimiento($id_cuenta, $_SESSION['user_id'], $tipo, $valor, $concepto);

        if ($res['success']) {
            setFlashMessage('success', $res['message']);
        } else {
            setFlashMessage('danger', $res['message']);
        }

        redirect('cuentas/movimientos');
    }
}
