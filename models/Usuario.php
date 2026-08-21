<?php
require_once __DIR__ . '/../Config/database.php';

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT u.*, r.nombre AS rol_nombre 
                FROM usuario u 
                JOIN rol r ON u.id_rol = r.id_rol 
                ORDER BY u.id_usuario DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $sql = "SELECT u.*, r.nombre AS rol_nombre 
                FROM usuario u 
                JOIN rol r ON u.id_rol = r.id_rol 
                WHERE u.id_usuario = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function getByUsername(string $username): ?array {
        $sql = "SELECT u.*, r.nombre AS rol_nombre 
                FROM usuario u 
                JOIN rol r ON u.id_rol = r.id_rol 
                WHERE (u.nombre_usuario = :username OR u.correo = :correo) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username, 'correo' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function getByCorreo(string $correo): ?array {
        $sql = "SELECT * FROM usuario WHERE correo = :correo LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function getByDocumento(string $documento): ?array {
        $sql = "SELECT * FROM usuario WHERE documento = :documento LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['documento' => $documento]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO usuario (id_rol, nombre, apellido, documento, telefono, correo, nombre_usuario, contrasena, estado, fecha_registro) 
                VALUES (:id_rol, :nombre, :apellido, :documento, :telefono, :correo, :nombre_usuario, :contrasena, :estado, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_rol'         => $data['id_rol'],
            'nombre'         => $data['nombre'],
            'apellido'       => $data['apellido'],
            'documento'      => $data['documento'],
            'telefono'       => $data['telefono'] ?? null,
            'correo'         => $data['correo'] ?? null,
            'nombre_usuario' => $data['nombre_usuario'],
            'contrasena'     => password_hash($data['contrasena'], PASSWORD_BCRYPT),
            'estado'         => $data['estado'] ?? 'ACTIVO'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [
            'id_rol = :id_rol',
            'nombre = :nombre',
            'apellido = :apellido',
            'documento = :documento',
            'telefono = :telefono',
            'correo = :correo',
            'nombre_usuario = :nombre_usuario',
            'estado = :estado'
        ];

        $params = [
            'id'             => $id,
            'id_rol'         => $data['id_rol'],
            'nombre'         => $data['nombre'],
            'apellido'       => $data['apellido'],
            'documento'      => $data['documento'],
            'telefono'       => $data['telefono'] ?? null,
            'correo'         => $data['correo'] ?? null,
            'nombre_usuario' => $data['nombre_usuario'],
            'estado'         => $data['estado'] ?? 'ACTIVO'
        ];

        if (!empty($data['contrasena'])) {
            $fields[] = 'contrasena = :contrasena';
            $params['contrasena'] = password_hash($data['contrasena'], PASSWORD_BCRYPT);
        }

        $sql = "UPDATE usuario SET " . implode(', ', $fields) . " WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateLastAccess(int $id): bool {
        $stmt = $this->db->prepare("UPDATE usuario SET ultimo_acceso = NOW() WHERE id_usuario = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function toggleStatus(int $id): bool {
        $user = $this->getById($id);
        if (!$user) return false;

        $newStatus = ($user['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO';
        $stmt = $this->db->prepare("UPDATE usuario SET estado = :estado WHERE id_usuario = :id");
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]);
    }
}
