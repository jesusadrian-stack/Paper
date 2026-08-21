<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Models/OperacionCorresponsal.php';
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Services/CorresponsalService.php';

class CorresponsalController {
    private OperacionCorresponsal $operacionModel;
    private Cliente $clienteModel;
    private Cuenta $cuentaModel;
    private CorresponsalService $corresponsalService;

    public function __construct() {
        AuthMiddleware::check();
        $this->operacionModel = new OperacionCorresponsal();
        $this->clienteModel = new Cliente();
        $this->cuentaModel = new Cuenta();
        $this->corresponsalService = new CorresponsalService();
    }

    public function index(): void {
        $cuenta = $this->cuentaModel->getByTipo('CORRESPONSAL');
        $operacionesHoy = $this->operacionModel->getTodayStats();
        $operacionesRecientes = $this->operacionModel->getAll(null, null, null, 10);
        require_once VIEWS_PATH . '/Corresponsal/index.php';
    }

    public function deposito(): void {
        $clientes = $this->clienteModel->getAll(true);
        $cuenta = $this->cuentaModel->getByTipo('CORRESPONSAL');
        require_once VIEWS_PATH . '/Corresponsal/deposito.php';
    }

    public function retiro(): void {
        $clientes = $this->clienteModel->getAll(true);
        $cuenta = $this->cuentaModel->getByTipo('CORRESPONSAL');
        require_once VIEWS_PATH . '/Corresponsal/retiro.php';
    }

    public function store(): void {
        $tipo = strtoupper(sanitize($_POST['tipo'] ?? ''));
        $valor = (float)($_POST['valor'] ?? 0);
        $clientId = !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null;
        $referencia = sanitize($_POST['referencia'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');

        if (!in_array($tipo, ['DEPOSITO', 'RETIRO']) || $valor <= 0) {
            setFlashMessage('danger', 'Ingrese un tipo de operación y un valor válidos.');
            redirect('corresponsal/' . strtolower($tipo));
        }

        $res = $this->corresponsalService->registrarOperacion([
            'id_usuario'  => $_SESSION['user_id'],
            'id_cliente'  => $clientId,
            'tipo'        => $tipo,
            'valor'       => $valor,
            'referencia'  => $referencia,
            'descripcion' => $descripcion
        ]);

        if ($res['success']) {
            setFlashMessage('success', $res['message']);
            redirect('corresponsal/historial');
        } else {
            setFlashMessage('danger', $res['message']);
            redirect('corresponsal/' . strtolower($tipo));
        }
    }

    public function historial(): void {
        $tipo = sanitize($_GET['tipo'] ?? '');
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? '');
        $fechaFin = sanitize($_GET['fecha_fin'] ?? '');

        $cuenta = $this->cuentaModel->getByTipo('CORRESPONSAL');
        $operaciones = $this->operacionModel->getAll($tipo, $fechaInicio, $fechaFin, 300);

        require_once VIEWS_PATH . '/Corresponsal/historial.php';
    }
}
