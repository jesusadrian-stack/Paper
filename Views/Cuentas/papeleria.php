<?php
$pageTitle = 'Cuenta de Papelería';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon success fs-3">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <small class="text-muted text-uppercase fw-semibold">Cuenta Principal</small>
                <h4 class="fw-bold mb-0 text-dark"><?= sanitize($cuenta['nombre']) ?></h4>
            </div>
        </div>
        <div class="text-sm-end">
            <small class="text-muted d-block">Saldo Disponible</small>
            <h2 class="fw-bold text-success mb-0"><?= formatMoney($cuenta['saldo']) ?></h2>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-dark">Movimientos de Caja de Papelería</h5>
        <div class="d-flex gap-2">
            <a href="<?= url('ventas') ?>" class="btn btn-outline-success btn-sm">Ver Ventas Asociadas</a>
            <a href="<?= url('transferencias/create') ?>" class="btn btn-outline-primary btn-sm">Transferir a Corresponsal</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Valor</th>
                    <th>Saldo Anterior</th>
                    <th>Saldo Nuevo</th>
                    <th>Venta</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimientos)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay movimientos registrados en la cuenta de papelería.</td></tr>
                <?php else: ?>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><small><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])) ?></small></td>
                            <td>
                                <span class="badge bg-<?= $m['tipo'] === 'INGRESO' ? 'success' : 'danger' ?>-subtle text-<?= $m['tipo'] === 'INGRESO' ? 'success' : 'danger' ?>">
                                    <?= $m['tipo'] ?>
                                </span>
                            </td>
                            <td><small><?= sanitize($m['concepto']) ?></small></td>
                            <td class="fw-bold <?= $m['tipo'] === 'INGRESO' ? 'text-success' : 'text-danger' ?>">
                                <?= formatMoney($m['valor']) ?>
                            </td>
                            <td><?= formatMoney($m['saldo_anterior']) ?></td>
                            <td class="fw-semibold text-dark"><?= formatMoney($m['saldo_nuevo']) ?></td>
                            <td>
                                <?php if ($m['id_venta']): ?>
                                    <a href="<?= url('ventas/show?id=' . $m['id_venta']) ?>" class="badge bg-light text-primary border text-decoration-none">
                                        #<?= $m['id_venta'] ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= sanitize($m['usuario_nombre']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
