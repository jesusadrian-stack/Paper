<?php
require_once __DIR__ . '/../Config/database.php';

class Cliente {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(bool $onlyActive = false, ?string $search = null): array {
        $sql = "SELECT * FROM cliente WHERE 1=1 ";
        $params = [];

        if ($onlyActive) {
            $sql .= " AND estado = 'ACTIVO' ";
        }
        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :s1 OR apellido LIKE :s2 OR numero_identificacion LIKE :s3 OR telefono LIKE :s4) ";
            $params['s1'] = "%{$search}%";
            $params['s2'] = "%{$search}%";
            $params['s3'] = "%{$search}%";
            $params['s4'] = "%{$search}%";
        }

        $sql .= " ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE id_cliente = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $client = $stmt->fetch();
        return $client ?: null;
    }

    public function getByIdentificacion(string $doc): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE numero_identificacion = :doc LIMIT 1");
        $stmt->execute(['doc' => $doc]);
        $client = $stmt->fetch();
        return $client ?: null;
    }

    public function countTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM cliente WHERE estado = 'ACTIVO'");
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        $sql = "INSERT INTO cliente (tipo_identificacion, numero_identificacion, nombre, apellido, telefono, correo, direccion, estado, fecha_registro) 
                VALUES (:tipo, :num, :nombre, :apellido, :telefono, :correo, :direccion, :estado, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'tipo'      => $data['tipo_identificacion'],
            'num'       => $data['numero_identificacion'],
            'nombre'    => $data['nombre'],
            'apellido'  => $data['apellido'] ?? null,
            'telefono'  => $data['telefono'] ?? null,
            'correo'    => $data['correo'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'estado'    => $data['estado'] ?? 'ACTIVO'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE cliente SET 
                    tipo_identificacion = :tipo, 
                    numero_identificacion = :num, 
                    nombre = :nombre, 
                    apellido = :apellido, 
                    telefono = :telefono, 
                    correo = :correo, 
                    direccion = :direccion, 
                    estado = :estado 
                WHERE id_cliente = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'        => $id,
            'tipo'      => $data['tipo_identificacion'],
            'num'       => $data['numero_identificacion'],
            'nombre'    => $data['nombre'],
            'apellido'  => $data['apellido'] ?? null,
            'telefono'  => $data['telefono'] ?? null,
            'correo'    => $data['correo'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'estado'    => $data['estado'] ?? 'ACTIVO'
        ]);
    }

    public function toggleStatus(int $id): bool {
        $client = $this->getById($id);
        if (!$client) return false;
        $newStatus = ($client['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO';
        $stmt = $this->db->prepare("UPDATE cliente SET estado = :estado WHERE id_cliente = :id");
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]);
    }
}
