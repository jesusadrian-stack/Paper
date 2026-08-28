<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Conecta con la clase Database encargada de la configuración y conexión PDO

class Cuenta { // Modelo 'Cuenta': administra las cuentas bancarias/financieras de la tabla 'cuenta' (ej. Bancolombia, Nequi, Daviplata, Caja)
    private PDO $db; // Variable para almacenar la conexión PDO a la base de datos MySQL

    public function __construct() { // Constructor que se ejecuta cuando creamos una nueva instancia del modelo Cuenta
        $this->db = Database::getConnection(); // Establece y conecta la instancia de base de datos activa
    } // Fin del constructor

    public function getAll(): array { // Método para obtener el listado de todas las cuentas registradas
        $stmt = $this->db->query("SELECT * FROM cuenta ORDER BY tipo ASC"); // Ejecuta consulta SQL para listar todas las cuentas ordenadas alfabéticamente por tipo
        return $stmt->fetchAll(); // Retorna todas las filas de cuentas como un arreglo de datos
    } // Fin del método getAll

    public function getById(int $id): ?array { // Método para buscar una cuenta específica por su clave primaria (id_cuenta)
        $stmt = $this->db->prepare("SELECT * FROM cuenta WHERE id_cuenta = :id LIMIT 1"); // Prepara consulta segura con parámetro :id para evitar inyección SQL
        $stmt->execute(['id' => $id]); // Ejecuta la consulta pasando el identificador recibido
        $acc = $stmt->fetch(); // Extrae la fila de la cuenta encontrada
        return $acc ?: null; // Retorna los datos de la cuenta o null si no existe
    } // Fin del método getById

    public function getByTipo(string $tipo): ?array { // Método para consultar una cuenta según su tipo (ej. 'BANCOLOMBIA', 'NEQUI')
        $stmt = $this->db->prepare("SELECT * FROM cuenta WHERE tipo = :tipo LIMIT 1"); // Prepara consulta filtrando por la columna 'tipo'
        $stmt->execute(['tipo' => $tipo]); // Ejecuta vinculando el valor de tipo
        $acc = $stmt->fetch(); // Extrae el registro de la cuenta coincidente
        return $acc ?: null; // Retorna los datos de la cuenta o null si no se halló
    } // Fin del método getByTipo

    public function updateSaldo(int $id, float $newSaldo): bool { // Método para actualizar el saldo disponible de una cuenta específica
        if ($newSaldo < 0) { // Valida como regla de negocio que una cuenta no tenga saldo menor a cero
            throw new InvalidArgumentException("El saldo de la cuenta no puede ser negativo."); // Lanza excepción de error si el saldo es negativo
        } // Fin de validación
        $stmt = $this->db->prepare("UPDATE cuenta SET saldo = :saldo WHERE id_cuenta = :id"); // Prepara la actualización del campo 'saldo' conectando con id_cuenta
        return $stmt->execute(['saldo' => $newSaldo, 'id' => $id]); // Ejecuta el UPDATE con los nuevos valores y retorna true si fue exitoso
    } // Fin del método updateSaldo
} // Fin de la clase Cuenta
