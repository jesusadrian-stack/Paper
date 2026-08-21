<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/MovimientoCuenta.php';

class CuentaService {
    private PDO $db;
    private Cuenta $cuentaModel;
    private MovimientoCuenta $movimientoCuentaModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->cuentaModel = new Cuenta();
        $this->movimientoCuentaModel = new MovimientoCuenta();
    }

    public function registrarMovimiento(int $cuentaId, int $userId, string $tipo, float $valor, string $concepto, ?int $ventaId = null): array {
        if ($valor <= 0) {
            return ['success' => false, 'message' => 'El valor debe ser mayor a cero.'];
        }

        try {
            $this->db->beginTransaction();

            $cuenta = $this->cuentaModel->getById($cuentaId);
            if (!$cuenta) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Cuenta no encontrada.'];
            }

            $saldoAnterior = (float)$cuenta['saldo'];
            $saldoNuevo = $saldoAnterior;

            if ($tipo === 'INGRESO' || $tipo === 'DEPOSITO') {
                $saldoNuevo += $valor;
            } elseif ($tipo === 'EGRESO' || $tipo === 'RETIRO') {
                if ($saldoAnterior < $valor) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "Saldo insuficiente en la cuenta. Saldo actual: {$saldoAnterior}, solicitado: {$valor}."];
                }
                $saldoNuevo -= $valor;
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Tipo de movimiento inválido.'];
            }

            // Actualizar saldo de la cuenta
            $this->cuentaModel->updateSaldo($cuentaId, $saldoNuevo);

            // Registrar movimiento
            $movId = $this->movimientoCuentaModel->create([
                'id_cuenta'      => $cuentaId,
                'id_usuario'     => $userId,
                'id_venta'       => $ventaId,
                'tipo'           => $tipo,
                'concepto'       => $concepto,
                'valor'          => $valor,
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo'    => $saldoNuevo
            ]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Movimiento registrado con éxito.', 'id_movimiento' => $movId, 'saldo_nuevo' => $saldoNuevo];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al registrar movimiento: ' . $e->getMessage()];
        }
    }
}
