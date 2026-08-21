<?php
require_once __DIR__ . '/../Config/database.php';

class DetalleVenta {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(int $saleId, int $productId, int $quantity, float $unitPrice, float $subtotal): int {
        $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_venta'        => $saleId,
            'id_producto'     => $productId,
            'cantidad'        => $quantity,
            'precio_unitario' => $unitPrice,
            'subtotal'        => $subtotal
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getBySaleId(int $saleId): array {
        $sql = "SELECT dv.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, c.nombre AS categoria_nombre 
                FROM detalle_venta dv 
                JOIN producto p ON dv.id_producto = p.id_producto 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE dv.id_venta = :saleId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['saleId' => $saleId]);
        return $stmt->fetchAll();
    }

    public function getTopSellingProducts(int $limit = 5): array {
        $sql = "SELECT p.id_producto, p.codigo, p.nombre, c.nombre AS categoria_nombre, 
                       SUM(dv.cantidad) AS total_unidades, 
                       SUM(dv.subtotal) AS total_recaudado 
                FROM detalle_venta dv 
                JOIN producto p ON dv.id_producto = p.id_producto 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                JOIN venta v ON dv.id_venta = v.id_venta 
                WHERE v.estado = 'COMPLETADA' 
                GROUP BY p.id_producto 
                ORDER BY total_unidades DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
