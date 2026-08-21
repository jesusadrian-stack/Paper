<?php
require_once __DIR__ . '/../Services/AuthService.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class AuthController {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function showLogin(): void {
        AuthMiddleware::guest();
        require_once VIEWS_PATH . '/Auth/login.php';
    }

    public function login(): void {
        AuthMiddleware::guest();

        $username = sanitize($_POST['nombre_usuario'] ?? '');
        $password = $_POST['contrasena'] ?? '';

        if (empty($username) || empty($password)) {
            setFlashMessage('danger', 'Por favor complete todos los campos.');
            redirect('auth/login');
        }

        $result = $this->authService->login($username, $password);

        if ($result['success']) {
            setFlashMessage('success', "¡Bienvenido/a, {$result['user']['nombre']}!");
            redirect('dashboard');
        } else {
            setFlashMessage('danger', $result['message']);
            redirect('auth/login');
        }
    }

    public function showRegister(): void {
        AuthMiddleware::guest();
        require_once __DIR__ . '/../Models/Rol.php';
        $rolModel = new Rol();
        $roles = $rolModel->getAll();
        require_once VIEWS_PATH . '/Auth/register.php';
    }

    public function register(): void {
        AuthMiddleware::guest();
        require_once __DIR__ . '/../Models/Usuario.php';
        require_once __DIR__ . '/../Models/Rol.php';

        $nombre = sanitize($_POST['nombre'] ?? '');
        $apellido = sanitize($_POST['apellido'] ?? '');
        $documento = sanitize($_POST['documento'] ?? '');
        $telefono = sanitize($_POST['telefono'] ?? '');
        $correo = sanitize($_POST['correo'] ?? '');
        $nombre_usuario = sanitize($_POST['nombre_usuario'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
        $id_rol = (int)($_POST['id_rol'] ?? 2); // Por defecto TRABAJADOR

        if (empty($nombre) || empty($apellido) || empty($documento) || empty($nombre_usuario) || empty($contrasena)) {
            setFlashMessage('danger', 'Por favor complete todos los campos obligatorios.');
            redirect('auth/register');
        }

        if ($contrasena !== $confirmar_contrasena) {
            setFlashMessage('danger', 'Las contraseñas no coinciden.');
            redirect('auth/register');
        }

        if (strlen($contrasena) < 4) {
            setFlashMessage('danger', 'La contraseña debe tener al menos 4 caracteres.');
            redirect('auth/register');
        }

        $usuarioModel = new Usuario();

        if ($usuarioModel->getByUsername($nombre_usuario)) {
            setFlashMessage('danger', 'El nombre de usuario ya se encuentra en uso.');
            redirect('auth/register');
        }

        if ($usuarioModel->getByDocumento($documento)) {
            setFlashMessage('danger', 'El número de documento ya está registrado.');
            redirect('auth/register');
        }

        if (!empty($correo) && $usuarioModel->getByCorreo($correo)) {
            setFlashMessage('danger', 'El correo electrónico ya está registrado.');
            redirect('auth/register');
        }

        try {
            $usuarioModel->create([
                'id_rol'         => $id_rol,
                'nombre'         => $nombre,
                'apellido'       => $apellido,
                'documento'      => $documento,
                'telefono'       => $telefono,
                'correo'         => $correo,
                'nombre_usuario' => $nombre_usuario,
                'contrasena'     => $contrasena,
                'estado'         => 'ACTIVO'
            ]);

            // Iniciar sesión automáticamente tras el registro
            $loginRes = $this->authService->login($nombre_usuario, $contrasena);
            if ($loginRes['success']) {
                setFlashMessage('success', "¡Registro exitoso! Bienvenido/a al sistema, {$nombre}.");
                redirect('dashboard');
            } else {
                setFlashMessage('success', 'Usuario registrado correctamente. Inicie sesión.');
                redirect('auth/login');
            }
        } catch (Exception $e) {
            setFlashMessage('danger', 'Error al registrar el usuario: ' . $e->getMessage());
            redirect('auth/register');
        }
    }

    public function logout(): void {
        $this->authService->logout();
        setFlashMessage('info', 'Sesión cerrada correctamente.');
        redirect('auth/login');
    }
}
