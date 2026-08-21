<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Models/Cliente.php';

class ClienteController {
    private Cliente $clienteModel;

    public function __construct() {
        AuthMiddleware::check();
        $this->clienteModel = new Cliente();
    }

    public function index(): void {
        $search = sanitize($_GET['buscar'] ?? '');
        $clientes = $this->clienteModel->getAll(false, $search);
        require_once VIEWS_PATH . '/Clientes/index.php';
    }

    public function show(int $id): void {
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente) {
            setFlashMessage('danger', 'Cliente no encontrado.');
            redirect('clientes');
        }
        require_once VIEWS_PATH . '/Clientes/show.php';
    }

    public function create(): void {
        require_once VIEWS_PATH . '/Clientes/create.php';
    }

    public function store(): void {
        $tipo = $_POST['tipo_identificacion'] ?? 'CC';
        $num = sanitize($_POST['numero_identificacion'] ?? '');
        $nombre = sanitize($_POST['nombre'] ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $correo = sanitize($_POST['correo'] ?? '');
        $direccion = sanitize($_POST['direccion'] ?? '');
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($num) || empty($nombre)) {
            setFlashMessage('danger', 'El número de identificación y el nombre son obligatorios.');
            redirect('clientes/create');
        }

        if ($this->clienteModel->getByIdentificacion($num)) {
            setFlashMessage('danger', 'El número de identificación ya está registrado.');
            redirect('clientes/create');
        }

        try {
            $this->clienteModel->create([
                'tipo_identificacion'   => $tipo,
                'numero_identificacion' => $num,
                'nombre'                => $nombre,
                'apellido'              => $apellido,
                'telefono'              => $telefono,
                'correo'                => $correo,
                'direccion'             => $direccion,
                'estado'                => $estado
            ]);

            setFlashMessage('success', 'Cliente registrado correctamente.');
            redirect('clientes');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al registrar cliente: ' . $e->getMessage());
            redirect('clientes/create');
        }
    }

    public function edit(int $id): void {
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente) {
            setFlashMessage('danger', 'Cliente no encontrado.');
            redirect('clientes');
        }
        require_once VIEWS_PATH . '/Clientes/edit.php';
    }

    public function update(int $id): void {
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente) {
            setFlashMessage('danger', 'Cliente no encontrado.');
            redirect('clientes');
        }

        $tipo = $_POST['tipo_identificacion'] ?? 'CC';
        $num = sanitize($_POST['numero_identificacion'] ?? '');
        $nombre = sanitize($_POST['nombre'] ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $correo = sanitize($_POST['correo'] ?? '');
        $direccion = sanitize($_POST['direccion'] ?? '');
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($num) || empty($nombre)) {
            setFlashMessage('danger', 'El número de identificación y el nombre son obligatorios.');
            redirect("clientes/edit?id={$id}");
        }

        $existing = $this->clienteModel->getByIdentificacion($num);
        if ($existing && $existing['id_cliente'] != $id) {
            setFlashMessage('danger', 'El número de identificación ya pertenece a otro cliente.');
            redirect("clientes/edit?id={$id}");
        }

        try {
            $this->clienteModel->update($id, [
                'tipo_identificacion'   => $tipo,
                'numero_identificacion' => $num,
                'nombre'                => $nombre,
                'apellido'              => $apellido,
                'telefono'              => $telefono,
                'correo'                => $correo,
                'direccion'             => $direccion,
                'estado'                => $estado
            ]);

            setFlashMessage('success', 'Cliente actualizado correctamente.');
            redirect('clientes');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al actualizar cliente: ' . $e->getMessage());
            redirect("clientes/edit?id={$id}");
        }
    }

    public function toggle(int $id): void {
        $this->clienteModel->toggleStatus($id);
        setFlashMessage('success', 'Estado del cliente actualizado.');
        redirect('clientes');
    }

    // Endpoint API para autocompletar clientes en el POS / Corresponsal
    public function searchApi(): void {
        $query = sanitize($_GET['q'] ?? '');
        $clientes = $this->clienteModel->getAll(true, $query);
        jsonResponse($clientes);
    }
}
