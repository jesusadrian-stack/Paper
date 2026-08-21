<?php
require_once __DIR__ . '/../Config/database.php';

class MovimientoCuenta {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO movimiento_cuenta (id_cuenta, id_usuario, id_venta, tipo, concepto, valor, saldo_anterior, saldo_nuevo, fecha_movimiento) 
                VALUES (:id_cuenta, :id_usuario, :id_venta, :tipo, :concepto, :valor, :saldo_anterior, :saldo_nuevo, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_cuenta'      => $data['id_cuenta'],
            'id_usuario'     => $data['id_usuario'],
            'id_venta'       => $data['id_venta'] ?? null,
            'tipo'           => $data['tipo'], // INGRESO, EGRESO, DEPOSITO, RETIRO
            'concepto'       => $data['concepto'],
            'valor'          => $data['valor'],
            'saldo_anterior' => $data['saldo_anterior'],
            'saldo_nuevo'    => $data['saldo_nuevo']
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAll(?int $cuentaId = null, ?string $tipo = null, ?string $fechaInicio = null, ?string $fechaFin = null, int $limit = 200): array {
        $sql = "SELECT mc.*, c.nombre AS cuenta_nombre, c.tipo AS cuenta_tipo, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM movimiento_cuenta mc 
                JOIN cuenta c ON mc.id_cuenta = c.id_cuenta 
                JOIN usuario u ON mc.id_usuario = u.id_usuario 
                WHERE 1=1 ";
        
        $params = [];
        if ($cuentaId) {
            $sql .= " AND mc.id_cuenta = :cuentaId ";
            $params['cuentaId'] = $cuentaId;
        }
        if (!empty($tipo)) {
            $sql .= " AND mc.tipo = :tipo ";
            $params['tipo'] = $tipo;
        }
        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(mc.fecha_movimiento) >= :fechaInicio ";
            $params['fechaInicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND DATE(mc.fecha_movimiento) <= :fechaFin ";
            $params['fechaFin'] = $fechaFin;
        }

        $sql .= " ORDER BY mc.fecha_movimiento DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
