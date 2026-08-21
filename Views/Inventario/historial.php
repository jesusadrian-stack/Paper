<?php
$pageTitle = 'Historial de Movimientos de Inventario';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Kárdex y Movimientos de Inventario</h5>
            <p class="text-muted small mb-0">Trazabilidad inmutable de todas las entradas, salidas, ventas y ajustes de productos.</p>
        </div>
        <a href="<?= url('inventario') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver a Inventario
        </a>
    </div>

    <!-- Filtros -->
    <form action="<?= url('inventario/historial') ?>" method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-3">
            <select class="form-select" name="producto">
                <option value="">-- Todos los Productos --</option>
                <?php foreach ($productos as $pr): ?>
                    <option value="<?= $pr['id_producto'] ?>" <?= (isset($_GET['producto']) && $_GET['producto'] == $pr['id_producto']) ? 'selected' : '' ?>>
                        <?= sanitize($pr['codigo']) ?> - <?= sanitize($pr['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <select class="form-select" name="tipo">
                <option value="">-- Tipo --</option>
                <option value="ENTRADA" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'ENTRADA') ? 'selected' : '' ?>>ENTRADA</option>
                <option value="SALIDA" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'SALIDA') ? 'selected' : '' ?>>SALIDA</option>
                <option value="AJUSTE" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'AJUSTE') ? 'selected' : '' ?>>AJUSTE</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <input type="date" class="form-control" name="fecha_inicio" value="<?= sanitize($_GET['fecha_inicio'] ?? '') ?>" placeholder="Desde">
        </div>

        <div class="col-6 col-md-2">
            <input type="date" class="form-control" name="fecha_fin" value="<?= sanitize($_GET['fecha_fin'] ?? '') ?>" placeholder="Hasta">
        </div>

        <div class="col-6 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filtrar</button>
            <a href="<?= url('inventario/historial') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Stock Anterior</th>
                    <th>Stock Nuevo</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimientos)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No hay movimientos de inventario que coincidan con el filtro.</td></tr>
                <?php else: ?>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><small><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])) ?></small></td>
                            <td><code><?= sanitize($m['producto_codigo']) ?></code></td>
                            <td class="fw-semibold"><?= sanitize($m['producto_nombre']) ?></td>
                            <td>
                                <?php if ($m['tipo'] === 'ENTRADA'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-arrow-down-left"></i> ENTRADA</span>
                                <?php elseif ($m['tipo'] === 'SALIDA'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-arrow-up-right"></i> SALIDA</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-dark"><i class="bi bi-sliders"></i> AJUSTE</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= $m['cantidad'] ?> uds</td>
                            <td><?= $m['stock_anterior'] ?></td>
                            <td class="fw-bold <?= $m['stock_nuevo'] <= 5 ? 'text-danger' : 'text-dark' ?>"><?= $m['stock_nuevo'] ?></td>
                            <td><small class="text-muted"><?= sanitize($m['motivo'] ?? '-') ?></small></td>
                            <td><small><?= sanitize($m['usuario_nombre']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
