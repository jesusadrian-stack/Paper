<?php
$pageTitle = 'Finanzas y Gestión de Cuentas';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="row g-4 mb-4">
    <?php foreach ($cuentas as $c): ?>
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon <?= $c['tipo'] === 'PAPELERIA' ? 'success' : 'info' ?> fs-3">
                        <i class="bi <?= $c['tipo'] === 'PAPELERIA' ? 'bi-cash-stack' : 'bi-bank2' ?>"></i>
                    </div>
                    <span class="badge-custom <?= $c['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $c['estado'] ?>
                    </span>
                </div>

                <small class="text-muted text-uppercase fw-semibold">Cuenta: <?= $c['tipo'] ?></small>
                <h5 class="fw-bold text-dark mb-2"><?= sanitize($c['nombre']) ?></h5>
                <h2 class="fw-bold <?= $c['tipo'] === 'PAPELERIA' ? 'text-success' : 'text-primary' ?> mb-3">
                    <?= formatMoney($c['saldo']) ?>
                </h2>

                <div class="d-flex gap-2 mt-auto pt-3 border-top">
                    <?php if ($c['tipo'] === 'PAPELERIA'): ?>
                        <a href="<?= url('cuentas/papeleria') ?>" class="btn btn-outline-success btn-sm flex-grow-1">Ver Movimientos Papelería</a>
                    <?php else: ?>
                        <a href="<?= url('cuentas/corresponsal') ?>" class="btn btn-outline-primary btn-sm flex-grow-1">Ver Movimientos Corresponsal</a>
                    <?php endif; ?>
                    <a href="<?= url('transferencias/create') ?>" class="btn btn-outline-secondary btn-sm" title="Transferir fondos">
                        <i class="bi bi-send"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Últimos Movimientos Generales -->
<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-arrow-left-right text-primary me-2"></i>Últimos Movimientos Financieros</h6>
        <a href="<?= url('cuentas/movimientos') ?>" class="btn btn-sm btn-link text-decoration-none">Ver todos los movimientos &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cuenta</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Valor</th>
                    <th>Saldo Resultante</th>
                    <th>Operador</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimientos)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay movimientos financieros registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><small><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])) ?></small></td>
                            <td><span class="badge bg-light text-dark border"><?= sanitize($m['cuenta_nombre']) ?></span></td>
                            <td>
                                <?php if ($m['tipo'] === 'INGRESO' || $m['tipo'] === 'DEPOSITO'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">+ <?= $m['tipo'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">- <?= $m['tipo'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= sanitize($m['concepto']) ?></small></td>
                            <td class="fw-bold <?= in_array($m['tipo'], ['INGRESO', 'DEPOSITO']) ? 'text-success' : 'text-danger' ?>">
                                <?= formatMoney($m['valor']) ?>
                            </td>
                            <td class="fw-semibold text-dark"><?= formatMoney($m['saldo_nuevo']) ?></td>
                            <td><small class="text-muted"><?= sanitize($m['usuario_nombre']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
