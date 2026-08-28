<?php // Apertura del script PHP
require_once __DIR__ . '/../Config/database.php'; // Incluye la conexión centralizada a la base de datos MySQL mediante PDO

class Producto { // Modelo 'Producto': gestiona el catálogo de artículos, precios e inventarios en la tabla 'producto'
    private PDO $db; // Variable que almacena la conexión PDO a MySQL

    public function __construct() { // Constructor de la clase Producto
        $this->db = Database::getConnection(); // Conecta con la base de datos a través de Database::getConnection()
    } // Fin del constructor

    public function getAll(bool $onlyActive = false, ?int $categoryId = null, ?string $search = null): array { // Obtiene los productos con filtros y el nombre de su categoría asociada
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE 1=1 "; // Conexión relacional: JOIN entre 'producto' (p) y 'categoria' (c) mediante la llave foránea p.id_categoria = c.id_categoria para traer el nombre de la categoría
        
        $params = []; // Arreglo para almacenar los parámetros dinámicos del filtro
        if ($onlyActive) { // Condicional: si solo se solicitan productos activos
            $sql .= " AND p.estado = 'ACTIVO' "; // Filtra por estado ACTIVO
        } // Fin condición onlyActive
        if ($categoryId) { // Condicional: si se filtra por una categoría en específico
            $sql .= " AND p.id_categoria = :categoryId "; // Filtra por la llave foránea id_categoria
            $params['categoryId'] = $categoryId; // Asigna el valor de la categoría
        } // Fin condición categoryId
        if (!empty($search)) { // Condicional: si se busca por texto
            $sql .= " AND (p.nombre LIKE :search1 OR p.codigo LIKE :search2 OR p.descripcion LIKE :search3) "; // Búsqueda coincidente en nombre, código de barras o descripción
            $params['search1'] = "%{$search}%"; // Parámetro para nombre
            $params['search2'] = "%{$search}%"; // Parámetro para código
            $params['search3'] = "%{$search}%"; // Parámetro para descripción
        } // Fin condición search

        $sql .= " ORDER BY p.nombre ASC"; // Ordena los productos alfabéticamente por nombre
        $stmt = $this->db->prepare($sql); // Prepara la consulta dinámica
        $stmt->execute($params); // Ejecuta con los parámetros filtrados
        return $stmt->fetchAll(); // Retorna la lista de productos con su categoría
    } // Fin del método getAll

    public function getById(int $id): ?array { // Busca un producto por su clave primaria id_producto
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.id_producto = :id LIMIT 1"; // Conexión relacional: JOIN con 'categoria' mediante id_categoria para traer información del producto y su categoría
        $stmt = $this->db->prepare($sql); // Prepara la consulta parametrizada
        $stmt->execute(['id' => $id]); // Ejecuta vinculando el parámetro :id
        $prod = $stmt->fetch(); // Extrae la fila del producto
        return $prod ?: null; // Retorna los datos del producto o null si no existe
    } // Fin del método getById

    public function getByCodigo(string $codigo): ?array { // Busca un producto por su código único de barras o referencia
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.codigo = :codigo LIMIT 1"; // Conexión relacional: JOIN con 'categoria' filtrando por la columna 'codigo'
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        $stmt->execute(['codigo' => $codigo]); // Ejecuta vinculando el código
        $prod = $stmt->fetch(); // Extrae el registro
        return $prod ?: null; // Retorna los datos o null si no se encuentra
    } // Fin del método getByCodigo

    public function getLowStockProducts(): array { // Consulta productos con stock bajo o agotado para alertas y reabastecimiento
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.estado = 'ACTIVO' AND p.stock_actual <= p.stock_minimo 
                ORDER BY p.stock_actual ASC"; // Conexión relacional: JOIN con 'categoria', filtrando productos donde stock_actual es menor o igual al stock_minimo
        $stmt = $this->db->query($sql); // Ejecuta la consulta
        return $stmt->fetchAll(); // Retorna los productos con bajo stock
    } // Fin del método getLowStockProducts

    public function countTotal(): int { // Cuenta el número total de productos activos en el catálogo
        $stmt = $this->db->query("SELECT COUNT(*) FROM producto WHERE estado = 'ACTIVO'"); // Consulta directa: conteo de productos con estado ACTIVO
        return (int)$stmt->fetchColumn(); // Retorna el número entero de productos
    } // Fin del método countTotal

