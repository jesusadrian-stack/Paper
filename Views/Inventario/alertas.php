<?php
$pageTitle = 'Alertas de Stock Crítico';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Alertas de Inventario Bajo</h5>
            <p class="text-muted small mb-0">Artículos cuyas existencias actuales han alcanzado o descendido por debajo del umbral mínimo de seguridad.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('ia/generar?tipo=reabastecimiento') ?>" class="btn btn-warning text-dark btn-sm fw-semibold">
                <i class="bi bi-stars me-1"></i> Sugerir Reabastecimiento con IA
            </a>
            <a href="<?= url('inventario/entrada') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Registrar Entrada
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Fecha de Alerta</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Mensaje del Sistema</th>
                    <th>Estado</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($alertas)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-5"><i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>No hay alertas de inventario pendientes. Todos los artículos tienen existencias seguras.</td></tr>
                <?php else: ?>
                    <?php foreach ($alertas as $alt): ?>
                        <tr class="<?= $alt['atendida'] ? 'opacity-75' : 'table-light' ?>">
                            <td><small><?= date('d/m/Y H:i', strtotime($alt['fecha_alerta'])) ?></small></td>
                            <td><code><?= sanitize($alt['producto_codigo']) ?></code></td>
                            <td class="fw-bold text-dark"><?= sanitize($alt['producto_nombre']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= sanitize($alt['categoria_nombre']) ?></span></td>
                            <td><span class="badge bg-danger fs-6"><?= $alt['stock_actual'] ?> uds</span></td>
                            <td><span class="badge bg-secondary"><?= $alt['stock_minimo'] ?> uds</span></td>
                            <td><small class="text-danger fw-semibold"><?= sanitize($alt['mensaje']) ?></small></td>
                            <td>
                                <?php if ($alt['atendida']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Atendida</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-dark fw-bold">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if (!$alt['atendida']): ?>
                                    <a href="<?= url('alertas/resolve?id=' . $alt['id_alerta']) ?>" class="btn btn-sm btn-outline-success py-0 px-2" title="Marcar como atendida">
                                        <i class="bi bi-check2"></i> Atender
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="bi bi-check2-all text-success"></i> Lista</span>
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
