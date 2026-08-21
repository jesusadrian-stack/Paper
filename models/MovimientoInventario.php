<?php
require_once __DIR__ . '/../Config/database.php';

class MovimientoInventario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, stock_anterior, stock_nuevo, motivo, fecha_movimiento) 
                VALUES (:id_producto, :id_usuario, :tipo, :cantidad, :stock_anterior, :stock_nuevo, :motivo, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_producto'    => $data['id_producto'],
            'id_usuario'     => $data['id_usuario'],
            'tipo'           => $data['tipo'], // ENTRADA, SALIDA, AJUSTE
            'cantidad'       => $data['cantidad'],
            'stock_anterior' => $data['stock_anterior'],
            'stock_nuevo'    => $data['stock_nuevo'],
            'motivo'         => $data['motivo'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAll(?int $productId = null, ?string $tipo = null, ?string $fechaInicio = null, ?string $fechaFin = null, int $limit = 200): array {
        $sql = "SELECT mi.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM movimiento_inventario mi 
                JOIN producto p ON mi.id_producto = p.id_producto 
                JOIN usuario u ON mi.id_usuario = u.id_usuario 
                WHERE 1=1 ";
        
        $params = [];
        if ($productId) {
            $sql .= " AND mi.id_producto = :productId ";
            $params['productId'] = $productId;
        }
        if (!empty($tipo)) {
            $sql .= " AND mi.tipo = :tipo ";
            $params['tipo'] = $tipo;
        }
        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(mi.fecha_movimiento) >= :fechaInicio ";
            $params['fechaInicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND DATE(mi.fecha_movimiento) <= :fechaFin ";
            $params['fechaFin'] = $fechaFin;
        }

        $sql .= " ORDER BY mi.fecha_movimiento DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
