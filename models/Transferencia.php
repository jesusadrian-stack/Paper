<?php
require_once __DIR__ . '/../Config/database.php';

class Transferencia {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(int $userId, int $origenId, int $destinoId, float $valor, ?string $concepto): int {
        $sql = "INSERT INTO transferencia (id_usuario, id_cuenta_origen, id_cuenta_destino, valor, concepto, fecha_transferencia) 
                VALUES (:id_usuario, :id_cuenta_origen, :id_cuenta_destino, :valor, :concepto, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_usuario'        => $userId,
            'id_cuenta_origen'  => $origenId,
            'id_cuenta_destino' => $destinoId,
            'valor'             => $valor,
            'concepto'          => $concepto ?? 'Transferencia entre cuentas'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $limit = 100): array {
        $sql = "SELECT t.*, 
                       co.nombre AS cuenta_origen_nombre, co.tipo AS cuenta_origen_tipo, 
                       cd.nombre AS cuenta_destino_nombre, cd.tipo AS cuenta_destino_tipo, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM transferencia t 
                JOIN cuenta co ON t.id_cuenta_origen = co.id_cuenta 
                JOIN cuenta cd ON t.id_cuenta_destino = cd.id_cuenta 
                JOIN usuario u ON t.id_usuario = u.id_usuario 
                ORDER BY t.fecha_transferencia DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
