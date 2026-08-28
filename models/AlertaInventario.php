<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión centralizada PDO a la base de datos

class AlertaInventario { // Modelo 'AlertaInventario': gestiona las notificaciones de stock bajo o agotado en la tabla 'alerta_inventario'
    private PDO $db; // Variable para almacenar el enlace con la base de datos MySQL

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Inicializa la conexión mediante el singleton Database
    } // Fin del constructor

    public function create(int $productId, int $currentStock, int $minStock, string $mensaje): int { // Crea o actualiza una alerta de inventario vinculada a un producto
        // Verificar si ya existe una alerta activa para este producto
        $stmtCheck = $this->db->prepare("SELECT id_alerta FROM alerta_inventario WHERE id_producto = :prodId AND atendida = 0 LIMIT 1"); // Conexión: Busca en 'alerta_inventario' por la llave foránea id_producto si hay una alerta pendiente (atendida = 0)
        $stmtCheck->execute(['prodId' => $productId]); // Ejecuta la comprobación con el ID del producto
        $existing = $stmtCheck->fetch(); // Obtiene el registro si ya existía una alerta activa

        if ($existing) { // Si ya existía una alerta pendiente para este producto
            // Actualizar mensaje y stock
            $stmtUpdate = $this->db->prepare("UPDATE alerta_inventario SET stock_actual = :curr, stock_minimo = :min, mensaje = :msg, fecha_alerta = NOW() WHERE id_alerta = :id"); // Actualiza la alerta existente con la fecha actual y nuevos valores de stock
            $stmtUpdate->execute([ // Ejecuta la actualización de la alerta
                'curr' => $currentStock, // Stock actual reportado
                'min'  => $minStock, // Stock mínimo configurado
                'msg'  => $mensaje, // Mensaje descriptivo de la alerta
                'id'   => $existing['id_alerta'] // Identificador de la alerta encontrada
            ]); // Fin de ejecución
            return (int)$existing['id_alerta']; // Retorna el ID de la alerta actualizada
        } // Fin del bloque if

        $sql = "INSERT INTO alerta_inventario (id_producto, stock_actual, stock_minimo, mensaje, atendida, fecha_alerta) 
                VALUES (:id_producto, :stock_actual, :stock_minimo, :mensaje, 0, NOW())"; // Sentencia SQL: Inserta nueva alerta vinculada al producto mediante id_producto con estado atendida = 0
        $stmt = $this->db->prepare($sql); // Prepara la inserción
        $stmt->execute([ // Ejecuta la inserción con los datos
            'id_producto'  => $productId, // Llave foránea que referencia a la tabla 'producto'
            'stock_actual' => $currentStock, // Stock actual
            'stock_minimo' => $minStock, // Stock mínimo
            'mensaje'      => $mensaje // Motivo de la alerta
        ]); // Fin de ejecución

        return (int)$this->db->lastInsertId(); // Retorna el nuevo id_alerta generado
    } // Fin del método create

    public function getAll(bool $onlyPending = false): array { // Obtiene el listado de alertas de stock uniendo con producto y categoría
        $sql = "SELECT a.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, c.nombre AS categoria_nombre 
                FROM alerta_inventario a 
                JOIN producto p ON a.id_producto = p.id_producto 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE 1=1 "; // Conexión relacional múltiple: Une 'alerta_inventario' con 'producto' (a.id_producto = p.id_producto) y 'producto' con 'categoria' (p.id_categoria = c.id_categoria) para mostrar nombre, código y categoría del artículo
        
        if ($onlyPending) { // Condicional: si solo se solicitan alertas no atendidas
            $sql .= " AND a.atendida = 0 "; // Filtra por atendida = 0
        } // Fin condición

        $sql .= " ORDER BY a.atendida ASC, a.fecha_alerta DESC"; // Ordena primero las pendientes y luego por fecha más reciente
        $stmt = $this->db->query($sql); // Ejecuta la consulta
        return $stmt->fetchAll(); // Retorna todas las alertas con los datos del producto
    } // Fin del método getAll

    public function countPending(): int { // Cuenta el número de alertas de stock pendientes de atención
        $stmt = $this->db->query("SELECT COUNT(*) FROM alerta_inventario WHERE atendida = 0"); // Consulta SQL: cuenta filas donde atendida = 0
        return (int)$stmt->fetchColumn(); // Retorna el número total de alertas activas
    } // Fin del método countPending

    public function markAsResolved(int $id): bool { // Marca una alerta específica como atendida o resuelta
        $stmt = $this->db->prepare("UPDATE alerta_inventario SET atendida = 1 WHERE id_alerta = :id"); // Sentencia SQL: actualiza atendida = 1 para el id_alerta dado
        return $stmt->execute(['id' => $id]); // Ejecuta y retorna true si se actualizó
    } // Fin del método markAsResolved

    public function resolveByProductIfRestocked(int $productId, int $newStock): void { // Resuelve automáticamente alertas de un producto si su stock se restableció
        $stmt = $this->db->prepare("SELECT stock_minimo FROM producto WHERE id_producto = :id"); // Conexión: Consulta el stock mínimo configurado en la tabla 'producto' para ese id_producto
        $stmt->execute(['id' => $productId]); // Ejecuta la consulta
        $minStock = (int)$stmt->fetchColumn(); // Obtiene el valor del stock mínimo

        if ($newStock > $minStock) { // Comprueba si el nuevo stock supera el mínimo requerido
            $update = $this->db->prepare("UPDATE alerta_inventario SET atendida = 1 WHERE id_producto = :id AND atendida = 0"); // Marca como atendidas las alertas activas de ese producto
            $update->execute(['id' => $productId]); // Ejecuta la resolución automática
        } // Fin comprobación de stock
    } // Fin del método resolveByProductIfRestocked
} // Fin de la clase AlertaInventario
