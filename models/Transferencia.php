<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la clase de conexión a la base de datos

class Transferencia { // Modelo 'Transferencia': gestiona los movimientos de dinero entre dos cuentas en la tabla 'transferencia'
    private PDO $db; // Almacena el objeto PDO que conecta con la base de datos MySQL

    public function __construct() { // Constructor ejecutado al instanciar la clase Transferencia
        $this->db = Database::getConnection(); // Obtiene y establece la conexión activa a la base de datos
    } // Fin del constructor

    public function create(int $userId, int $origenId, int $destinoId, float $valor, ?string $concepto): int { // Registra una transferencia conectando usuario, cuenta origen y cuenta destino
        $sql = "INSERT INTO transferencia (id_usuario, id_cuenta_origen, id_cuenta_destino, valor, concepto, fecha_transferencia) 
                VALUES (:id_usuario, :id_cuenta_origen, :id_cuenta_destino, :valor, :concepto, NOW())"; // SQL con llaves foráneas: conecta id_usuario (quien transfiere), id_cuenta_origen (sale dinero) e id_cuenta_destino (entra dinero)
        
        $stmt = $this->db->prepare($sql); // Prepara la sentencia SQL de inserción
        $stmt->execute([ // Ejecuta la inserción con los parámetros enlazados
            'id_usuario'        => $userId, // Llave foránea vinculada a la tabla 'usuario'
            'id_cuenta_origen'  => $origenId, // Llave foránea vinculada a la cuenta de origen (tabla 'cuenta')
            'id_cuenta_destino' => $destinoId, // Llave foránea vinculada a la cuenta de destino (tabla 'cuenta')
            'valor'             => $valor, // Monto monetario transferido
            'concepto'          => $concepto ?? 'Transferencia entre cuentas' // Motivo o concepto de la transferencia con valor por defecto
        ]); // Fin de la ejecución con parámetros

        return (int)$this->db->lastInsertId(); // Retorna el ID autoincrementable de la transferencia recién creada
    } // Fin del método create

    public function getAll(int $limit = 100): array { // Consulta transferencias uniendo datos de cuentas y usuario
        $sql = "SELECT t.*, 
                       co.nombre AS cuenta_origen_nombre, co.tipo AS cuenta_origen_tipo, 
                       cd.nombre AS cuenta_destino_nombre, cd.tipo AS cuenta_destino_tipo, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM transferencia t 
                JOIN cuenta co ON t.id_cuenta_origen = co.id_cuenta 
                JOIN cuenta cd ON t.id_cuenta_destino = cd.id_cuenta 
                JOIN usuario u ON t.id_usuario = u.id_usuario 
                ORDER BY t.fecha_transferencia DESC 
                LIMIT :limit"; // Conexión relacional: JOIN con 'cuenta' para datos de origen (co), JOIN con 'cuenta' para datos de destino (cd), y JOIN con 'usuario' (u) para saber quién realizó la transferencia
        
        $stmt = $this->db->prepare($sql); // Prepara la consulta para evitar inyecciones SQL
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Vincula el límite como número entero en PDO
        $stmt->execute(); // Ejecuta la consulta relacional
        return $stmt->fetchAll(); // Retorna el listado completo de transferencias con los detalles de cuentas y usuario
    } // Fin del método getAll
} // Fin de la clase Transferencia
