<?php
require_once __DIR__ . '/AuthMiddleware.php';

class RoleMiddleware {
    public static function check(array|string $allowedRoles): void {
        AuthMiddleware::check();

        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        $userRole = $_SESSION['user_role'] ?? '';

        if (!in_array($userRole, $allowedRoles)) {
            setFlashMessage('danger', 'Acceso denegado. No tiene los permisos necesarios para realizar esta acción.');
            redirect('dashboard');
        }
    }

    public static function adminOnly(): void {
        self::check(['ADMINISTRADOR']);
    }

    public static function workerOrAdmin(): void {
        self::check(['ADMINISTRADOR', 'TRABAJADOR']);
    }
}
