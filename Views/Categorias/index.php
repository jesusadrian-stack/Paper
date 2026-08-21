<?php
$pageTitle = 'Categorías de Productos';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Categorías de Artículos</h5>
            <p class="text-muted small mb-0">Organice y clasifique el catálogo de productos de la papelería.</p>
        </div>
        <a href="<?= url('categorias/create') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-tag-fill me-1"></i> Nueva Categoría
        </a>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control table-search-input" data-table="tabla-categorias" placeholder="Buscar categoría...">
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0" id="tabla-categorias">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Total Productos</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td>#<?= $c['id_categoria'] ?></td>
                        <td class="fw-bold text-dark"><?= sanitize($c['nombre']) ?></td>
                        <td><small class="text-muted"><?= sanitize($c['descripcion'] ?? '-') ?></small></td>
                        <td><span class="badge bg-light text-dark border"><?= $c['total_productos'] ?? 0 ?> productos</span></td>
                        <td>
                            <span class="badge-custom <?= $c['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $c['estado'] ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= url('categorias/edit?id=' . $c['id_categoria']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="<?= url('categorias/toggle?id=' . $c['id_categoria']) ?>" class="btn btn-sm btn-outline-<?= $c['estado'] === 'ACTIVO' ? 'danger' : 'success' ?> py-0 px-2 btn-confirm" data-confirm="¿Está seguro de cambiar el estado de esta categoría?" title="<?= $c['estado'] === 'ACTIVO' ? 'Desactivar' : 'Activar' ?>">
                                <i class="bi bi-power"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
