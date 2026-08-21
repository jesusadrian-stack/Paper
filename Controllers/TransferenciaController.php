<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/Transferencia.php';
require_once __DIR__ . '/../Services/TransferenciaService.php';

class TransferenciaController {
    private Cuenta $cuentaModel;
    private Transferencia $transferenciaModel;
    private TransferenciaService $transferenciaService;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->cuentaModel = new Cuenta();
        $this->transferenciaModel = new Transferencia();
        $this->transferenciaService = new TransferenciaService();
    }

    public function index(): void {
        $transferencias = $this->transferenciaModel->getAll();
        require_once VIEWS_PATH . '/Transferencias/index.php';
    }

    public function create(): void {
        $cuentas = $this->cuentaModel->getAll();
        require_once VIEWS_PATH . '/Transferencias/create.php';
    }

    public function store(): void {
        $origenId = (int)($_POST['id_cuenta_origen'] ?? 0);
        $destinoId = (int)($_POST['id_cuenta_destino'] ?? 0);
        $valor = (float)($_POST['valor'] ?? 0);
        $concepto = sanitize($_POST['concepto'] ?? '');

        if ($origenId <= 0 || $destinoId <= 0 || $valor <= 0) {
            setFlashMessage('danger', 'Seleccione cuentas válidas y un monto superior a cero.');
            redirect('transferencias/create');
        }

        $res = $this->transferenciaService->realizarTransferencia($_SESSION['user_id'], $origenId, $destinoId, $valor, $concepto);

        if ($res['success']) {
            setFlashMessage('success', $res['message']);
            redirect('transferencias');
        } else {
            setFlashMessage('danger', $res['message']);
            redirect('transferencias/create');
        }
    }
}
