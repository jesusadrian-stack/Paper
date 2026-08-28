<?php // Apertura del archivo PHP
require_once __DIR__ . '/../Config/database.php'; // Carga la conexión a la base de datos MySQL mediante la clase Database

class Usuario { // Modelo 'Usuario': gestiona las cuentas de usuarios del sistema y sus roles en la tabla 'usuario'
    private PDO $db; // Variable para almacenar la conexión PDO a MySQL

    public function __construct() { // Constructor que se ejecuta al instanciar la clase
        $this->db = Database::getConnection(); // Obtiene y conecta la instancia activa de Database
    } // Fin del constructor

    public function getAll(): array { // Obtiene el listado completo de usuarios con el nombre de su rol
        $sql = "SELECT u.*, r.nombre AS rol_nombre 
                FROM usuario u 
                JOIN rol r ON u.id_rol = r.id_rol 
                ORDER BY u.id_usuario DESC"; // Conexión relacional: JOIN entre 'usuario' (u) y 'rol' (r) mediante la llave foránea u.id_rol = r.id_rol para obtener el rol asignado a cada usuario
        $stmt = $this->db->query($sql); // Ejecuta la consulta SQL directamente
        return $stmt->fetchAll(); // Retorna todos los usuarios con sus roles
    } // Fin del método getAll

    public function getById(int $id): ?array { // Busca un usuario específico por su identificador id_usuario
        $sql = "SELECT u.*, r.nombre AS rol_nombre 
                FROM usuario u 
                JOIN rol r ON u.id_rol = r.id_rol 
                WHERE u.id_usuario = :id LIMIT 1"; // Conexión relacional: JOIN con 'rol' filtrando por la clave primaria u.id_usuario
        $stmt = $this->db->prepare($sql); // Prepara la consulta parametrizada
        $stmt->execute(['id' => $id]); // Ejecuta vinculando el :id del usuario
        $user = $stmt->fetch(); // Extrae la fila del usuario
        return $user ?: null; // Retorna los datos o null si no se encuentra
    } // Fin del método getById

    public function getByUsername(string $username): ?array { // Busca un usuario para inicio de sesión por nombre de usuario o correo
        $sql = "SELECT u.*, r.nombre AS rol_nombre 
                FROM usuario u 
                JOIN rol r ON u.id_rol = r.id_rol 
                WHERE (u.nombre_usuario = :username OR u.correo = :correo) LIMIT 1"; // Conexión relacional: JOIN con 'rol' permitiendo autenticación por username o por email
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        $stmt->execute(['username' => $username, 'correo' => $username]); // Ejecuta vinculando ambos parámetros con el mismo valor
        $user = $stmt->fetch(); // Extrae el usuario autenticado
        return $user ?: null; // Retorna el registro o null si no coincide
    } // Fin del método getByUsername

    public function getByCorreo(string $correo): ?array { // Busca si un correo ya está registrado en la base de datos
        $sql = "SELECT * FROM usuario WHERE correo = :correo LIMIT 1"; // Consulta parametrizada por columna 'correo'
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        $stmt->execute(['correo' => $correo]); // Ejecuta pasando el correo
        $user = $stmt->fetch(); // Extrae la fila
        return $user ?: null; // Retorna el usuario o null
    } // Fin del método getByCorreo

    public function getByDocumento(string $documento): ?array { // Busca si un número de documento ya existe en 'usuario'
        $sql = "SELECT * FROM usuario WHERE documento = :documento LIMIT 1"; // Consulta parametrizada por columna 'documento'
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        $stmt->execute(['documento' => $documento]); // Ejecuta pasando el documento
        $user = $stmt->fetch(); // Extrae la fila
        return $user ?: null; // Retorna el usuario o null
    } // Fin del método getByDocumento

