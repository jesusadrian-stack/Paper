<?php
require_once __DIR__ . '/../Middleware/RoleMiddleware.php';
require_once __DIR__ . '/../Models/Rol.php';

class RolController {
    private Rol $rolModel;

    public function __construct() {
        RoleMiddleware::adminOnly();
        $this->rolModel = new Rol();
    }

    public function index(): void {
        $roles = $this->rolModel->getAll();
        require_once VIEWS_PATH . '/Roles/index.php';
    }
}
