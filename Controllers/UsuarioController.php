<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Models/Rol.php';

class UsuarioController {
    private Usuario $usuarioModel;
    private Rol $rolModel;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->usuarioModel = new Usuario();
        $this->rolModel = new Rol();
    }

    public function index(): void {
        $usuarios = $this->usuarioModel->getAll();
        require_once VIEWS_PATH . '/Usuarios/index.php';
    }

    public function create(): void {
        $roles = $this->rolModel->getAll();
        require_once VIEWS_PATH . '/Usuarios/create.php';
    }

    public function store(): void {
        $nombre = sanitize($_POST['nombre'] ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');
        $documento = sanitize($_POST['documento'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $correo = sanitize($_POST['correo'] ?? '');
        $nombre_usuario = sanitize($_POST['nombre_usuario'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $id_rol = (int)($_POST['id_rol'] ?? 0);
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($nombre) || empty($apellido) || empty($documento) || empty($nombre_usuario) || empty($contrasena) || empty($id_rol)) {
            setFlashMessage('danger', 'Por favor complete todos los campos obligatorios.');
            redirect('usuarios/create');
        }

        // Validar unicidad
        if ($this->usuarioModel->getByUsername($nombre_usuario)) {
            setFlashMessage('danger', 'El nombre de usuario ya está registrado.');
            redirect('usuarios/create');
        }
        if ($this->usuarioModel->getByDocumento($documento)) {
            setFlashMessage('danger', 'El número de documento ya está registrado.');
            redirect('usuarios/create');
        }
        if (!empty($correo) && $this->usuarioModel->getByCorreo($correo)) {
            setFlashMessage('danger', 'El correo electrónico ya está registrado.');
            redirect('usuarios/create');
        }

        try {
            $this->usuarioModel->create([
                'id_rol'         => $id_rol,
                'nombre'         => $nombre,
                'apellido'       => $apellido,
                'documento'      => $documento,
                'telefono'       => $telefono,
                'correo'         => $correo,
                'nombre_usuario' => $nombre_usuario,
                'contrasena'     => $contrasena,
                'estado'         => $estado
            ]);

            setFlashMessage('success', 'Usuario creado correctamente.');
            redirect('usuarios');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al crear usuario: ' . $e->getMessage());
            redirect('usuarios/create');
        }
    }

    public function edit(int $id): void {
        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            setFlashMessage('danger', 'Usuario no encontrado.');
            redirect('usuarios');
        }

        $roles = $this->rolModel->getAll();
        require_once VIEWS_PATH . '/Usuarios/edit.php';
    }

    public function update(int $id): void {
        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            setFlashMessage('danger', 'Usuario no encontrado.');
            redirect('usuarios');
        }

        $nombre = sanitize($_POST['nombre'] ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');
        $documento = sanitize($_POST['documento'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $correo = sanitize($_POST['correo'] ?? '');
        $nombre_usuario = sanitize($_POST['nombre_usuario'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $id_rol = (int)($_POST['id_rol'] ?? 0);
        $estado = $_POST['estado'] ?? 'ACTIVO';

        if (empty($nombre) || empty($apellido) || empty($documento) || empty($nombre_usuario) || empty($id_rol)) {
            setFlashMessage('danger', 'Por favor complete todos los campos obligatorios.');
            redirect("usuarios/edit?id={$id}");
        }

        // Validar unicidad si cambió
        $existingUser = $this->usuarioModel->getByUsername($nombre_usuario);
        if ($existingUser && $existingUser['id_usuario'] != $id) {
            setFlashMessage('danger', 'El nombre de usuario ya está en uso por otro usuario.');
            redirect("usuarios/edit?id={$id}");
        }

        $existingDoc = $this->usuarioModel->getByDocumento($documento);
        if ($existingDoc && $existingDoc['id_usuario'] != $id) {
            setFlashMessage('danger', 'El número de documento ya está en uso.');
            redirect("usuarios/edit?id={$id}");
        }

        try {
            $this->usuarioModel->update($id, [
                'id_rol'         => $id_rol,
                'nombre'         => $nombre,
                'apellido'       => $apellido,
                'documento'      => $documento,
                'telefono'       => $telefono,
                'correo'         => $correo,
                'nombre_usuario' => $nombre_usuario,
                'contrasena'     => $contrasena,
                'estado'         => $estado
            ]);

            setFlashMessage('success', 'Usuario actualizado correctamente.');
            redirect('usuarios');
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al actualizar usuario: ' . $e->getMessage());
            redirect("usuarios/edit?id={$id}");
        }
    }

    public function toggle(int $id): void {
        if ($id == $_SESSION['user_id']) {
            setFlashMessage('danger', 'No puede desactivar su propio usuario.');
            redirect('usuarios');
        }

        $this->usuarioModel->toggleStatus($id);
        setFlashMessage('success', 'Estado del usuario modificado con éxito.');
        redirect('usuarios');
    }
}
