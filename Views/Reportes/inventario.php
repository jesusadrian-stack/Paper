<?php
$pageTitle = 'Reporte de Valoración de Inventario';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Reporte de Inventario y Existencias</h5>
            <p class="text-muted small mb-0">Valorización financiera del inventario físico disponible en tienda.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Imprimir</button>
            <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    <!-- Filtros -->
    <form action="<?= url('reportes/inventario') ?>" method="GET" class="row g-2 mb-4">
        <div class="col-12 col-md-5">
            <label class="form-label small text-muted">Categoría</label>
            <select class="form-select form-select-sm" name="categoria">
                <option value="">-- Todas las Categorías --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                        <?= sanitize($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-4 d-flex align-items-center pt-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="stock_bajo" name="stock_bajo" value="1" <?= !empty($_GET['stock_bajo']) ? 'checked' : '' ?>>
                <label class="form-check-label small fw-semibold text-danger" for="stock_bajo">
                    Solo artículos con stock crítico o bajo
                </label>
            </div>
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter me-1"></i> Filtrar</button>
        </div>
    </form>

    <!-- Resumen de Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Referencias Totales</small>
                <span class="fs-4 fw-bold text-dark"><?= $reporte['total_articulos'] ?></span>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Unidades Físicas</small>
                <span class="fs-4 fw-bold text-primary"><?= $reporte['unidades_totales'] ?> uds</span>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Valor Total Estimado</small>
                <span class="fs-4 fw-bold text-success"><?= formatMoney($reporte['valor_inventario_total']) ?></span>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio Unit.</th>
                    <th>Stock Físico</th>
                    <th>Stock Mín.</th>
                    <th>Valoración Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reporte['productos'] as $p): ?>
                    <tr>
                        <td><code><?= sanitize($p['codigo']) ?></code></td>
                        <td class="fw-semibold"><?= sanitize($p['nombre']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= sanitize($p['categoria_nombre']) ?></span></td>
                        <td><?= formatMoney($p['precio']) ?></td>
                        <td class="fw-bold"><?= $p['stock_actual'] ?> uds</td>
                        <td><small class="text-muted"><?= $p['stock_minimo'] ?> uds</small></td>
                        <td class="fw-bold text-success"><?= formatMoney($p['valor_total_stock']) ?></td>
                        <td>
                            <?php if ($p['stock_actual'] <= $p['stock_minimo']): ?>
                                <span class="badge bg-danger">Bajo Stock</span>
                            <?php else: ?>
                                <span class="badge bg-success">Normal</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
