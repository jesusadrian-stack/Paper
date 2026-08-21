<?php
require_once __DIR__ . '/../Config/database.php';

class AnalisisIA {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(?int $userId, string $tipo, string $titulo, string $resultado): int {
        $sql = "INSERT INTO analisis_ia (id_usuario, tipo, titulo, resultado, fecha_analisis) 
                VALUES (:id_usuario, :tipo, :titulo, :resultado, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_usuario' => $userId,
            'tipo'       => $tipo,
            'titulo'     => $titulo,
            'resultado'  => $resultado
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $limit = 50): array {
        $sql = "SELECT a.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido,
                       (SELECT COUNT(*) FROM recomendacion_ia r WHERE r.id_analisis = a.id_analisis) AS total_recomendaciones 
                FROM analisis_ia a 
                LEFT JOIN usuario u ON a.id_usuario = u.id_usuario 
                ORDER BY a.fecha_analisis DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $sql = "SELECT a.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM analisis_ia a 
                LEFT JOIN usuario u ON a.id_usuario = u.id_usuario 
                WHERE a.id_analisis = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $analysis = $stmt->fetch();
        return $analysis ?: null;
    }
}
