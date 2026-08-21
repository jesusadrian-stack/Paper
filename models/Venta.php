<?php
require_once __DIR__ . '/../Config/database.php';

class Venta {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(?string $fechaInicio = null, ?string $fechaFin = null, ?int $userId = null, int $limit = 200): array {
        $sql = "SELECT v.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.numero_identificacion 
                FROM venta v 
                JOIN usuario u ON v.id_usuario = u.id_usuario 
                LEFT JOIN cliente c ON v.id_cliente = c.id_cliente 
                WHERE 1=1 ";
        
        $params = [];
        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(v.fecha_venta) >= :fechaInicio ";
            $params['fechaInicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND DATE(v.fecha_venta) <= :fechaFin ";
            $params['fechaFin'] = $fechaFin;
        }
        if ($userId) {
            $sql .= " AND v.id_usuario = :userId ";
            $params['userId'] = $userId;
        }

        $sql .= " ORDER BY v.fecha_venta DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $sql = "SELECT v.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.numero_identificacion, c.telefono, c.direccion 
                FROM venta v 
                JOIN usuario u ON v.id_usuario = u.id_usuario 
                LEFT JOIN cliente c ON v.id_cliente = c.id_cliente 
                WHERE v.id_venta = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $sale = $stmt->fetch();
        return $sale ?: null;
    }

    public function getTodayStats(): array {
        $sql = "SELECT COUNT(*) AS total_ventas, COALESCE(SUM(total), 0) AS total_ingresos 
                FROM venta 
                WHERE DATE(fecha_venta) = CURDATE() AND estado = 'COMPLETADA'";
        $stmt = $this->db->query($sql);
        return $stmt->fetch() ?: ['total_ventas' => 0, 'total_ingresos' => 0];
    }

    public function getSalesLastDays(int $days = 7): array {
        $sql = "SELECT DATE(fecha_venta) AS fecha, COUNT(*) AS cantidad, COALESCE(SUM(total), 0) AS total 
                FROM venta 
                WHERE estado = 'COMPLETADA' AND fecha_venta >= DATE_SUB(CURDATE(), INTERVAL :days DAY) 
                GROUP BY DATE(fecha_venta) 
                ORDER BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(int $userId, ?int $clientId, float $subtotal, float $total): int {
        $sql = "INSERT INTO venta (id_usuario, id_cliente, subtotal, total, estado, fecha_venta) 
                VALUES (:id_usuario, :id_cliente, :subtotal, :total, 'COMPLETADA', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_usuario' => $userId,
            'id_cliente' => $clientId,
            'subtotal'   => $subtotal,
            'total'      => $total
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function cancel(int $id): bool {
        $stmt = $this->db->prepare("UPDATE venta SET estado = 'CANCELADA' WHERE id_venta = :id");
        return $stmt->execute(['id' => $id]);
    }
}
