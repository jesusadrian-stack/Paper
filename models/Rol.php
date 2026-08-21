<?php
require_once __DIR__ . '/../Config/database.php';

class Rol {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM rol ORDER BY id_rol ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM rol WHERE id_rol = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $rol = $stmt->fetch();
        return $rol ?: null;
    }

    public function getByNombre(string $nombre): ?array {
        $stmt = $this->db->prepare("SELECT * FROM rol WHERE nombre = :nombre LIMIT 1");
        $stmt->execute(['nombre' => $nombre]);
        $rol = $stmt->fetch();
        return $rol ?: null;
    }
}
