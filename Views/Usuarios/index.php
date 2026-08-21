<?php
$pageTitle = 'Gestión de Usuarios';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Usuarios del Sistema</h5>
            <p class="text-muted small mb-0">Administre cuentas de acceso, roles y estados de los operadores.</p>
        </div>
        <a href="<?= url('usuarios/create') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </a>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control table-search-input" data-table="tabla-usuarios" placeholder="Buscar por nombre, usuario, documento o rol...">
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0" id="tabla-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Documento</th>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>#<?= $u['id_usuario'] ?></td>
                        <td class="fw-semibold"><?= sanitize($u['nombre'] . ' ' . $u['apellido']) ?></td>
                        <td><?= sanitize($u['documento']) ?></td>
                        <td><code><?= sanitize($u['nombre_usuario']) ?></code></td>
                        <td><small><?= sanitize($u['correo'] ?? '-') ?></small></td>
                        <td>
                            <span class="badge-custom <?= $u['rol_nombre'] === 'ADMINISTRADOR' ? 'badge-admin' : 'badge-worker' ?>">
                                <?= sanitize($u['rol_nombre']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-custom <?= $u['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $u['estado'] ?>
                            </span>
                        </td>
                        <td><small class="text-muted"><?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca' ?></small></td>
                        <td class="text-end">
                            <a href="<?= url('usuarios/edit?id=' . $u['id_usuario']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <?php if ($u['id_usuario'] != $_SESSION['user_id']): ?>
                                <a href="<?= url('usuarios/toggle?id=' . $u['id_usuario']) ?>" class="btn btn-sm btn-outline-<?= $u['estado'] === 'ACTIVO' ? 'danger' : 'success' ?> py-0 px-2 btn-confirm" data-confirm="¿Está seguro de cambiar el estado de este usuario?" title="<?= $u['estado'] === 'ACTIVO' ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-power"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