    public function create(array $data): int { // Inserta un nuevo usuario asociándole un rol y encriptando su contraseña
        $sql = "INSERT INTO usuario (id_rol, nombre, apellido, documento, telefono, correo, nombre_usuario, contrasena, estado, fecha_registro) 
                VALUES (:id_rol, :nombre, :apellido, :documento, :telefono, :correo, :nombre_usuario, :contrasena, :estado, NOW())"; // Sentencia SQL con llave foránea id_rol (conecta con tabla 'rol')
        
        $stmt = $this->db->prepare($sql); // Prepara la inserción
        $stmt->execute([ // Ejecuta la inserción con los datos
            'id_rol'         => $data['id_rol'], // Llave foránea vinculada a la tabla 'rol'
            'nombre'         => $data['nombre'], // Nombres del usuario
            'apellido'       => $data['apellido'], // Apellidos del usuario
            'documento'      => $data['documento'], // Cédula o documento
            'telefono'       => $data['telefono'] ?? null, // Teléfono opcional
            'correo'         => $data['correo'] ?? null, // Correo electrónico opcional
            'nombre_usuario' => $data['nombre_usuario'], // Nickname o login
            'contrasena'     => password_hash($data['contrasena'], PASSWORD_BCRYPT), // Encriptación segura de contraseña con algoritmo BCRYPT
            'estado'         => $data['estado'] ?? 'ACTIVO' // Estado inicial (por defecto 'ACTIVO')
        ]); // Fin de ejecución

        return (int)$this->db->lastInsertId(); // Retorna el nuevo id_usuario generado
    } // Fin del método create

    public function update(int $id, array $data): bool { // Actualiza los datos de un usuario existente (incluyendo cambio opcional de rol y contraseña)
        $fields = [ // Arreglo con los campos estándar a actualizar
            'id_rol = :id_rol', // Conexión a nuevo o mismo rol
            'nombre = :nombre', // Actualiza nombre
            'apellido = :apellido', // Actualiza apellido
            'documento = :documento', // Actualiza documento
            'telefono = :telefono', // Actualiza teléfono
            'correo = :correo', // Actualiza correo
            'nombre_usuario = :nombre_usuario', // Actualiza username
            'estado = :estado' // Actualiza estado
        ]; // Fin de arreglo de campos

        $params = [ // Arreglo de parámetros con los valores correspondientes
            'id'             => $id, // ID del usuario a modificar
            'id_rol'         => $data['id_rol'], // Llave foránea de rol
            'nombre'         => $data['nombre'], // Nombre
            'apellido'       => $data['apellido'], // Apellido
            'documento'      => $data['documento'], // Documento
            'telefono'       => $data['telefono'] ?? null, // Teléfono
            'correo'         => $data['correo'] ?? null, // Correo
            'nombre_usuario' => $data['nombre_usuario'], // Nombre de usuario
            'estado'         => $data['estado'] ?? 'ACTIVO' // Estado
        ]; // Fin de arreglo de parámetros

        if (!empty($data['contrasena'])) { // Si el usuario ingresó una nueva contraseña
            $fields[] = 'contrasena = :contrasena'; // Agrega la actualización de contraseña a la consulta SQL
            $params['contrasena'] = password_hash($data['contrasena'], PASSWORD_BCRYPT); // Encripta la nueva contraseña antes de guardarla
        } // Fin condición contraseña

        $sql = "UPDATE usuario SET " . implode(', ', $fields) . " WHERE id_usuario = :id"; // Arma la consulta UPDATE dinámica
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        return $stmt->execute($params); // Ejecuta y retorna true si fue exitoso
    } // Fin del método update

    public function updateLastAccess(int $id): bool { // Actualiza la fecha y hora del último acceso del usuario al iniciar sesión
        $stmt = $this->db->prepare("UPDATE usuario SET ultimo_acceso = NOW() WHERE id_usuario = :id"); // Sentencia SQL: establece ultimo_acceso = NOW() para el id_usuario
        return $stmt->execute(['id' => $id]); // Ejecuta la actualización de acceso
    } // Fin del método updateLastAccess

    public function toggleStatus(int $id): bool { // Alterna el estado de un usuario entre ACTIVO e INACTIVO
        $user = $this->getById($id); // Consulta el usuario para verificar su estado actual
        if (!$user) return false; // Si no existe el usuario, retorna false

        $newStatus = ($user['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO'; // Alterna el valor de estado
        $stmt = $this->db->prepare("UPDATE usuario SET estado = :estado WHERE id_usuario = :id"); // Prepara la actualización de estado
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]); // Ejecuta el cambio de estado
    } // Fin del método toggleStatus
} // Fin de la clase Usuario
