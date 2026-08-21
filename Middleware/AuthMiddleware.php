<?php
require_once __DIR__ . '/../Config/config.php';

class AuthMiddleware {
    public static function check(): void {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            setFlashMessage('warning', 'Debe iniciar sesión para acceder al sistema.');
            redirect('auth/login');
        }
    }

    public static function guest(): void {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            redirect('dashboard');
        }
    }
}