    public function countLowStock(): int { // Cuenta cuántos productos activos tienen stock crítico
        $stmt = $this->db->query("SELECT COUNT(*) FROM producto WHERE estado = 'ACTIVO' AND stock_actual <= stock_minimo"); // Consulta SQL: cuenta productos activos con stock_actual <= stock_minimo
        return (int)$stmt->fetchColumn(); // Retorna el total de productos en riesgo de agotarse
    } // Fin del método countLowStock

    public function create(array $data): int { // Inserta un nuevo producto vinculándolo con su categoría correspondiente
        $sql = "INSERT INTO producto (id_categoria, codigo, nombre, descripcion, precio, stock_actual, stock_minimo, estado, fecha_registro) 
                VALUES (:id_categoria, :codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, :estado, NOW())"; // Sentencia SQL con llave foránea: Vincula el producto a su categoría mediante id_categoria
        
        $stmt = $this->db->prepare($sql); // Prepara la sentencia de inserción
        $stmt->execute([ // Ejecuta vinculando los datos del producto
            'id_categoria' => $data['id_categoria'], // Llave foránea vinculada a la tabla 'categoria'
            'codigo'       => $data['codigo'], // Código único o código de barras
            'nombre'       => $data['nombre'], // Nombre del producto
            'descripcion'  => $data['descripcion'] ?? null, // Descripción detallada o null
            'precio'       => $data['precio'], // Precio unitario de venta
            'stock_actual' => $data['stock_actual'] ?? 0, // Stock inicial (por defecto 0)
            'stock_minimo' => $data['stock_minimo'] ?? 0, // Umbral mínimo de stock para alertas
            'estado'       => $data['estado'] ?? 'ACTIVO' // Estado inicial (por defecto 'ACTIVO')
        ]); // Fin de ejecución

        return (int)$this->db->lastInsertId(); // Retorna el id_producto generado
    } // Fin del método create

    public function update(int $id, array $data): bool { // Actualiza los datos generales de un producto
        $sql = "UPDATE producto SET 
                    id_categoria = :id_categoria, 
                    codigo = :codigo, 
                    nombre = :nombre, 
                    descripcion = :descripcion, 
                    precio = :precio, 
                    stock_minimo = :stock_minimo, 
                    estado = :estado,
                    fecha_actualizacion = NOW() 
                WHERE id_producto = :id"; // Sentencia SQL: actualiza campos y llave foránea id_categoria para el id_producto correspondiente
        
        $stmt = $this->db->prepare($sql); // Prepara la actualización
        return $stmt->execute([ // Ejecuta con los nuevos valores
            'id'           => $id, // ID del producto a modificar
            'id_categoria' => $data['id_categoria'], // Nueva llave foránea de categoría
            'codigo'       => $data['codigo'], // Nuevo código
            'nombre'       => $data['nombre'], // Nuevo nombre
            'descripcion'  => $data['descripcion'] ?? null, // Nueva descripción o null
            'precio'       => $data['precio'], // Nuevo precio
            'stock_minimo' => $data['stock_minimo'] ?? 0, // Nuevo stock mínimo
            'estado'       => $data['estado'] ?? 'ACTIVO' // Nuevo estado
        ]); // Retorna true si se actualizó con éxito
    } // Fin del método update

    public function updateStock(int $id, int $newStock): bool { // Actualiza únicamente las existencias físicas de un producto
        if ($newStock < 0) { // Valida que el stock no sea un valor negativo
            throw new InvalidArgumentException("El stock no puede ser negativo."); // Lanza error si el stock es inválido
        } // Fin validación
        $sql = "UPDATE producto SET stock_actual = :stock, fecha_actualizacion = NOW() WHERE id_producto = :id"; // Sentencia SQL: actualiza el stock_actual y la fecha_actualizacion
        $stmt = $this->db->prepare($sql); // Prepara la consulta
        return $stmt->execute(['stock' => $newStock, 'id' => $id]); // Ejecuta y retorna true
    } // Fin del método updateStock

    public function toggleStatus(int $id): bool { // Alterna el estado del producto entre ACTIVO e INACTIVO
        $prod = $this->getById($id); // Consulta el producto para saber su estado actual
        if (!$prod) return false; // Si no existe el producto, retorna false
        $newStatus = ($prod['estado'] === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO'; // Invierte el estado
        $stmt = $this->db->prepare("UPDATE producto SET estado = :estado, fecha_actualizacion = NOW() WHERE id_producto = :id"); // Prepara la actualización de estado
        return $stmt->execute(['estado' => $newStatus, 'id' => $id]); // Ejecuta el cambio de estado
    } // Fin del método toggleStatus
} // Fin de la clase Producto
