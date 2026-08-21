<?php
require_once __DIR__ . '/../Config/database.php';

class RecomendacionIA {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO recomendacion_ia (id_analisis, id_producto, id_cliente, tipo, recomendacion, prioridad, fecha_recomendacion, atendida) 
                VALUES (:id_analisis, :id_producto, :id_cliente, :tipo, :recomendacion, :prioridad, NOW(), 0)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_analisis'    => $data['id_analisis'],
            'id_producto'    => $data['id_producto'] ?? null,
            'id_cliente'     => $data['id_cliente'] ?? null,
            'tipo'           => $data['tipo'],
            'recomendacion'  => $data['recomendacion'],
            'prioridad'      => $data['prioridad'] ?? 'MEDIA'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAll(bool $onlyPending = false, int $limit = 50): array {
        $sql = "SELECT r.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido 
                FROM recomendacion_ia r 
                LEFT JOIN producto p ON r.id_producto = p.id_producto 
                LEFT JOIN cliente c ON r.id_cliente = c.id_cliente 
                WHERE 1=1 ";
        
        if ($onlyPending) {
            $sql .= " AND r.atendida = 0 ";
        }

        $sql .= " ORDER BY r.atendida ASC, 
                  CASE r.prioridad 
                      WHEN 'ALTA' THEN 1 
                      WHEN 'MEDIA' THEN 2 
                      WHEN 'BAJA' THEN 3 
                      ELSE 4 
                  END, 
                  r.fecha_recomendacion DESC 
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByAnalysisId(int $analysisId): array {
        $sql = "SELECT r.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido 
                FROM recomendacion_ia r 
                LEFT JOIN producto p ON r.id_producto = p.id_producto 
                LEFT JOIN cliente c ON r.id_cliente = c.id_cliente 
                WHERE r.id_analisis = :analysisId 
                ORDER BY r.fecha_recomendacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['analysisId' => $analysisId]);
        return $stmt->fetchAll();
    }

    public function countPending(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM recomendacion_ia WHERE atendida = 0");
        return (int)$stmt->fetchColumn();
    }

    public function markAsResolved(int $id): bool {
        $stmt = $this->db->prepare("UPDATE recomendacion_ia SET atendida = 1 WHERE id_recomendacion = :id");
        return $stmt->execute(['id' => $id]);
    }
}
