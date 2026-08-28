<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión PDO con la base de datos

class OperacionCorresponsal { // Modelo 'OperacionCorresponsal': gestiona giros, recaudos, retiros y depósitos bancarios en la tabla 'operacion_corresponsal'
    private PDO $db; // Variable para la conexión PDO a MySQL

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Inicializa la conexión a la base de datos
    } // Fin del constructor

    public function create(array $data): int { // Registra una operación de corresponsal bancario conectando usuario, cliente y cuenta
        $sql = "INSERT INTO operacion_corresponsal (id_usuario, id_cliente, id_cuenta, tipo, valor, referencia, descripcion, fecha_operacion) 
                VALUES (:id_usuario, :id_cliente, :id_cuenta, :tipo, :valor, :referencia, :descripcion, NOW())"; // Sentencia SQL con llaves foráneas: 'usuario' (id_usuario), 'cliente' (id_cliente) y 'cuenta' (id_cuenta)
        
        $stmt = $this->db->prepare($sql); // Prepara la sentencia SQL de inserción
        $stmt->execute([ // Ejecuta la inserción con los parámetros vinculados
            'id_usuario'  => $data['id_usuario'], // Llave foránea vinculada al operador (tabla 'usuario')
            'id_cliente'  => $data['id_cliente'] ?? null, // Llave foránea opcional vinculada al cliente (tabla 'cliente')
            'id_cuenta'   => $data['id_cuenta'], // Llave foránea vinculada a la cuenta bancaria utilizada (tabla 'cuenta')
            'tipo'        => $data['tipo'], // Tipo de operación ('DEPOSITO', 'RETIRO')
            'valor'       => $data['valor'], // Monto de dinero de la operación
            'referencia'  => $data['referencia'] ?? null, // Número de referencia o convenio bancario
            'descripcion' => $data['descripcion'] ?? null // Observaciones o detalles adicionales
        ]); // Fin de la ejecución con parámetros

        return (int)$this->db->lastInsertId(); // Retorna el ID autoincrementable de la operación creada
    } // Fin del método create

    public function getAll(?string $tipo = null, ?string $fechaInicio = null, ?string $fechaFin = null, int $limit = 200): array { // Obtiene el listado de operaciones con datos de usuario y cliente
        $sql = "SELECT oc.*, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.numero_identificacion 
                FROM operacion_corresponsal oc 
                JOIN usuario u ON oc.id_usuario = u.id_usuario 
                LEFT JOIN cliente c ON oc.id_cliente = c.id_cliente 
                WHERE 1=1 "; // Conexión relacional: JOIN con 'usuario' (oc.id_usuario = u.id_usuario) y LEFT JOIN con 'cliente' (oc.id_cliente = c.id_cliente) porque un cliente puede o no estar registrado
        
        $params = []; // Arreglo para parámetros dinámicos de búsqueda
        if (!empty($tipo)) { // Condicional: si se filtra por tipo (DEPOSITO o RETIRO)
            $sql .= " AND oc.tipo = :tipo "; // Agrega cláusula WHERE para tipo
            $params['tipo'] = $tipo; // Asigna el valor del tipo
        } // Fin condición tipo
        if (!empty($fechaInicio)) { // Condicional: si se envía fecha inicio
            $sql .= " AND DATE(oc.fecha_operacion) >= :fechaInicio "; // Filtra fechas mayores o iguales
            $params['fechaInicio'] = $fechaInicio; // Asigna fecha inicio
        } // Fin condición fechaInicio
        if (!empty($fechaFin)) { // Condicional: si se envía fecha fin
            $sql .= " AND DATE(oc.fecha_operacion) <= :fechaFin "; // Filtra fechas menores o iguales
            $params['fechaFin'] = $fechaFin; // Asigna fecha fin
        } // Fin condición fechaFin

        $sql .= " ORDER BY oc.fecha_operacion DESC LIMIT " . (int)$limit; // Ordena cronológicamente descendente y limita la cantidad
        $stmt = $this->db->prepare($sql); // Prepara la consulta dinámica
        $stmt->execute($params); // Ejecuta pasando los filtros
        return $stmt->fetchAll(); // Retorna todas las operaciones con la información del usuario y cliente
    } // Fin del método getAll

    public function getTodayStats(): array { // Calcula métricas del día actual para corresponsal bancario
        $sql = "SELECT 
                    COUNT(*) AS total_operaciones, 
                    COALESCE(SUM(CASE WHEN tipo = 'DEPOSITO' THEN valor ELSE 0 END), 0) AS total_depositos, 
                    COALESCE(SUM(CASE WHEN tipo = 'RETIRO' THEN valor ELSE 0 END), 0) AS total_retiros 
                FROM operacion_corresponsal 
                WHERE DATE(fecha_operacion) = CURDATE()"; // Consulta SQL: Agrupa operaciones del día actual (CURDATE()) sumando depósitos y retiros por separado
        $stmt = $this->db->query($sql); // Ejecuta la consulta directa
        return $stmt->fetch() ?: ['total_operaciones' => 0, 'total_depositos' => 0, 'total_retiros' => 0]; // Retorna las estadísticas del día o valores en cero por defecto
    } // Fin del método getTodayStats
} // Fin de la clase OperacionCorresponsal
