<?php
require_once __DIR__ . '/../Config/database.php';

class Categoria {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(bool $onlyActive = false): array {
        $sql = "SELECT c.*, COUNT(p.id_producto) AS total_productos 
                FROM categoria c 
                LEFT JOIN producto p ON c.id_categoria = p.id_categoria 
                " . ($onlyActive ? "WHERE c.estado = 'ACTIVO' " : "") . "
                GROUP BY c.id_categoria 
                ORDER BY c.nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categoria WHERE id_categoria = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }

    public function getByNombre(string $nombre): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categoria WHERE nombre = :nombre LIMIT 1");
        $stmt->execute(['nombre' => $nombre]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO categoria (nombre, descripcion, estado) VALUES (:nombre, :descripcion, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'estado'      => $data['estado'] ?? 'ACTIVO'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE categoria SET nombre = :nombre, descripcion = :descripcion, estado = :estado WHERE id_categoria = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'          => $id,
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'estado'      => $data['estado'] ?? 'ACTIVO'
        ]);
    }

    public function toggleStatus(int $id): bool {
        $cat = $this->getById($id);
        if (!$cat) return false;
        $newStatus = ($cat['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO';
        $stmt = $this->db->prepare("UPDATE categoria SET estado = :estado WHERE id_categoria = :id");
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]);
    }
}
