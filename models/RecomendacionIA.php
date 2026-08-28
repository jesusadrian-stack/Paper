<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión a la base de datos MySQL

class RecomendacionIA { // Modelo 'RecomendacionIA': gestiona las sugerencias generadas por la IA en la tabla 'recomendacion_ia'
    private PDO $db; // Variable para almacenar la conexión PDO activa

    public function __construct() { // Constructor de la clase
        $this->db = Database::getConnection(); // Inicializa la conexión mediante el singleton Database
    } // Fin del constructor

    public function create(array $data): int { // Guarda una recomendación conectando con el análisis de origen, producto y/o cliente
        $sql = "INSERT INTO recomendacion_ia (id_analisis, id_producto, id_cliente, tipo, recomendacion, prioridad, fecha_recomendacion, atendida) 
                VALUES (:id_analisis, :id_producto, :id_cliente, :tipo, :recomendacion, :prioridad, NOW(), 0)"; // Sentencia SQL con llaves foráneas: Vincula con 'analisis_ia' (id_analisis), 'producto' (id_producto) y 'cliente' (id_cliente)
        
        $stmt = $this->db->prepare($sql); // Prepara la consulta para ejecución segura
        $stmt->execute([ // Ejecuta la inserción vinculando los parámetros
            'id_analisis'    => $data['id_analisis'], // Llave foránea obligatoria hacia 'analisis_ia' (diagnóstico que generó la recomendación)
            'id_producto'    => $data['id_producto'] ?? null, // Llave foránea opcional hacia 'producto' (si la sugerencia es sobre un artículo)
            'id_cliente'     => $data['id_cliente'] ?? null, // Llave foránea opcional hacia 'cliente' (si la sugerencia es para un comprador)
            'tipo'           => $data['tipo'], // Categoría de la recomendación (ej. STOCK, PRECIO, PROMOCION)
            'recomendacion'  => $data['recomendacion'], // Texto con el consejo o acción sugerida por la IA
            'prioridad'      => $data['prioridad'] ?? 'MEDIA' // Nivel de urgencia ('ALTA', 'MEDIA', 'BAJA')
        ]); // Fin de la ejecución con parámetros

        return (int)$this->db->lastInsertId(); // Retorna el id_recomendacion generado
    } // Fin del método create

    public function getAll(bool $onlyPending = false, int $limit = 50): array { // Obtiene las recomendaciones uniendo datos de productos y clientes
        $sql = "SELECT r.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido 
                FROM recomendacion_ia r 
                LEFT JOIN producto p ON r.id_producto = p.id_producto 
                LEFT JOIN cliente c ON r.id_cliente = c.id_cliente 
                WHERE 1=1 "; // Conexión relacional: LEFT JOIN con 'producto' (r.id_producto = p.id_producto) y LEFT JOIN con 'cliente' (r.id_cliente = c.id_cliente) para mostrar nombres legibles si están asociados
        
        if ($onlyPending) { // Condicional: si solo se solicitan sugerencias no atendidas
            $sql .= " AND r.atendida = 0 "; // Filtra por atendida = 0
        } // Fin condición

        $sql .= " ORDER BY r.atendida ASC, 
                  CASE r.prioridad 
                      WHEN 'ALTA' THEN 1 
                      WHEN 'MEDIA' THEN 2 
                      WHEN 'BAJA' THEN 3 
                      ELSE 4 
                  END, 
                  r.fecha_recomendacion DESC 
                  LIMIT :limit"; // Ordenamiento inteligente: primero pendientes, luego orden por prioridad (ALTA > MEDIA > BAJA) y fecha más reciente
        
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Vincula el límite de registros como entero
        $stmt->execute(); // Ejecuta la consulta
        return $stmt->fetchAll(); // Retorna la lista de recomendaciones detalladas
    } // Fin del método getAll

    public function getByAnalysisId(int $analysisId): array { // Obtiene todas las recomendaciones derivadas de un análisis específico
        $sql = "SELECT r.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido 
                FROM recomendacion_ia r 
                LEFT JOIN producto p ON r.id_producto = p.id_producto 
                LEFT JOIN cliente c ON r.id_cliente = c.id_cliente 
                WHERE r.id_analisis = :analysisId 
                ORDER BY r.fecha_recomendacion DESC"; // Conexión relacional: Filtra por la llave foránea id_analisis uniendo con 'producto' y 'cliente'
        $stmt = $this->db->prepare($sql); // Prepara la sentencia SQL
        $stmt->execute(['analysisId' => $analysisId]); // Ejecuta vinculando el identificador del análisis
        return $stmt->fetchAll(); // Retorna las recomendaciones asociadas a ese diagnóstico
    } // Fin del método getByAnalysisId

    public function countPending(): int { // Cuenta cuántas recomendaciones pendientes por aplicar existen
        $stmt = $this->db->query("SELECT COUNT(*) FROM recomendacion_ia WHERE atendida = 0"); // Consulta SQL: cuenta filas donde atendida = 0
        return (int)$stmt->fetchColumn(); // Retorna el total de recomendaciones pendientes
    } // Fin del método countPending

    public function markAsResolved(int $id): bool { // Marca una recomendación como atendida/resuelta
        $stmt = $this->db->prepare("UPDATE recomendacion_ia SET atendida = 1 WHERE id_recomendacion = :id"); // Sentencia SQL: actualiza atendida = 1 según id_recomendacion
        return $stmt->execute(['id' => $id]); // Ejecuta y retorna true si se actualizó
    } // Fin del método markAsResolved
} // Fin de la clase RecomendacionIA
