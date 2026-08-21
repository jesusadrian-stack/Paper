<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/MovimientoCuenta.php';
require_once __DIR__ . '/../Models/Transferencia.php';

class TransferenciaService {
    private PDO $db;
    private Cuenta $cuentaModel;
    private MovimientoCuenta $movimientoCuentaModel;
    private Transferencia $transferenciaModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->cuentaModel = new Cuenta();
        $this->movimientoCuentaModel = new MovimientoCuenta();
        $this->transferenciaModel = new Transferencia();
    }

    public function realizarTransferencia(int $userId, int $origenId, int $destinoId, float $valor, ?string $concepto = null): array {
        if ($origenId === $destinoId) {
            return ['success' => false, 'message' => 'La cuenta de origen y la de destino no pueden ser la misma.'];
        }

        if ($valor <= 0) {
            return ['success' => false, 'message' => 'El valor a transferir debe ser mayor a cero.'];
        }

        try {
            $this->db->beginTransaction();

            $cuentaOrigen = $this->cuentaModel->getById($origenId);
            $cuentaDestino = $this->cuentaModel->getById($destinoId);

            if (!$cuentaOrigen || !$cuentaDestino) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Una o ambas cuentas no existen.'];
            }

            if ($cuentaOrigen['estado'] !== 'ACTIVO' || $cuentaDestino['estado'] !== 'ACTIVO') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Ambas cuentas deben estar activas para realizar transferencias.'];
            }

            $saldoOrigenAnterior = (float)$cuentaOrigen['saldo'];
            if ($saldoOrigenAnterior < $valor) {
                $this->db->rollBack();
                return ['success' => false, 'message' => "Saldo insuficiente en la cuenta de origen ({$cuentaOrigen['nombre']}). Saldo disponible: " . formatMoney($saldoOrigenAnterior) . "."];
            }

            $saldoDestinoAnterior = (float)$cuentaDestino['saldo'];

            $saldoOrigenNuevo = $saldoOrigenAnterior - $valor;
            $saldoDestinoNuevo = $saldoDestinoAnterior + $valor;

            // 1. Actualizar saldo origen
            $this->cuentaModel->updateSaldo($origenId, $saldoOrigenNuevo);

            // 2. Actualizar saldo destino
            $this->cuentaModel->updateSaldo($destinoId, $saldoDestinoNuevo);

            // 3. Registrar egreso en origen
            $conceptoOrigen = "Transferencia enviada a {$cuentaDestino['nombre']}" . ($concepto ? " - {$concepto}" : "");
            $this->movimientoCuentaModel->create([
                'id_cuenta'      => $origenId,
                'id_usuario'     => $userId,
                'id_venta'       => null,
                'tipo'           => 'EGRESO',
                'concepto'       => $conceptoOrigen,
                'valor'          => $valor,
                'saldo_anterior' => $saldoOrigenAnterior,
                'saldo_nuevo'    => $saldoOrigenNuevo
            ]);

            // 4. Registrar ingreso en destino
            $conceptoDestino = "Transferencia recibida de {$cuentaOrigen['nombre']}" . ($concepto ? " - {$concepto}" : "");
            $this->movimientoCuentaModel->create([
                'id_cuenta'      => $destinoId,
                'id_usuario'     => $userId,
                'id_venta'       => null,
                'tipo'           => 'INGRESO',
                'concepto'       => $conceptoDestino,
                'valor'          => $valor,
                'saldo_anterior' => $saldoDestinoAnterior,
                'saldo_nuevo'    => $saldoDestinoNuevo
            ]);

            // 5. Registrar tabla de transferencia
            $transfId = $this->transferenciaModel->create($userId, $origenId, $destinoId, $valor, $concepto);

            $this->db->commit();
            return [
                'success'          => true,
                'message'          => 'Transferencia realizada con éxito.',
                'id_transferencia' => $transfId
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al realizar la transferencia: ' . $e->getMessage()];
        }
    }
}
