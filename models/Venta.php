<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión centralizada a la base de datos MySQL

class Venta { // Modelo 'Venta': administra las facturas y ventas realizadas en la tabla 'venta'
    private PDO $db; // Variable para almacenar la conexión PDO a MySQL

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Obtiene la instancia activa de la base de datos
    } // Fin del constructor

    public function getAll(?string $fechaInicio = null, ?string $fechaFin = null, ?int $userId = null, int $limit = 200): array { // Obtiene el listado de ventas con filtros de fecha y usuario
        $sql = "SELECT v.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.numero_identificacion 
                FROM venta v 
                JOIN usuario u ON v.id_usuario = u.id_usuario 
                LEFT JOIN cliente c ON v.id_cliente = c.id_cliente 
                WHERE 1=1 "; // Conexión relacional: JOIN obligatorio con 'usuario' (v.id_usuario = u.id_usuario) para datos del cajero/vendedor, y LEFT JOIN con 'cliente' (v.id_cliente = c.id_cliente) ya que una venta puede ser a cliente anónimo
        
        $params = []; // Arreglo para parámetros dinámicos de consulta
        if (!empty($fechaInicio)) { // Condicional: si se especificó fecha inicio
            $sql .= " AND DATE(v.fecha_venta) >= :fechaInicio "; // Filtra ventas a partir de esta fecha
            $params['fechaInicio'] = $fechaInicio; // Asigna fecha de inicio
        } // Fin condición fechaInicio
        if (!empty($fechaFin)) { // Condicional: si se especificó fecha fin
            $sql .= " AND DATE(v.fecha_venta) <= :fechaFin "; // Filtra ventas hasta esta fecha
            $params['fechaFin'] = $fechaFin; // Asigna fecha final
        } // Fin condición fechaFin
        if ($userId) { // Condicional: si se filtra por un vendedor/usuario específico
            $sql .= " AND v.id_usuario = :userId "; // Filtra por el id_usuario
            $params['userId'] = $userId; // Asigna el id del usuario
        } // Fin condición usuario

        $sql .= " ORDER BY v.fecha_venta DESC LIMIT " . (int)$limit; // Ordena por fecha de venta descendente y limita la cantidad de registros
        $stmt = $this->db->prepare($sql); // Prepara la consulta dinámica
        $stmt->execute($params); // Ejecuta con los parámetros filtrados
        return $stmt->fetchAll(); // Retorna la lista de ventas con información de usuario y cliente
    } // Fin del método getAll

    public function getById(int $id): ?array { // Obtiene los datos completos de una venta específica por su id_venta
        $sql = "SELECT v.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.numero_identificacion, c.telefono, c.direccion 
                FROM venta v 
                JOIN usuario u ON v.id_usuario = u.id_usuario 
                LEFT JOIN cliente c ON v.id_cliente = c.id_cliente 
                WHERE v.id_venta = :id LIMIT 1"; // Conexión relacional: Trae datos de 'venta', del vendedor en 'usuario' y del comprador en 'cliente' (incluyendo teléfono y dirección para la factura)
        $stmt = $this->db->prepare($sql); // Prepara la consulta parametrizada
        $stmt->execute(['id' => $id]); // Ejecuta vinculando el ID de la venta
        $sale = $stmt->fetch(); // Extrae la fila de la venta
        return $sale ?: null; // Retorna los datos o null si no se encuentra
    } // Fin del método getById

    public function getTodayStats(): array { // Calcula métricas del día actual para el panel de ventas
        $sql = "SELECT COUNT(*) AS total_ventas, COALESCE(SUM(total), 0) AS total_ingresos 
                FROM venta 
                WHERE DATE(fecha_venta) = CURDATE() AND estado = 'COMPLETADA'"; // Consulta SQL: Conteo y suma de ingresos del día de hoy (CURDATE()) considerando solo ventas con estado COMPLETADA
        $stmt = $this->db->query($sql); // Ejecuta la consulta
        return $stmt->fetch() ?: ['total_ventas' => 0, 'total_ingresos' => 0]; // Retorna el resumen o valores en cero si no hay ventas hoy
    } // Fin del método getTodayStats

    public function getSalesLastDays(int $days = 7): array { // Obtiene el historial agrupado por día para gráficas de tendencias
        $sql = "SELECT DATE(fecha_venta) AS fecha, COUNT(*) AS cantidad, COALESCE(SUM(total), 0) AS total 
                FROM venta 
                WHERE estado = 'COMPLETADA' AND fecha_venta >= DATE_SUB(CURDATE(), INTERVAL :days DAY) 
                GROUP BY DATE(fecha_venta) 
                ORDER BY fecha ASC"; // Consulta SQL: Agrupa ventas por fecha para los últimos N días calculados con DATE_SUB
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        $stmt->bindValue(':days', $days, PDO::PARAM_INT); // Asigna los días como número entero
        $stmt->execute(); // Ejecuta la consulta
        return $stmt->fetchAll(); // Retorna el arreglo de ventas agrupadas por día para gráficas
    } // Fin del método getSalesLastDays

    public function create(int $userId, ?int $clientId, float $subtotal, float $total): int { // Registra el encabezado de una nueva venta en la tabla 'venta'
        $sql = "INSERT INTO venta (id_usuario, id_cliente, subtotal, total, estado, fecha_venta) 
                VALUES (:id_usuario, :id_cliente, :subtotal, :total, 'COMPLETADA', NOW())"; // Sentencia SQL con llaves foráneas: Conecta el vendedor (id_usuario) y opcionalmente el cliente (id_cliente)
        $stmt = $this->db->prepare($sql); // Prepara la inserción
        $stmt->execute([ // Ejecuta la inserción con los datos de la transacción
            'id_usuario' => $userId, // Llave foránea hacia 'usuario'
            'id_cliente' => $clientId, // Llave foránea hacia 'cliente' o null
            'subtotal'   => $subtotal, // Subtotal de la venta antes de impuestos/descuentos
            'total'      => $total // Monto total liquidado
        ]); // Fin de ejecución
        return (int)$this->db->lastInsertId(); // Retorna el id_venta generado para insertar sus detalles en 'detalle_venta'
    } // Fin del método create

    public function cancel(int $id): bool { // Cancela o anula una venta en el sistema
        $stmt = $this->db->prepare("UPDATE venta SET estado = 'CANCELADA' WHERE id_venta = :id"); // Sentencia SQL: actualiza el estado a CANCELADA en la tabla 'venta'
        return $stmt->execute(['id' => $id]); // Ejecuta y retorna true si se canceló
    } // Fin del método cancel
} // Fin de la clase Venta
