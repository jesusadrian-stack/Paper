<?php
$pageTitle = 'Reporte Financiero y Flujo de Fondos';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Reporte Financiero y Conciliación</h5>
            <p class="text-muted small mb-0">Balance consolidado de ingresos, egresos, depósitos y retiros.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Imprimir</button>
            <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    <!-- Filtros -->
    <form action="<?= url('reportes/finanzas') ?>" method="GET" class="row g-2 mb-4">
        <div class="col-12 col-md-5">
            <label class="form-label small text-muted">Fecha Inicial</label>
            <input type="date" class="form-control form-control-sm" name="fecha_inicio" value="<?= sanitize($fechaInicio) ?>">
        </div>
        <div class="col-12 col-md-5">
            <label class="form-label small text-muted">Fecha Final</label>
            <input type="date" class="form-control form-control-sm" name="fecha_fin" value="<?= sanitize($fechaFin) ?>">
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter me-1"></i> Filtrar</button>
        </div>
    </form>

    <!-- Resumen de Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Ingresos Papelería</small>
                <span class="fs-5 fw-bold text-success"><?= formatMoney($reporte['total_ingresos']) ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Egresos / Gastos</small>
                <span class="fs-5 fw-bold text-danger"><?= formatMoney($reporte['total_egresos']) ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Depósitos Corresponsal</small>
                <span class="fs-5 fw-bold text-primary"><?= formatMoney($reporte['total_depositos']) ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Retiros Corresponsal</small>
                <span class="fs-5 fw-bold text-warning text-dark"><?= formatMoney($reporte['total_retiros']) ?></span>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Cuenta</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Valor</th>
                    <th>Saldo Resultante</th>
                    <th>Operador</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reporte['movimientos'])): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No se registraron movimientos en este rango de fechas.</td></tr>
                <?php else: ?>
                    <?php foreach ($reporte['movimientos'] as $m): ?>
                        <tr>
                            <td><small><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])) ?></small></td>
                            <td><span class="badge bg-light text-dark border"><?= sanitize($m['cuenta_nombre']) ?></span></td>
                            <td>
                                <span class="badge bg-<?= in_array($m['tipo'], ['INGRESO', 'DEPOSITO']) ? 'success' : 'danger' ?>-subtle text-<?= in_array($m['tipo'], ['INGRESO', 'DEPOSITO']) ? 'success' : 'danger' ?>">
                                    <?= $m['tipo'] ?>
                                </span>
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
