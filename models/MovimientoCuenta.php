<?php // Apertura del archivo PHP
require_once __DIR__ . '/../Config/database.php'; // Carga la conexión centralizada a la base de datos

class MovimientoCuenta { // Modelo 'MovimientoCuenta': audita el flujo de dinero (ingresos, egresos, depósitos, retiros) en la tabla 'movimiento_cuenta'
    private PDO $db; // Variable para almacenar la conexión PDO a MySQL

    public function __construct() { // Constructor ejecutado al instanciar el modelo
        $this->db = Database::getConnection(); // Obtiene la instancia activa de la base de datos
    } // Fin del constructor

    public function create(array $data): int { // Registra un movimiento financiero conectando cuenta, usuario y opcionalmente una venta
        $sql = "INSERT INTO movimiento_cuenta (id_cuenta, id_usuario, id_venta, tipo, concepto, valor, saldo_anterior, saldo_nuevo, fecha_movimiento) 
                VALUES (:id_cuenta, :id_usuario, :id_venta, :tipo, :concepto, :valor, :saldo_anterior, :saldo_nuevo, NOW())"; // Sentencia SQL: Llaves foráneas a 'cuenta' (id_cuenta), 'usuario' (id_usuario) y 'venta' (id_venta)
        
        $stmt = $this->db->prepare($sql); // Prepara la consulta para evitar inyección SQL
        $stmt->execute([ // Ejecuta la inserción con los parámetros enlazados
            'id_cuenta'      => $data['id_cuenta'], // Llave foránea conectada a la tabla 'cuenta' (caja o banco afectado)
            'id_usuario'     => $data['id_usuario'], // Llave foránea conectada a la tabla 'usuario' (quién hizo el movimiento)
            'id_venta'       => $data['id_venta'] ?? null, // Llave foránea opcional a 'venta' (si el dinero proviene de una venta)
            'tipo'           => $data['tipo'], // Tipo: INGRESO, EGRESO, DEPOSITO, RETIRO
            'concepto'       => $data['concepto'], // Descripción o motivo del movimiento
            'valor'          => $data['valor'], // Cantidad de dinero movida
            'saldo_anterior' => $data['saldo_anterior'], // Saldo que tenía la cuenta antes de la operación
            'saldo_nuevo'    => $data['saldo_nuevo'] // Saldo resultante tras la operación
        ]); // Fin de la ejecución con parámetros

        return (int)$this->db->lastInsertId(); // Retorna el ID autoincrementable del movimiento financiero
    } // Fin del método create

    public function getAll(?int $cuentaId = null, ?string $tipo = null, ?string $fechaInicio = null, ?string $fechaFin = null, int $limit = 200): array { // Consulta movimientos con filtros y datos de cuenta y usuario
        $sql = "SELECT mc.*, c.nombre AS cuenta_nombre, c.tipo AS cuenta_tipo, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM movimiento_cuenta mc 
                JOIN cuenta c ON mc.id_cuenta = c.id_cuenta 
                JOIN usuario u ON mc.id_usuario = u.id_usuario 
                WHERE 1=1 "; // Conexión relacional: JOIN con 'cuenta' (mc.id_cuenta = c.id_cuenta) y JOIN con 'usuario' (mc.id_usuario = u.id_usuario) para traer nombre de cuenta y usuario
        
        $params = []; // Arreglo para almacenar parámetros dinámicos del filtro
        if ($cuentaId) { // Condicional: si se filtra por una cuenta bancaria o de caja específica
            $sql .= " AND mc.id_cuenta = :cuentaId "; // Agrega filtro por id_cuenta
            $params['cuentaId'] = $cuentaId; // Asigna el valor del ID de cuenta
        } // Fin condición cuenta
        if (!empty($tipo)) { // Condicional: si se filtra por tipo de transacción (INGRESO, EGRESO, etc.)
            $sql .= " AND mc.tipo = :tipo "; // Agrega filtro por tipo
            $params['tipo'] = $tipo; // Asigna el tipo
        } // Fin condición tipo
        if (!empty($fechaInicio)) { // Condicional: si se proporciona fecha inicial
            $sql .= " AND DATE(mc.fecha_movimiento) >= :fechaInicio "; // Filtra fechas mayores o iguales a fechaInicio
            $params['fechaInicio'] = $fechaInicio; // Asigna fecha inicial
        } // Fin condición fechaInicio
        if (!empty($fechaFin)) { // Condicional: si se proporciona fecha final
            $sql .= " AND DATE(mc.fecha_movimiento) <= :fechaFin "; // Filtra fechas menores o iguales a fechaFin
            $params['fechaFin'] = $fechaFin; // Asigna fecha final
        } // Fin condición fechaFin

        $sql .= " ORDER BY mc.fecha_movimiento DESC LIMIT " . (int)$limit; // Ordena del más reciente al más antiguo y limita resultados
        $stmt = $this->db->prepare($sql); // Prepara la consulta dinámica
        $stmt->execute($params); // Ejecuta con los parámetros filtrados
        return $stmt->fetchAll(); // Retorna la lista de movimientos con detalles de cuenta y usuario
    } // Fin del método getAll
} // Fin de la clase MovimientoCuenta
