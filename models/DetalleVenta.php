<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión PDO con la base de datos

class DetalleVenta { // Modelo 'DetalleVenta': gestiona los productos individuales vendidos en cada transacción dentro de la tabla 'detalle_venta'
    private PDO $db; // Variable para almacenar el enlace con la base de datos

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Conecta con la base de datos a través de Database
    } // Fin del constructor

    public function create(int $saleId, int $productId, int $quantity, float $unitPrice, float $subtotal): int { // Inserta un ítem vendido asociándolo a su venta y producto
        $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal)"; // Sentencia SQL: Conecta la venta padre (id_venta) con el producto vendido (id_producto)
        $stmt = $this->db->prepare($sql); // Prepara la sentencia SQL
        $stmt->execute([ // Ejecuta la inserción con los parámetros
            'id_venta'        => $saleId, // Llave foránea vinculada a la tabla 'venta' (encabezado de la factura)
            'id_producto'     => $productId, // Llave foránea vinculada a la tabla 'producto' (artículo vendido)
            'cantidad'        => $quantity, // Cantidad de unidades vendidas
            'precio_unitario' => $unitPrice, // Precio por unidad al momento de la venta
            'subtotal'        => $subtotal // Total por este ítem (cantidad * precio_unitario)
        ]); // Fin de la ejecución con parámetros
        return (int)$this->db->lastInsertId(); // Retorna el id_detalle_venta generado
    } // Fin del método create

    public function getBySaleId(int $saleId): array { // Obtiene todos los artículos que pertenecen a una venta específica
        $sql = "SELECT dv.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, c.nombre AS categoria_nombre 
                FROM detalle_venta dv 
                JOIN producto p ON dv.id_producto = p.id_producto 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE dv.id_venta = :saleId"; // Conexión relacional múltiple: Une 'detalle_venta' con 'producto' (dv.id_producto = p.id_producto) y 'producto' con 'categoria' (p.id_categoria = c.id_categoria) para traer nombre, código y categoría de cada producto vendido
        $stmt = $this->db->prepare($sql); // Prepara la consulta para evitar inyección SQL
        $stmt->execute(['saleId' => $saleId]); // Ejecuta vinculando el ID de la venta consultada
        return $stmt->fetchAll(); // Retorna la lista de productos que componen la venta
    } // Fin del método getBySaleId

    public function getTopSellingProducts(int $limit = 5): array { // Obtiene los productos más vendidos del negocio
        $sql = "SELECT p.id_producto, p.codigo, p.nombre, c.nombre AS categoria_nombre, 
                       SUM(dv.cantidad) AS total_unidades, 
                       SUM(dv.subtotal) AS total_recaudado 
                FROM detalle_venta dv 
                JOIN producto p ON dv.id_producto = p.id_producto 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                JOIN venta v ON dv.id_venta = v.id_venta 
                WHERE v.estado = 'COMPLETADA' 
                GROUP BY p.id_producto 
                ORDER BY total_unidades DESC 
                LIMIT :limit"; // Conexión relacional triple: Une 'detalle_venta' con 'producto', 'categoria' y 'venta' para totalizar unidades e ingresos solo de ventas con estado COMPLETADA
        $stmt = $this->db->prepare($sql); // Prepara la consulta de agregación
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Asigna el límite numérico como entero
        $stmt->execute(); // Ejecuta la consulta
        return $stmt->fetchAll(); // Retorna el ranking de productos más vendidos
    } // Fin del método getTopSellingProducts
} // Fin de la clase DetalleVenta
