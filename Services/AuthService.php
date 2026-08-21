<?php
require_once __DIR__ . '/../Models/Usuario.php';

class AuthService {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function login(string $username, string $password): array {
        $user = $this->usuarioModel->getByUsername($username);

        if (!$user) {
            return ['success' => false, 'message' => 'Usuario o contraseña incorrectos.'];
        }

        if ($user['estado'] !== 'ACTIVO') {
            return ['success' => false, 'message' => 'El usuario se encuentra inactivo. Contacte al administrador.'];
        }

        if (!password_verify($password, $user['contrasena'])) {
            return ['success' => false, 'message' => 'Usuario o contraseña incorrectos.'];
        }

        // Registrar último acceso
        $this->usuarioModel->updateLastAccess($user['id_usuario']);

        // Iniciar sesión
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_username'] = $user['nombre_usuario'];
        $_SESSION['user_role'] = $user['rol_nombre'];
        $_SESSION['user_role_id'] = $user['id_rol'];
        $_SESSION['user_email'] = $user['correo'];

        return ['success' => true, 'user' => $user];
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function getCurrentUser(): ?array {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return [
            'id'       => $_SESSION['user_id'],
            'name'     => $_SESSION['user_name'] ?? '',
            'username' => $_SESSION['user_username'] ?? '',
            'role'     => $_SESSION['user_role'] ?? '',
            'role_id'  => $_SESSION['user_role_id'] ?? 0,
            'email'    => $_SESSION['user_email'] ?? ''
        ];
    }

    public static function isAdmin(): bool {
        return ($_SESSION['user_role'] ?? '') === 'ADMINISTRADOR';
    }
}
