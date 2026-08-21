<?php
$pageTitle = 'Transferencias entre Cuentas';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Transferencias entre Papelería y Corresponsal</h5>
            <p class="text-muted small mb-0">Traspasos de liquidez entre la caja de la papelería y el fondo de corresponsal bancario.</p>
        </div>
        <a href="<?= url('transferencias/create') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-send-fill me-1"></i> Nueva Transferencia
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha / Hora</th>
                    <th>Cuenta Origen</th>
                    <th>Cuenta Destino</th>
                    <th>Monto Transferido</th>
                    <th>Concepto</th>
                    <th>Autorizado Por</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transferencias)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay transferencias entre cuentas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($transferencias as $t): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border">#<?= $t['id_transferencia'] ?></span></td>
                            <td><small><?= date('d/m/Y H:i', strtotime($t['fecha_transferencia'])) ?></small></td>
                            <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle"><?= sanitize($t['cuenta_origen_nombre']) ?></span></td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle"><?= sanitize($t['cuenta_destino_nombre']) ?></span></td>
                            <td class="fw-bold text-primary"><?= formatMoney($t['valor']) ?></td>
                            <td><small class="text-muted"><?= sanitize($t['concepto'] ?? '-') ?></small></td>
                            <td><small><?= sanitize($t['usuario_nombre']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
