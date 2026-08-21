<?php
$pageTitle = 'Catálogo de Productos';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Catálogo de Productos</h5>
            <p class="text-muted small mb-0">Gestión de artículos, precios, existencias y umbrales mínimos de stock.</p>
        </div>
        <?php if ($isAdmin): ?>
            <div class="d-flex gap-2">
                <a href="<?= url('productos/historial-precios') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-clock-history me-1"></i> Historial Precios
                </a>
                <a href="<?= url('productos/create') ?>" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-plus-circle-fill me-1"></i> Nuevo Producto
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Filtros de búsqueda -->
    <form action="<?= url('productos') ?>" method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-5">
            <input type="text" class="form-control" name="buscar" value="<?= sanitize($_GET['buscar'] ?? '') ?>" placeholder="Buscar por código, nombre o descripción...">
        </div>
        <div class="col-12 col-md-4">
            <select class="form-select" name="categoria">
                <option value="">-- Todas las Categorías --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                        <?= sanitize($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filtrar</button>
            <a href="<?= url('productos') ?>" class="btn btn-outline-secondary" title="Limpiar filtros"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width: 60px;">Foto</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio Venta</th>
                    <th>Stock Actual</th>
                    <th>Stock Mín.</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron productos que coincidan con la búsqueda.</td></tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td>
                                <img src="<?= getProductImage($p) ?>" alt="Producto" class="rounded-3 shadow-sm border" style="width: 44px; height: 44px; object-fit: cover;">
                            </td>
                            <td><code><?= sanitize($p['codigo']) ?></code></td>
                            <td>
                                <a href="<?= url('productos/show?id=' . $p['id_producto']) ?>" class="fw-bold text-decoration-none text-dark">
                                    <?= sanitize($p['nombre']) ?>
                                </a>
                                <?php if (!empty($p['descripcion'])): ?>
                                    <div class="small text-muted text-truncate" style="max-width: 250px;"><?= sanitize($p['descripcion']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= sanitize($p['categoria_nombre']) ?></span></td>
                            <td class="fw-bold text-primary"><?= formatMoney($p['precio']) ?></td>
                            <td>
                                <?php if ($p['stock_actual'] <= $p['stock_minimo']): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill"></i> <?= $p['stock_actual'] ?> uds
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <?= $p['stock_actual'] ?> uds
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= $p['stock_minimo'] ?> uds</small></td>
                            <td>
                                <span class="badge-custom <?= $p['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $p['estado'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('productos/show?id=' . $p['id_producto']) ?>" class="btn btn-sm btn-outline-info py-0 px-2" title="Detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($isAdmin): ?>
                                    <a href="<?= url('productos/edit?id=' . $p['id_producto']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= url('productos/toggle?id=' . $p['id_producto']) ?>" class="btn btn-sm btn-outline-<?= $p['estado'] === 'ACTIVO' ? 'danger' : 'success' ?> py-0 px-2 btn-confirm" data-confirm="¿Está seguro de cambiar el estado de este producto?" title="<?= $p['estado'] === 'ACTIVO' ? 'Desactivar' : 'Activar' ?>">
                                        <i class="bi bi-power"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
