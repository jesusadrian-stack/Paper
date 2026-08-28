<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Conexión con la configuración de la base de datos (carga la clase Database)

class Rol { // Definición del modelo 'Rol', que gestiona las operaciones de la tabla 'rol' en la base de datos
    private PDO $db; // Propiedad privada que almacena la instancia de conexión PDO a la base de datos

    public function __construct() { // Constructor de la clase: se ejecuta al instanciar el modelo
        $this->db = Database::getConnection(); // Obtiene y asigna la conexión activa a la base de datos mediante el método estático
    } // Cierre del constructor

    public function getAll(): array { // Método para obtener todos los roles registrados en el sistema
        $stmt = $this->db->query("SELECT * FROM rol ORDER BY id_rol ASC"); // Ejecuta consulta directa para traer todos los roles ordenados por ID de forma ascendente
        return $stmt->fetchAll(); // Retorna la lista completa de roles como un arreglo asociativo
    } // Cierre del método getAll

    public function getById(int $id): ?array { // Método para buscar un rol específico por su identificador único (id_rol)
        $stmt = $this->db->prepare("SELECT * FROM rol WHERE id_rol = :id LIMIT 1"); // Prepara la consulta parametrizada para buscar por ID y evitar inyecciones SQL
        $stmt->execute(['id' => $id]); // Ejecuta la consulta vinculando el parámetro ':id' con el valor recibido
        $rol = $stmt->fetch(); // Obtiene la primera fila resultante de la base de datos
        return $rol ?: null; // Retorna el arreglo con los datos del rol si existe, o null si no se encontró
    } // Cierre del método getById

    public function getByNombre(string $nombre): ?array { // Método para buscar un rol por su nombre (ej. 'ADMINISTRADOR', 'EMPLEADO')
        $stmt = $this->db->prepare("SELECT * FROM rol WHERE nombre = :nombre LIMIT 1"); // Prepara la consulta parametrizada filtrando por la columna 'nombre'
        $stmt->execute(['nombre' => $nombre]); // Ejecuta la consulta vinculando el parámetro ':nombre'
        $rol = $stmt->fetch(); // Obtiene el registro encontrado
        return $rol ?: null; // Retorna los datos del rol o null si no existe
    } // Cierre del método getByNombre
} // Cierre de la clase Rol
