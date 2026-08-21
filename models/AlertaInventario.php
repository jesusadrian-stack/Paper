<?php
require_once __DIR__ . '/../Config/database.php';

class AlertaInventario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(int $productId, int $currentStock, int $minStock, string $mensaje): int {
        // Verificar si ya existe una alerta activa para este producto
        $stmtCheck = $this->db->prepare("SELECT id_alerta FROM alerta_inventario WHERE id_producto = :prodId AND atendida = 0 LIMIT 1");
        $stmtCheck->execute(['prodId' => $productId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            // Actualizar mensaje y stock
            $stmtUpdate = $this->db->prepare("UPDATE alerta_inventario SET stock_actual = :curr, stock_minimo = :min, mensaje = :msg, fecha_alerta = NOW() WHERE id_alerta = :id");
            $stmtUpdate->execute([
                'curr' => $currentStock,
                'min'  => $minStock,
                'msg'  => $mensaje,
                'id'   => $existing['id_alerta']
            ]);
            return (int)$existing['id_alerta'];
        }

        $sql = "INSERT INTO alerta_inventario (id_producto, stock_actual, stock_minimo, mensaje, atendida, fecha_alerta) 
                VALUES (:id_producto, :stock_actual, :stock_minimo, :mensaje, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_producto'  => $productId,
            'stock_actual' => $currentStock,
            'stock_minimo' => $minStock,
            'mensaje'      => $mensaje
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAll(bool $onlyPending = false): array {
        $sql = "SELECT a.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, c.nombre AS categoria_nombre 
                FROM alerta_inventario a 
                JOIN producto p ON a.id_producto = p.id_producto 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE 1=1 ";
        
        if ($onlyPending) {
            $sql .= " AND a.atendida = 0 ";
        }

        $sql .= " ORDER BY a.atendida ASC, a.fecha_alerta DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function countPending(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM alerta_inventario WHERE atendida = 0");
        return (int)$stmt->fetchColumn();
    }

    public function markAsResolved(int $id): bool {
        $stmt = $this->db->prepare("UPDATE alerta_inventario SET atendida = 1 WHERE id_alerta = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function resolveByProductIfRestocked(int $productId, int $newStock): void {
        $stmt = $this->db->prepare("SELECT stock_minimo FROM producto WHERE id_producto = :id");
        $stmt->execute(['id' => $productId]);
        $minStock = (int)$stmt->fetchColumn();

        if ($newStock > $minStock) {
            $update = $this->db->prepare("UPDATE alerta_inventario SET atendida = 1 WHERE id_producto = :id AND atendida = 0");
            $update->execute(['id' => $productId]);
        }
    }
}
