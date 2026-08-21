<?php
require_once __DIR__ . '/../Config/database.php';

class Producto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(bool $onlyActive = false, ?int $categoryId = null, ?string $search = null): array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE 1=1 ";
        
        $params = [];
        if ($onlyActive) {
            $sql .= " AND p.estado = 'ACTIVO' ";
        }
        if ($categoryId) {
            $sql .= " AND p.id_categoria = :categoryId ";
            $params['categoryId'] = $categoryId;
        }
        if (!empty($search)) {
            $sql .= " AND (p.nombre LIKE :search1 OR p.codigo LIKE :search2 OR p.descripcion LIKE :search3) ";
            $params['search1'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }

        $sql .= " ORDER BY p.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.id_producto = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $prod = $stmt->fetch();
        return $prod ?: null;
    }

    public function getByCodigo(string $codigo): ?array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.codigo = :codigo LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        $prod = $stmt->fetch();
        return $prod ?: null;
    }

    public function getLowStockProducts(): array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.estado = 'ACTIVO' AND p.stock_actual <= p.stock_minimo 
                ORDER BY p.stock_actual ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function countTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM producto WHERE estado = 'ACTIVO'");
        return (int)$stmt->fetchColumn();
    }

    public function countLowStock(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM producto WHERE estado = 'ACTIVO' AND stock_actual <= stock_minimo");
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO producto (id_categoria, codigo, nombre, descripcion, precio, stock_actual, stock_minimo, estado, fecha_registro) 
                VALUES (:id_categoria, :codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, :estado, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_categoria' => $data['id_categoria'],
            'codigo'       => $data['codigo'],
            'nombre'       => $data['nombre'],
            'descripcion'  => $data['descripcion'] ?? null,
            'precio'       => $data['precio'],
            'stock_actual' => $data['stock_actual'] ?? 0,
            'stock_minimo' => $data['stock_minimo'] ?? 0,
            'estado'       => $data['estado'] ?? 'ACTIVO'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE producto SET 
                    id_categoria = :id_categoria, 
                    codigo = :codigo, 
                    nombre = :nombre, 
                    descripcion = :descripcion, 
                    precio = :precio, 
                    stock_minimo = :stock_minimo, 
                    estado = :estado,
                    fecha_actualizacion = NOW() 
                WHERE id_producto = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'           => $id,
            'id_categoria' => $data['id_categoria'],
            'codigo'       => $data['codigo'],
            'nombre'       => $data['nombre'],
            'descripcion'  => $data['descripcion'] ?? null,
            'precio'       => $data['precio'],
            'stock_minimo' => $data['stock_minimo'] ?? 0,
            'estado'       => $data['estado'] ?? 'ACTIVO'
        ]);
    }

    public function updateStock(int $id, int $newStock): bool {
        if ($newStock < 0) {
            throw new InvalidArgumentException("El stock no puede ser negativo.");
        }
        $sql = "UPDATE producto SET stock_actual = :stock, fecha_actualizacion = NOW() WHERE id_producto = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['stock' => $newStock, 'id' => $id]);
    }

    public function toggleStatus(int $id): bool {
        $prod = $this->getById($id);
        if (!$prod) return false;
        $newStatus = ($prod['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO';
        $stmt = $this->db->prepare("UPDATE producto SET estado = :estado, fecha_actualizacion = NOW() WHERE id_producto = :id");
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]);
    }
}
