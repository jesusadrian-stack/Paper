<?php
require_once __DIR__ . '/../Config/database.php';

class HistorialPrecio {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(int $productId, int $userId, ?float $oldPrice, float $newPrice): int {
        $sql = "INSERT INTO historial_precio (id_producto, id_usuario, precio_anterior, precio_nuevo, fecha_cambio) 
                VALUES (:id_producto, :id_usuario, :precio_anterior, :precio_nuevo, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_producto'     => $productId,
            'id_usuario'      => $userId,
            'precio_anterior' => $oldPrice,
            'precio_nuevo'    => $newPrice
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getByProductId(int $productId): array {
        $sql = "SELECT hp.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM historial_precio hp 
                JOIN usuario u ON hp.id_usuario = u.id_usuario 
                WHERE hp.id_producto = :productId 
                ORDER BY hp.fecha_cambio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll();
    }

    public function getAll(int $limit = 100): array {
        $sql = "SELECT hp.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM historial_precio hp 
                JOIN producto p ON hp.id_producto = p.id_producto 
                JOIN usuario u ON hp.id_usuario = u.id_usuario 
                ORDER BY hp.fecha_cambio DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
