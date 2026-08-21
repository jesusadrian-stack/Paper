<?php
$pageTitle = 'Historial de Ventas';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Historial de Ventas Realizadas</h5>
            <p class="text-muted small mb-0">Consulte comprobantes, totales y reimprima comprobantes o tickets térmicos.</p>
        </div>
        <a href="<?= url('ventas/create') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-cart-plus-fill me-1"></i> Nueva Venta (POS)
        </a>
    </div>

    <!-- Filtros de fecha -->
    <form action="<?= url('ventas') ?>" method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-4">
            <input type="date" class="form-control" name="fecha_inicio" value="<?= sanitize($_GET['fecha_inicio'] ?? '') ?>" placeholder="Fecha Inicial">
        </div>
        <div class="col-12 col-md-4">
            <input type="date" class="form-control" name="fecha_fin" value="<?= sanitize($_GET['fecha_fin'] ?? '') ?>" placeholder="Fecha Final">
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-filter me-1"></i> Filtrar</button>
            <a href="<?= url('ventas') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha / Hora</th>
                    <th>Cliente</th>
                    <th>Subtotal</th>
                    <th>Total</th>
                    <th>Vendedor</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No se encontraron ventas registradas en el período seleccionado.</td></tr>
                <?php else: ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border">#<?= $v['id_venta'] ?></span></td>
                            <td><small><?= date('d/m/Y H:i', strtotime($v['fecha_venta'])) ?></small></td>
                            <td class="fw-semibold">
                                <?= sanitize($v['cliente_nombre'] ?? 'Cliente Mostrador') ?>
                                <?php if (!empty($v['numero_identificacion'])): ?>
                                    <small class="text-muted d-block">(<?= sanitize($v['numero_identificacion']) ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= formatMoney($v['subtotal']) ?></td>
                            <td class="fw-bold text-success"><?= formatMoney($v['total']) ?></td>
                            <td><small><?= sanitize($v['usuario_nombre']) ?></small></td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <?= $v['estado'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('ventas/show?id=' . $v['id_venta']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Ver Detalle">
                                    <i class="bi bi-eye"></i> Detalle
                                </a>
                                <a href="<?= url('ventas/ticket?id=' . $v['id_venta']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Imprimir Ticket">
                                    <i class="bi bi-printer"></i>
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
