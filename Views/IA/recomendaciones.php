<?php
$pageTitle = 'Recomendaciones Estratégicas IA';
require_once VIEWS_PATH . '/Layouts/header.php';
$filter = $_GET['estado'] ?? 'pendientes';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Recomendaciones de Negocio Generadas por IA</h5>
            <p class="text-muted small mb-0">Plan de acción sugerido para reabastecimiento, ajuste de márgenes y promociones.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="btn-group btn-group-sm" role="group">
                <a href="<?= url('ia/recomendaciones?estado=pendientes') ?>" class="btn <?= $filter === 'pendientes' ? 'btn-primary' : 'btn-outline-primary' ?>">Pendientes</a>
                <a href="<?= url('ia/recomendaciones?estado=todas') ?>" class="btn <?= $filter === 'todas' ? 'btn-primary' : 'btn-outline-primary' ?>">Todas</a>
            </div>
            <a href="<?= url('ia') ?>" class="btn btn-outline-secondary btn-sm">Volver a Diagnósticos</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Prioridad</th>
                    <th>Tipo</th>
                    <th>Artículo / Referencia</th>
                    <th>Recomendación Accionable</th>
                    <th>Estado</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recomendaciones)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>No hay recomendaciones en este estado.</td></tr>
                <?php else: ?>
                    <?php foreach ($recomendaciones as $rec): ?>
                        <tr class="<?= $rec['atendida'] ? 'opacity-75' : '' ?>">
                            <td><small><?= date('d/m/Y H:i', strtotime($rec['fecha_recomendacion'])) ?></small></td>
                            <td>
                                <span class="badge bg-<?= $rec['prioridad'] === 'ALTA' ? 'danger' : 'warning' ?>-subtle text-<?= $rec['prioridad'] === 'ALTA' ? 'danger' : 'dark' ?> fw-bold">
                                    <?= $rec['prioridad'] ?>
                                </span>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= $rec['tipo'] ?></span></td>
                            <td>
                                <?php if (!empty($rec['producto_nombre'])): ?>
                                    <strong><?= sanitize($rec['producto_nombre']) ?></strong>
                                    <small class="text-muted d-block">Código: <?= sanitize($rec['producto_codigo']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Estrategia General</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 400px;"><?= sanitize($rec['recomendacion']) ?></td>
                            <td>
                                <?php if ($rec['atendida']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Atendida</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-dark fw-bold">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if (!$rec['atendida']): ?>
                                    <a href="<?= url('ia/recomendaciones/resolve?id=' . $rec['id_recomendacion']) ?>" class="btn btn-sm btn-outline-success py-0 px-2" title="Marcar como atendida">
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
