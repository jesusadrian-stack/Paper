<?php // Apertura del archivo de código PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión centralizada a la base de datos (Database::getConnection)

class Categoria { // Modelo 'Categoria': gestiona la tabla 'categoria' y sus relaciones con los productos
    private PDO $db; // Objeto PDO que mantiene el enlace y conexión con el servidor MySQL

    public function __construct() { // Constructor de la clase Categoria
        $this->db = Database::getConnection(); // Conecta a la base de datos asignando la instancia a la propiedad $this->db
    } // Fin del constructor

    public function getAll(bool $onlyActive = false): array { // Método para obtener categorías, opcionalmente filtrando solo las activas
        $sql = "SELECT c.*, COUNT(p.id_producto) AS total_productos 
                FROM categoria c 
                LEFT JOIN producto p ON c.id_categoria = p.id_categoria 
                " . ($onlyActive ? "WHERE c.estado = 'ACTIVO' " : "") . "
                GROUP BY c.id_categoria 
                ORDER BY c.nombre ASC"; // Consulta SQL: Conecta la tabla 'categoria' (c) con 'producto' (p) usando la llave foránea id_categoria para contar cuántos productos tiene cada categoría
        $stmt = $this->db->query($sql); // Ejecuta la consulta SQL directamente en la base de datos
        return $stmt->fetchAll(); // Devuelve todas las categorías con el conteo de productos asociados
    } // Fin del método getAll

    public function getById(int $id): ?array { // Método para buscar una categoría por su clave primaria id_categoria
        $stmt = $this->db->prepare("SELECT * FROM categoria WHERE id_categoria = :id LIMIT 1"); // Prepara consulta parametrizada con :id
        $stmt->execute(['id' => $id]); // Ejecuta vinculando el identificador recibido
        $cat = $stmt->fetch(); // Obtiene el registro resultante
        return $cat ?: null; // Retorna los datos de la categoría o null si no existe
    } // Fin del método getById

    public function getByNombre(string $nombre): ?array { // Método para buscar una categoría por su nombre exacto
        $stmt = $this->db->prepare("SELECT * FROM categoria WHERE nombre = :nombre LIMIT 1"); // Prepara consulta filtrando por nombre
        $stmt->execute(['nombre' => $nombre]); // Ejecuta vinculando el parámetro :nombre
        $cat = $stmt->fetch(); // Extrae la fila de la categoría si existe
        return $cat ?: null; // Retorna el arreglo de la categoría o null si no se encuentra
    } // Fin del método getByNombre

    public function create(array $data): int { // Método para insertar una nueva categoría en la tabla 'categoria'
        $sql = "INSERT INTO categoria (nombre, descripcion, estado) VALUES (:nombre, :descripcion, :estado)"; // Sentencia SQL de inserción parametrizada
        $stmt = $this->db->prepare($sql); // Prepara la consulta para evitar ataques de inyección SQL
        $stmt->execute([ // Ejecuta la inserción vinculando los campos del array $data
            'nombre'      => $data['nombre'], // Nombre de la categoría
            'descripcion' => $data['descripcion'] ?? null, // Descripción opcional (si no viene, guarda null)
            'estado'      => $data['estado'] ?? 'ACTIVO' // Estado por defecto 'ACTIVO' si no se especifica
        ]); // Fin de la ejecución con parámetros
        return (int)$this->db->lastInsertId(); // Retorna el nuevo ID auto-incremental generado por la base de datos
    } // Fin del método create

    public function update(int $id, array $data): bool { // Método para actualizar la información de una categoría existente
        $sql = "UPDATE categoria SET nombre = :nombre, descripcion = :descripcion, estado = :estado WHERE id_categoria = :id"; // Sentencia SQL para actualizar campos según el id_categoria
        $stmt = $this->db->prepare($sql); // Prepara la consulta SQL
        return $stmt->execute([ // Ejecuta la actualización vinculando los valores
            'id'          => $id, // ID de la categoría a modificar
            'nombre'      => $data['nombre'], // Nuevo nombre
            'descripcion' => $data['descripcion'] ?? null, // Nueva descripción o null
            'estado'      => $data['estado'] ?? 'ACTIVO' // Nuevo estado ('ACTIVO' o 'INACTIVO')
        ]); // Retorna true si la actualización se ejecutó con éxito
    } // Fin del método update

    public function toggleStatus(int $id): bool { // Método para alternar el estado entre ACTIVO e INACTIVO de una categoría
        $cat = $this->getById($id); // Consulta la categoría para conocer su estado actual
        if (!$cat) return false; // Si la categoría no existe en la base de datos, retorna false
        $newStatus = ($cat['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO'; // Cambia de ACTIVO a INACTIVO o viceversa
        $stmt = $this->db->prepare("UPDATE categoria SET estado = :estado WHERE id_categoria = :id"); // Prepara la consulta de cambio de estado
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]); // Ejecuta el cambio y devuelve true o false
    } // Fin del método toggleStatus
} // Fin de la clase Categoria
