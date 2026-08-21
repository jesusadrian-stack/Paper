<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/MovimientoCuenta.php';

class CuentaController {
    private Cuenta $cuentaModel;
    private MovimientoCuenta $movimientoCuentaModel;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->cuentaModel = new Cuenta();
        $this->movimientoCuentaModel = new MovimientoCuenta();
    }

    public function index(): void {
        $cuentas = $this->cuentaModel->getAll();
        $movimientos = $this->movimientoCuentaModel->getAll(null, null, null, null, 20);
        require_once VIEWS_PATH . '/Cuentas/index.php';
    }

    public function papeleria(): void {
        $cuenta = $this->cuentaModel->getByTipo('PAPELERIA');
        $movimientos = $this->movimientoCuentaModel->getAll($cuenta['id_cuenta'], null, null, null, 100);
        require_once VIEWS_PATH . '/Cuentas/papeleria.php';
    }

    public function corresponsal(): void {
        $cuenta = $this->cuentaModel->getByTipo('CORRESPONSAL');
        $movimientos = $this->movimientoCuentaModel->getAll($cuenta['id_cuenta'], null, null, null, 100);
        require_once VIEWS_PATH . '/Cuentas/corresponsal.php';
    }

    public function movimientos(): void {
        $cuentaId = !empty($_GET['cuenta']) ? (int)$_GET['cuenta'] : null;
        $tipo = sanitize($_GET['tipo'] ?? '');
        $fechaInicio = sanitize($_GET['fecha_inicio'] ?? '');
        $fechaFin = sanitize($_GET['fecha_fin'] ?? '');

        $cuentas = $this->cuentaModel->getAll();
        $movimientos = $this->movimientoCuentaModel->getAll($cuentaId, $tipo, $fechaInicio, $fechaFin, 300);

        require_once VIEWS_PATH . '/Cuentas/movimientos.php';
    }
}
