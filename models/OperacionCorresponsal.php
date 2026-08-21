<?php
require_once __DIR__ . '/../Config/database.php';

class OperacionCorresponsal {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO operacion_corresponsal (id_usuario, id_cliente, id_cuenta, tipo, valor, referencia, descripcion, fecha_operacion) 
                VALUES (:id_usuario, :id_cliente, :id_cuenta, :tipo, :valor, :referencia, :descripcion, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_usuario'  => $data['id_usuario'],
            'id_cliente'  => $data['id_cliente'] ?? null,
            'id_cuenta'   => $data['id_cuenta'],
            'tipo'        => $data['tipo'], // DEPOSITO, RETIRO
            'valor'       => $data['valor'],
            'referencia'  => $data['referencia'] ?? null,
            'descripcion' => $data['descripcion'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAll(?string $tipo = null, ?string $fechaInicio = null, ?string $fechaFin = null, int $limit = 200): array {
        $sql = "SELECT oc.*, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.numero_identificacion 
                FROM operacion_corresponsal oc 
                JOIN usuario u ON oc.id_usuario = u.id_usuario 
                LEFT JOIN cliente c ON oc.id_cliente = c.id_cliente 
                WHERE 1=1 ";
        
        $params = [];
        if (!empty($tipo)) {
            $sql .= " AND oc.tipo = :tipo ";
            $params['tipo'] = $tipo;
        }
        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(oc.fecha_operacion) >= :fechaInicio ";
            $params['fechaInicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND DATE(oc.fecha_operacion) <= :fechaFin ";
            $params['fechaFin'] = $fechaFin;
        }

        $sql .= " ORDER BY oc.fecha_operacion DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTodayStats(): array {
        $sql = "SELECT 
                    COUNT(*) AS total_operaciones, 
                    COALESCE(SUM(CASE WHEN tipo = 'DEPOSITO' THEN valor ELSE 0 END), 0) AS total_depositos, 
                    COALESCE(SUM(CASE WHEN tipo = 'RETIRO' THEN valor ELSE 0 END), 0) AS total_retiros 
                FROM operacion_corresponsal 
                WHERE DATE(fecha_operacion) = CURDATE()";
        $stmt = $this->db->query($sql);
        return $stmt->fetch() ?: ['total_operaciones' => 0, 'total_depositos' => 0, 'total_retiros' => 0];
    }
}
