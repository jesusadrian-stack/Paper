<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/MovimientoCuenta.php';
require_once __DIR__ . '/../Models/OperacionCorresponsal.php';

class CorresponsalService {
    private PDO $db;
    private Cuenta $cuentaModel;
    private MovimientoCuenta $movimientoCuentaModel;
    private OperacionCorresponsal $operacionModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->cuentaModel = new Cuenta();
        $this->movimientoCuentaModel = new MovimientoCuenta();
        $this->operacionModel = new OperacionCorresponsal();
    }

    public function registrarOperacion(array $data): array {
        $tipo = strtoupper($data['tipo'] ?? '');
        $valor = (float)($data['valor'] ?? 0);
        $userId = (int)($data['id_usuario'] ?? 0);
        $clientId = !empty($data['id_cliente']) ? (int)$data['id_cliente'] : null;
        $referencia = $data['referencia'] ?? null;
        $descripcion = $data['descripcion'] ?? null;

        if (!in_array($tipo, ['DEPOSITO', 'RETIRO'])) {
            return ['success' => false, 'message' => 'Tipo de operación inválido. Solo se permite DEPOSITO o RETIRO.'];
        }

        if ($valor <= 0) {
            return ['success' => false, 'message' => 'El valor de la operación debe ser mayor a cero.'];
        }

        try {
            $this->db->beginTransaction();

            $cuenta = $this->cuentaModel->getByTipo('CORRESPONSAL');
            if (!$cuenta) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'No se encontró la cuenta de Corresponsal.'];
            }

            $cuentaId = (int)$cuenta['id_cuenta'];
            $saldoAnterior = (float)$cuenta['saldo'];
            $saldoNuevo = $saldoAnterior;

            if ($tipo === 'DEPOSITO') {
                $saldoNuevo += $valor;
            } elseif ($tipo === 'RETIRO') {
                if ($saldoAnterior < $valor) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "Saldo insuficiente en la cuenta Corresponsal. Saldo disponible: " . formatMoney($saldoAnterior) . ", solicitado: " . formatMoney($valor) . "."];
                }
                $saldoNuevo -= $valor;
            }

            // 1. Actualizar saldo de cuenta
            $this->cuentaModel->updateSaldo($cuentaId, $saldoNuevo);

            // 2. Registrar movimiento de cuenta
            $concepto = ($tipo === 'DEPOSITO' ? 'Depósito Corresponsal' : 'Retiro Corresponsal') . ($referencia ? " - Ref: {$referencia}" : "");
            $this->movimientoCuentaModel->create([
                'id_cuenta'      => $cuentaId,
                'id_usuario'     => $userId,
                'id_venta'       => null,
                'tipo'           => $tipo,
                'concepto'       => $concepto,
                'valor'          => $valor,
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo'    => $saldoNuevo
            ]);

            // 3. Registrar operación de corresponsal
            $operacionId = $this->operacionModel->create([
                'id_usuario'  => $userId,
                'id_cliente'  => $clientId,
                'id_cuenta'   => $cuentaId,
                'tipo'        => $tipo,
                'valor'       => $valor,
                'referencia'  => $referencia,
                'descripcion' => $descripcion
            ]);

            $this->db->commit();
            return [
                'success'      => true,
                'message'      => "Operación de {$tipo} registrada exitosamente.",
                'id_operacion' => $operacionId,
                'saldo_nuevo'  => $saldoNuevo
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al procesar la operación bancaria: ' . $e->getMessage()];
        }
    }
}
