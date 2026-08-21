<?php
$pageTitle = 'Control y Gestión de Inventario';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Estado de Existencias de Inventario</h5>
            <p class="text-muted small mb-0">Consulte existencias físicas y ejecute movimientos de entrada, salida o ajustes.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <?php if ($isAdmin): ?>
                <a href="<?= url('inventario/entrada') ?>" class="btn btn-success btn-sm px-3">
                    <i class="bi bi-box-arrow-in-down me-1"></i> Registrar Entrada
                </a>
                <a href="<?= url('inventario/salida') ?>" class="btn btn-danger btn-sm px-3">
                    <i class="bi bi-box-arrow-up me-1"></i> Registrar Salida
                </a>
                <a href="<?= url('inventario/ajuste') ?>" class="btn btn-warning text-dark btn-sm px-3">
                    <i class="bi bi-sliders me-1"></i> Ajuste Físico
                </a>
            <?php endif; ?>
            <a href="<?= url('inventario/historial') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-clock-history me-1"></i> Historial
            </a>
        </div>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control table-search-input" data-table="tabla-inventario" placeholder="Buscar producto por código, nombre o categoría...">
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0" id="tabla-inventario">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Condición</th>
                    <?php if ($isAdmin): ?>
                        <th class="text-end">Operaciones Rápidas</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><code><?= sanitize($p['codigo']) ?></code></td>
                        <td class="fw-semibold"><?= sanitize($p['nombre']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= sanitize($p['categoria_nombre']) ?></span></td>
                        <td><?= formatMoney($p['precio']) ?></td>
                        <td class="fw-bold fs-6">
                            <?= $p['stock_actual'] ?> uds
                        </td>
                        <td><small class="text-muted"><?= $p['stock_minimo'] ?> uds</small></td>
                        <td>
                            <?php if ($p['stock_actual'] <= 0): ?>
                                <span class="badge bg-danger">Agotado</span>
                            <?php elseif ($p['stock_actual'] <= $p['stock_minimo']): ?>
                                <span class="badge bg-warning text-dark">Stock Bajo</span>
                            <?php else: ?>
                                <span class="badge bg-success">Óptimo</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isAdmin): ?>
                            <td class="text-end">
                                <a href="<?= url('inventario/entrada?producto=' . $p['id_producto']) ?>" class="btn btn-sm btn-outline-success py-0 px-2" title="Entrada rápida">
                                    + Entrada
                                </a>
                                <a href="<?= url('inventario/ajuste?producto=' . $p['id_producto']) ?>" class="btn btn-sm btn-outline-warning text-dark py-0 px-2" title="Ajustar">
                                    Ajuste
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
