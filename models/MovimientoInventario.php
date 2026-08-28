<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión a la base de datos

class MovimientoInventario { // Modelo 'MovimientoInventario': audita entradas, salidas y ajustes de stock en la tabla 'movimiento_inventario'
    private PDO $db; // Variable para gestionar la conexión PDO a MySQL

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Conecta a la base de datos activa
    } // Fin del constructor

    public function create(array $data): int { // Registra un movimiento en inventario conectando producto y usuario
        $sql = "INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, stock_anterior, stock_nuevo, motivo, fecha_movimiento) 
                VALUES (:id_producto, :id_usuario, :tipo, :cantidad, :stock_anterior, :stock_nuevo, :motivo, NOW())"; // Sentencia SQL: Vincula el producto afectado (id_producto) y el usuario responsable (id_usuario)
        
        $stmt = $this->db->prepare($sql); // Prepara la consulta para evitar ataques de inyección SQL
        $stmt->execute([ // Ejecuta la inserción con los valores proporcionados
            'id_producto'    => $data['id_producto'], // Llave foránea que referencia a la tabla 'producto'
            'id_usuario'     => $data['id_usuario'], // Llave foránea que referencia a la tabla 'usuario'
            'tipo'           => $data['tipo'], // Tipo de movimiento ('ENTRADA', 'SALIDA', 'AJUSTE')
            'cantidad'       => $data['cantidad'], // Cantidad de unidades que entraron o salieron
            'stock_anterior' => $data['stock_anterior'], // Stock disponible antes del movimiento
            'stock_nuevo'    => $data['stock_nuevo'], // Stock resultante tras el movimiento
            'motivo'         => $data['motivo'] ?? null // Motivo explicativo o justificación opcional
        ]); // Fin de la ejecución con parámetros

        return (int)$this->db->lastInsertId(); // Retorna el ID autogenerado del movimiento de inventario
    } // Fin del método create

    public function getAll(?int $productId = null, ?string $tipo = null, ?string $fechaInicio = null, ?string $fechaFin = null, int $limit = 200): array { // Obtiene el historial de movimientos con filtros opcionales
        $sql = "SELECT mi.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM movimiento_inventario mi 
                JOIN producto p ON mi.id_producto = p.id_producto 
                JOIN usuario u ON mi.id_usuario = u.id_usuario 
                WHERE 1=1 "; // Conexión relacional: JOIN con 'producto' (mi.id_producto = p.id_producto) para nombre/código del producto y JOIN con 'usuario' (mi.id_usuario = u.id_usuario) para identificar al usuario
        
        $params = []; // Arreglo para almacenar parámetros dinámicos de filtrado
        if ($productId) { // Condicional: si se filtró por un producto específico
            $sql .= " AND mi.id_producto = :productId "; // Agrega cláusula WHERE para id_producto
            $params['productId'] = $productId; // Asigna el valor del id del producto
        } // Fin condición producto
        if (!empty($tipo)) { // Condicional: si se filtró por tipo de movimiento (ENTRADA, SALIDA, AJUSTE)
            $sql .= " AND mi.tipo = :tipo "; // Agrega cláusula WHERE para tipo
            $params['tipo'] = $tipo; // Asigna el tipo
        } // Fin condición tipo
        if (!empty($fechaInicio)) { // Condicional: si se especificó fecha inicial
            $sql .= " AND DATE(mi.fecha_movimiento) >= :fechaInicio "; // Filtra movimientos desde esta fecha
            $params['fechaInicio'] = $fechaInicio; // Asigna la fecha inicio
        } // Fin condición fechaInicio
        if (!empty($fechaFin)) { // Condicional: si se especificó fecha final
            $sql .= " AND DATE(mi.fecha_movimiento) <= :fechaFin "; // Filtra movimientos hasta esta fecha
            $params['fechaFin'] = $fechaFin; // Asigna la fecha fin
        } // Fin condición fechaFin

        $sql .= " ORDER BY mi.fecha_movimiento DESC LIMIT " . (int)$limit; // Ordena cronológicamente descendente y limita la cantidad de registros
        $stmt = $this->db->prepare($sql); // Prepara la consulta dinámica
        $stmt->execute($params); // Ejecuta con los parámetros filtrados
        return $stmt->fetchAll(); // Retorna la lista de movimientos con detalles de producto y usuario
    } // Fin del método getAll
} // Fin de la clase MovimientoInventario
