<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión centralizada a la base de datos MySQL

class HistorialPrecio { // Modelo 'HistorialPrecio': registra y audita los cambios de precio en la tabla 'historial_precio'
    private PDO $db; // Variable para almacenar la conexión PDO activa

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Obtiene la instancia de la base de datos
    } // Fin del constructor

    public function create(int $productId, int $userId, ?float $oldPrice, float $newPrice): int { // Registra una traza de cambio de precio vinculando producto y usuario
        $sql = "INSERT INTO historial_precio (id_producto, id_usuario, precio_anterior, precio_nuevo, fecha_cambio) 
                VALUES (:id_producto, :id_usuario, :precio_anterior, :precio_nuevo, NOW())"; // Sentencia SQL: Llaves foráneas que conectan el producto modificado (id_producto) y el usuario responsable (id_usuario)
        $stmt = $this->db->prepare($sql); // Prepara la consulta para ejecución segura
        $stmt->execute([ // Ejecuta la inserción con los parámetros vinculados
            'id_producto'     => $productId, // Llave foránea que apunta a la tabla 'producto'
            'id_usuario'      => $userId, // Llave foránea que apunta a la tabla 'usuario' (quién hizo el cambio)
            'precio_anterior' => $oldPrice, // Precio antes de la modificación
            'precio_nuevo'    => $newPrice // Nuevo precio asignado
        ]); // Fin de la ejecución con parámetros
        return (int)$this->db->lastInsertId(); // Retorna el ID autoincrementable del registro en el historial
    } // Fin del método create

    public function getByProductId(int $productId): array { // Obtiene el historial de variaciones de precio de un producto específico
        $sql = "SELECT hp.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM historial_precio hp 
                JOIN usuario u ON hp.id_usuario = u.id_usuario 
                WHERE hp.id_producto = :productId 
                ORDER BY hp.fecha_cambio DESC"; // Conexión relacional: JOIN con 'usuario' mediante id_usuario para mostrar qué persona modificó el precio de este producto
        $stmt = $this->db->prepare($sql); // Prepara la consulta parametrizada
        $stmt->execute(['productId' => $productId]); // Ejecuta vinculando el id del producto consultado
        return $stmt->fetchAll(); // Retorna el listado cronológico de cambios de precio para ese producto
    } // Fin del método getByProductId

    public function getAll(int $limit = 100): array { // Obtiene el historial global de precios de todos los productos
        $sql = "SELECT hp.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM historial_precio hp 
                JOIN producto p ON hp.id_producto = p.id_producto 
                JOIN usuario u ON hp.id_usuario = u.id_usuario 
                ORDER BY hp.fecha_cambio DESC 
                LIMIT :limit"; // Conexión relacional doble: JOIN con 'producto' (hp.id_producto = p.id_producto) y JOIN con 'usuario' (hp.id_usuario = u.id_usuario) para tener detalle del artículo y del autor
        $stmt = $this->db->prepare($sql); // Prepara la consulta para evitar SQL injection
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Asigna el límite como número entero
        $stmt->execute(); // Ejecuta la consulta
        return $stmt->fetchAll(); // Devuelve el historial con nombres de producto y usuario
    } // Fin del método getAll
} // Fin de la clase HistorialPrecio
