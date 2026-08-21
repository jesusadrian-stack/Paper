<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/HistorialPrecio.php';
require_once __DIR__ . '/../Services/InventarioService.php';

class ProductoController {
    private Producto $productoModel;
    private Categoria $categoriaModel;
    private HistorialPrecio $historialPrecioModel;
    private InventarioService $inventarioService;

    public function __construct() {
        AuthMiddleware::check();
        $this->productoModel = new Producto();
        $this->categoriaModel = new Categoria();
        $this->historialPrecioModel = new HistorialPrecio();
        $this->inventarioService = new InventarioService();
    }

    public function index(): void {
        $categoryId = !empty($_GET['categoria']) ? (int)$_GET['categoria'] : null;
        $search = sanitize($_GET['buscar'] ?? '');
        $isAdmin = ($_SESSION['user_role'] ?? '') === 'ADMINISTRADOR';

        $productos = $this->productoModel->getAll(false, $categoryId, $search);
        $categorias = $this->categoriaModel->getAll(true);

        require_once VIEWS_PATH . '/Productos/index.php';
    }

    public function show(int $id): void {
        $producto = $this->productoModel->getById($id);
        if (!$producto) {
            setFlashMessage('danger', 'Producto no encontrado.');
            redirect('productos');
        }

        $historialPrecios = $this->historialPrecioModel->getByProductId($id);
        require_once VIEWS_PATH . '/Productos/show.php';
    }

    public function create(): void {
        RoleMiddleware::adminOnly();
        $categorias = $this->categoriaModel->getAll(true);
        require_once VIEWS_PATH . '/Productos/create.php';
    }

    public function store(): void {
        RoleMiddleware::adminOnly();

        $codigo = strtoupper(sanitize($_POST['codigo'] ?? ''));
        $nombre = sanitize($_POST['nombre'] ?? '');
        $id_categoria = (int)($_POST['id_categoria'] ?? 0);
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        $stock_actual = (int)($_POST['stock_actual'] ?? 0);
        $stock_minimo = (int)($_POST['stock_minimo'] ?? 0);
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($codigo) || empty($nombre) || empty($id_categoria) || $precio <= 0) {
            setFlashMessage('danger', 'Por favor complete todos los campos obligatorios y asegúrese de que el precio sea mayor a cero.');
            redirect('productos/create');
        }

        if ($this->productoModel->getByCodigo($codigo)) {
            setFlashMessage('danger', 'El código de producto ya está registrado.');
            redirect('productos/create');
        }

        try {
            $productId = $this->productoModel->create([
                'id_categoria' => $id_categoria,
                'codigo'       => $codigo,
                'nombre'       => $nombre,
                'descripcion'  => $descripcion,
                'precio'       => $precio,
                'stock_actual' => $stock_actual,
                'stock_minimo' => $stock_minimo,
                'estado'       => $estado
            ]);

            // Registrar precio inicial en historial
            $this->historialPrecioModel->create($productId, $_SESSION['user_id'], null, $precio);

            // Si se registró con stock inicial mayor a cero, registrar movimiento de entrada
            if ($stock_actual > 0) {
                require_once __DIR__ . '/../Models/MovimientoInventario.php';
                $movModel = new MovimientoInventario();
                $movModel->create([
                    'id_producto'    => $productId,
                    'id_usuario'     => $_SESSION['user_id'],
                    'tipo'           => 'ENTRADA',
                    'cantidad'       => $stock_actual,
                    'stock_anterior' => 0,
                    'stock_nuevo'    => $stock_actual,
                    'motivo'         => 'Inventario inicial al crear producto'
                ]);
            }

            setFlashMessage('success', 'Producto registrado exitosamente.');
            redirect('productos');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al registrar producto: ' . $e->getMessage());
            redirect('productos/create');
        }
    }

    public function edit(int $id): void {
        RoleMiddleware::adminOnly();
        $producto = $this->productoModel->getById($id);
        if (!$producto) {
            setFlashMessage('danger', 'Producto no encontrado.');
            redirect('productos');
        }

        $categorias = $this->categoriaModel->getAll(true);
        require_once VIEWS_PATH . '/Productos/edit.php';
    }

    public function update(int $id): void {
        RoleMiddleware::adminOnly();

        $producto = $this->productoModel->getById($id);
        if (!$producto) {
            setFlashMessage('danger', 'Producto no encontrado.');
            redirect('productos');
        }

        $codigo = strtoupper(sanitize($_POST['codigo'] ?? ''));
        $nombre = sanitize($_POST['nombre'] ?? '');
        $id_categoria = (int)($_POST['id_categoria'] ?? 0);
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        $stock_minimo = (int)($_POST['stock_minimo'] ?? 0);
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($codigo) || empty($nombre) || empty($id_categoria) || $precio <= 0) {
            setFlashMessage('danger', 'Por favor complete todos los campos obligatorios.');
            redirect("productos/edit?id={$id}");
        }

        $existing = $this->productoModel->getByCodigo($codigo);
        if ($existing && $existing['id_producto'] != $id) {
            setFlashMessage('danger', 'El código ya pertenece a otro producto.');
            redirect("productos/edit?id={$id}");
        }

        $res = $this->inventarioService->actualizarProductoConPrecio($id, [
            'id_categoria' => $id_categoria,
            'codigo'       => $codigo,
            'nombre'       => $nombre,
            'descripcion'  => $descripcion,
            'precio'       => $precio,
            'stock_minimo' => $stock_minimo,
            'estado'       => $estado
        ], $_SESSION['user_id']);

        if ($res['success']) {
            setFlashMessage('success', 'Producto actualizado correctamente.');
            redirect('productos');
        } else {
            setFlashMessage('danger', $res['message']);
            redirect("productos/edit?id={$id}");
        }
    }

    public function toggle(int $id): void {
        RoleMiddleware::adminOnly();
        $this->productoModel->toggleStatus($id);
        setFlashMessage('success', 'Estado del producto actualizado.');
        redirect('productos');
    }

    // Endpoint API/AJAX para búsqueda rápida en el POS
    public function searchApi(): void {
        $query = sanitize($_GET['q'] ?? '');
        $productos = $this->productoModel->getAll(true, null, $query);
        jsonResponse($productos);
    }
}
