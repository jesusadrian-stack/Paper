<?php // Apertura del archivo PHP
require_once __DIR__ . '/../Config/database.php'; // Requiere el archivo de configuración para la conexión a la base de datos

class AnalisisIA { // Modelo 'AnalisisIA': almacena y consulta los diagnósticos de inteligencia artificial en la tabla 'analisis_ia'
    private PDO $db; // Variable para gestionar la conexión PDO con MySQL

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Inicializa la conexión mediante el singleton de Database
    } // Fin del constructor

    public function create(?int $userId, string $tipo, string $titulo, string $resultado): int { // Registra un nuevo análisis generado por el módulo de IA
        $sql = "INSERT INTO analisis_ia (id_usuario, tipo, titulo, resultado, fecha_analisis) 
                VALUES (:id_usuario, :tipo, :titulo, :resultado, NOW())"; // Inserta el análisis conectándolo con el usuario que lo solicitó (id_usuario)
        $stmt = $this->db->prepare($sql); // Prepara la sentencia SQL de inserción
        $stmt->execute([ // Ejecuta la consulta pasando los parámetros correspondientes
            'id_usuario' => $userId, // Llave foránea vinculada al usuario (o null si fue automático por el sistema)
            'tipo'       => $tipo, // Tipo de análisis (ej. INVENTARIO, VENTAS, FINANCIERO)
            'titulo'     => $titulo, // Título descriptivo del análisis
            'resultado'  => $resultado // Texto o contenido JSON con el resultado entregado por la IA
        ]); // Fin de la ejecución con parámetros
        return (int)$this->db->lastInsertId(); // Retorna el id_analisis generado para poder vincularlo con recomendaciones
    } // Fin del método create

    public function getAll(int $limit = 50): array { // Obtiene el listado de análisis de IA con información de usuario y total de recomendaciones
        $sql = "SELECT a.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido,
                       (SELECT COUNT(*) FROM recomendacion_ia r WHERE r.id_analisis = a.id_analisis) AS total_recomendaciones 
                FROM analisis_ia a 
                LEFT JOIN usuario u ON a.id_usuario = u.id_usuario 
                ORDER BY a.fecha_analisis DESC 
                LIMIT :limit"; // Conexión relacional: LEFT JOIN con 'usuario' para ver quién generó el análisis, y subconsulta a 'recomendacion_ia' conectando id_analisis para contar recomendaciones derivadas
        $stmt = $this->db->prepare($sql); // Prepara la consulta para ejecución segura
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Asigna el parámetro limit como entero
        $stmt->execute(); // Ejecuta la consulta
        return $stmt->fetchAll(); // Retorna la lista de análisis con sus métricas y datos de usuario
    } // Fin del método getAll

    public function getById(int $id): ?array { // Obtiene un análisis específico por su clave primaria id_analisis
        $sql = "SELECT a.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM analisis_ia a 
                LEFT JOIN usuario u ON a.id_usuario = u.id_usuario 
                WHERE a.id_analisis = :id LIMIT 1"; // Conexión: LEFT JOIN con 'usuario' mediante id_usuario para traer el nombre completo del usuario
        $stmt = $this->db->prepare($sql); // Prepara la consulta con el parámetro :id
        $stmt->execute(['id' => $id]); // Ejecuta vinculando el identificador del análisis
        $analysis = $stmt->fetch(); // Extrae la fila del análisis
        return $analysis ?: null; // Retorna los datos del análisis o null si no se encontró
    } // Fin del método getById
} // Fin de la clase AnalisisIA
