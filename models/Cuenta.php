<?php
require_once __DIR__ . '/../Config/database.php';

class Cuenta {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM cuenta ORDER BY tipo ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cuenta WHERE id_cuenta = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $acc = $stmt->fetch();
        return $acc ?: null;
    }

    public function getByTipo(string $tipo): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cuenta WHERE tipo = :tipo LIMIT 1");
        $stmt->execute(['tipo' => $tipo]);
        $acc = $stmt->fetch();
        return $acc ?: null;
    }

    public function updateSaldo(int $id, float $newSaldo): bool {
        if ($newSaldo < 0) {
            throw new InvalidArgumentException("El saldo de la cuenta no puede ser negativo.");
        }
        $stmt = $this->db->prepare("UPDATE cuenta SET saldo = :saldo WHERE id_cuenta = :id");
        return $stmt->execute(['saldo' => $newSaldo, 'id' => $id]);
    }
}
