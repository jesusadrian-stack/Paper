<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Categoria.php';

class CategoriaController {
    private Categoria $categoriaModel;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->categoriaModel = new Categoria();
    }

    public function index(): void {
        $categorias = $this->categoriaModel->getAll();
        require_once VIEWS_PATH . '/Categorias/index.php';
    }

    public function create(): void {
        require_once VIEWS_PATH . '/Categorias/create.php';
    }

    public function store(): void {
        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($nombre)) {
            setFlashMessage('danger', 'El nombre de la categoría es obligatorio.');
            redirect('categorias/create');
        }

        if ($this->categoriaModel->getByNombre($nombre)) {
            setFlashMessage('danger', 'Ya existe una categoría con ese nombre.');
            redirect('categorias/create');
        }

        try {
            $this->categoriaModel->create([
                'nombre'      => $nombre,
                'descripcion' => $descripcion,
                'estado'      => $estado
            ]);
            setFlashMessage('success', 'Categoría creada exitosamente.');
            redirect('categorias');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al crear la categoría: ' . $e->getMessage());
            redirect('categorias/create');
        }
    }

    public function edit(int $id): void {
        $categoria = $this->categoriaModel->getById($id);
        if (!$categoria) {
            setFlashMessage('danger', 'Categoría no encontrada.');
            redirect('categorias');
        }
        require_once VIEWS_PATH . '/Categorias/edit.php';
    }

    public function update(int $id): void {
        $categoria = $this->categoriaModel->getById($id);
        if (!$categoria) {
            setFlashMessage('danger', 'Categoría no encontrada.');
            redirect('categorias');
        }

        $nombre = sanitize($_POST['nombre'] ?? '');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($nombre)) {
            setFlashMessage('danger', 'El nombre de la categoría es obligatorio.');
            redirect("categorias/edit?id={$id}");
        }

        $existing = $this->categoriaModel->getByNombre($nombre);
        if ($existing && $existing['id_categoria'] != $id) {
            setFlashMessage('danger', 'Ya existe otra categoría con ese nombre.');
            redirect("categorias/edit?id={$id}");
        }

        try {
            $this->categoriaModel->update($id, [
                'nombre'      => $nombre,
                'descripcion' => $descripcion,
                'estado'      => $estado
            ]);
            setFlashMessage('success', 'Categoría actualizada exitosamente.');
            redirect('categorias');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al actualizar categoría: ' . $e->getMessage());
            redirect("categorias/edit?id={$id}");
        }
    }

    public function toggle(int $id): void {
        $this->categoriaModel->toggleStatus($id);
        setFlashMessage('success', 'Estado de la categoría actualizado.');
        redirect('categorias');
    }
}
