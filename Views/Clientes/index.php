<?php
$pageTitle = 'Directorio de Clientes';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Directorio de Clientes</h5>
            <p class="text-muted small mb-0">Gestión de clientes para compras en papelería y operaciones en corresponsal bancario.</p>
        </div>
        <a href="<?= url('clientes/create') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Registrar Cliente
        </a>
    </div>

    <!-- Búsqueda -->
    <form action="<?= url('clientes') ?>" method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-9">
            <input type="text" class="form-control" name="buscar" value="<?= sanitize($_GET['buscar'] ?? '') ?>" placeholder="Buscar por identificación, nombre, teléfono o correo...">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Buscar</button>
            <a href="<?= url('clientes') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Identificación</th>
                    <th>Nombre Completo</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No se encontraron clientes registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= $c['tipo_identificacion'] ?></span></td>
                            <td><code><?= sanitize($c['numero_identificacion']) ?></code></td>
                            <td class="fw-bold">
                                <a href="<?= url('clientes/show?id=' . $c['id_cliente']) ?>" class="text-decoration-none text-dark">
                                    <?= sanitize($c['nombre'] . ' ' . ($c['apellido'] ?? '')) ?>
                                </a>
                            </td>
                            <td><?= sanitize($c['telefono'] ?? '-') ?></td>
                            <td><small><?= sanitize($c['correo'] ?? '-') ?></small></td>
                            <td><small class="text-muted"><?= sanitize($c['direccion'] ?? '-') ?></small></td>
                            <td>
                                <span class="badge-custom <?= $c['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $c['estado'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('clientes/show?id=' . $c['id_cliente']) ?>" class="btn btn-sm btn-outline-info py-0 px-2" title="Detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= url('clientes/edit?id=' . $c['id_cliente']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="<?= url('clientes/toggle?id=' . $c['id_cliente']) ?>" class="btn btn-sm btn-outline-<?= $c['estado'] === 'ACTIVO' ? 'danger' : 'success' ?> py-0 px-2 btn-confirm" data-confirm="¿Desea cambiar el estado del cliente?" title="<?= $c['estado'] === 'ACTIVO' ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-power"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
