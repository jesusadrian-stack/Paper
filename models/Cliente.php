<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la clase de configuración y conexión a la base de datos MySQL

class Cliente { // Modelo 'Cliente': gestiona los datos de los compradores y clientes en la tabla 'cliente'
    private PDO $db; // Variable para almacenar la conexión PDO a MySQL

    public function __construct() { // Constructor de la clase Cliente
        $this->db = Database::getConnection(); // Conecta con la base de datos a través de Database
    } // Fin del constructor

    public function getAll(bool $onlyActive = false, ?string $search = null): array { // Obtiene todos los clientes con opciones de filtro por estado y término de búsqueda
        $sql = "SELECT * FROM cliente WHERE 1=1 "; // Consulta base sobre la tabla 'cliente'
        $params = []; // Arreglo para almacenar parámetros enlazados

        if ($onlyActive) { // Condicional: si solo se solicitan clientes activos
            $sql .= " AND estado = 'ACTIVO' "; // Filtra clientes cuyo estado sea ACTIVO
        } // Fin condición onlyActive
        if (!empty($search)) { // Condicional: si se envió un término de búsqueda
            $sql .= " AND (nombre LIKE :s1 OR apellido LIKE :s2 OR numero_identificacion LIKE :s3 OR telefono LIKE :s4) "; // Búsqueda flexible por nombre, apellido, documento o teléfono
            $params['s1'] = "%{$search}%"; // Parámetro de coincidencia para nombre
            $params['s2'] = "%{$search}%"; // Parámetro de coincidencia para apellido
            $params['s3'] = "%{$search}%"; // Parámetro de coincidencia para número de identificación
            $params['s4'] = "%{$search}%"; // Parámetro de coincidencia para teléfono
        } // Fin condición search

        $sql .= " ORDER BY nombre ASC"; // Ordena alfabéticamente por nombre
        $stmt = $this->db->prepare($sql); // Prepara la consulta dinámica
        $stmt->execute($params); // Ejecuta vinculando los parámetros de búsqueda
        return $stmt->fetchAll(); // Retorna la lista de clientes encontrados
    } // Fin del método getAll

    public function getById(int $id): ?array { // Busca un cliente por su clave primaria id_cliente
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE id_cliente = :id LIMIT 1"); // Prepara la consulta filtrando por id_cliente
        $stmt->execute(['id' => $id]); // Ejecuta vinculando el parámetro :id
        $client = $stmt->fetch(); // Extrae la fila del cliente
        return $client ?: null; // Retorna los datos del cliente o null si no existe
    } // Fin del método getById

    public function getByIdentificacion(string $doc): ?array { // Busca un cliente por su documento de identidad (cédula, NIT, etc.)
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE numero_identificacion = :doc LIMIT 1"); // Prepara la consulta por numero_identificacion
        $stmt->execute(['doc' => $doc]); // Ejecuta vinculando el número de documento
        $client = $stmt->fetch(); // Extrae el registro si existe
        return $client ?: null; // Retorna el cliente o null si no se encuentra
    } // Fin del método getByIdentificacion

    public function countTotal(): int { // Cuenta el número total de clientes activos en el sistema
        $stmt = $this->db->query("SELECT COUNT(*) FROM cliente WHERE estado = 'ACTIVO'"); // Consulta SQL: cuenta registros con estado ACTIVO
        return (int)$stmt->fetchColumn(); // Retorna el total numérico de clientes activos
    } // Fin del método countTotal

    public function create(array $data): int { // Registra un nuevo cliente en la tabla 'cliente'
        $sql = "INSERT INTO cliente (tipo_identificacion, numero_identificacion, nombre, apellido, telefono, correo, direccion, estado, fecha_registro) 
                VALUES (:tipo, :num, :nombre, :apellido, :telefono, :correo, :direccion, :estado, NOW())"; // Sentencia SQL para insertar cliente
        
        $stmt = $this->db->prepare($sql); // Prepara la consulta para evitar inyección SQL
        $stmt->execute([ // Ejecuta la inserción con los campos recibidos
            'tipo'      => $data['tipo_identificacion'], // Tipo de documento (CC, TI, NIT, CE, PASAPORTE)
            'num'       => $data['numero_identificacion'], // Número de documento de identidad
            'nombre'    => $data['nombre'], // Nombres del cliente
            'apellido'  => $data['apellido'] ?? null, // Apellidos del cliente (opcional)
            'telefono'  => $data['telefono'] ?? null, // Teléfono o celular (opcional)
            'correo'    => $data['correo'] ?? null, // Correo electrónico (opcional)
            'direccion' => $data['direccion'] ?? null, // Dirección física (opcional)
            'estado'    => $data['estado'] ?? 'ACTIVO' // Estado inicial (por defecto 'ACTIVO')
        ]); // Fin de ejecución con parámetros

        return (int)$this->db->lastInsertId(); // Retorna el id_cliente generado por la base de datos
    } // Fin del método create

    public function update(int $id, array $data): bool { // Actualiza los datos de un cliente existente
        $sql = "UPDATE cliente SET 
                    tipo_identificacion = :tipo, 
                    numero_identificacion = :num, 
                    nombre = :nombre, 
                    apellido = :apellido, 
                    telefono = :telefono, 
                    correo = :correo, 
                    direccion = :direccion, 
                    estado = :estado 
                WHERE id_cliente = :id"; // Sentencia SQL: actualiza columnas filtrando por id_cliente
        
        $stmt = $this->db->prepare($sql); // Prepara la sentencia de actualización
        return $stmt->execute([ // Ejecuta con los nuevos valores
            'id'        => $id, // ID del cliente a modificar
            'tipo'      => $data['tipo_identificacion'], // Nuevo tipo de documento
            'num'       => $data['numero_identificacion'], // Nuevo número de documento
            'nombre'    => $data['nombre'], // Nuevo nombre
            'apellido'  => $data['apellido'] ?? null, // Nuevo apellido o null
            'telefono'  => $data['telefono'] ?? null, // Nuevo teléfono o null
            'correo'    => $data['correo'] ?? null, // Nuevo correo o null
            'direccion' => $data['direccion'] ?? null, // Nueva dirección o null
            'estado'    => $data['estado'] ?? 'ACTIVO' // Nuevo estado
        ]); // Retorna true si se actualizó correctamente
    } // Fin del método update

    public function toggleStatus(int $id): bool { // Alterna el estado del cliente entre ACTIVO e INACTIVO
        $client = $this->getById($id); // Consulta el cliente para verificar su estado actual
        if (!$client) return false; // Si el cliente no existe, retorna false
        $newStatus = ($client['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO'; // Invierte el estado
        $stmt = $this->db->prepare("UPDATE cliente SET estado = :estado WHERE id_cliente = :id"); // Prepara la actualización de estado
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]); // Ejecuta el cambio y retorna true/false
    } // Fin del método toggleStatus
} // Fin de la clase Cliente
