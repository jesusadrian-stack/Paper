<?php
$pageTitle = 'Movimientos Financieros de Caja';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Movimientos Financieros Globales</h5>
            <p class="text-muted small mb-0">Consulte y registre entradas o salidas de dinero extraordinarias.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalNuevoMovimiento">
            <i class="bi bi-plus-circle-fill me-1"></i> Registrar Ingreso/Egreso
        </button>
    </div>

    <!-- Filtros -->
    <form action="<?= url('cuentas/movimientos') ?>" method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-3">
            <select class="form-select" name="cuenta">
                <option value="">-- Todas las Cuentas --</option>
                <?php foreach ($cuentas as $cta): ?>
                    <option value="<?= $cta['id_cuenta'] ?>" <?= (isset($_GET['cuenta']) && $_GET['cuenta'] == $cta['id_cuenta']) ? 'selected' : '' ?>>
                        <?= sanitize($cta['nombre']) ?> (Saldo: <?= formatMoney($cta['saldo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <select class="form-select" name="tipo">
                <option value="">-- Tipo --</option>
                <option value="INGRESO" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'INGRESO') ? 'selected' : '' ?>>INGRESO</option>
                <option value="EGRESO" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'EGRESO') ? 'selected' : '' ?>>EGRESO</option>
                <option value="DEPOSITO" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'DEPOSITO') ? 'selected' : '' ?>>DEPOSITO</option>
                <option value="RETIRO" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'RETIRO') ? 'selected' : '' ?>>RETIRO</option>
            </select>
        </div>

        <div class="col-6 col-md-2">
            <input type="date" class="form-control" name="fecha_inicio" value="<?= sanitize($_GET['fecha_inicio'] ?? '') ?>" placeholder="Desde">
        </div>

        <div class="col-6 col-md-2">
            <input type="date" class="form-control" name="fecha_fin" value="<?= sanitize($_GET['fecha_fin'] ?? '') ?>" placeholder="Hasta">
        </div>

        <div class="col-6 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-filter me-1"></i> Filtrar</button>
            <a href="<?= url('cuentas/movimientos') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Cuenta</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Valor</th>
                    <th>Saldo Anterior</th>
                    <th>Saldo Resultante</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimientos)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No se encontraron movimientos financieros.</td></tr>
                <?php else: ?>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><small><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])) ?></small></td>
                            <td><span class="badge bg-light text-dark border"><?= sanitize($m['cuenta_nombre']) ?></span></td>
                            <td>
                                <span class="badge bg-<?= in_array($m['tipo'], ['INGRESO', 'DEPOSITO']) ? 'success' : 'danger' ?>-subtle text-<?= in_array($m['tipo'], ['INGRESO', 'DEPOSITO']) ? 'success' : 'danger' ?>">
                                    <?= $m['tipo'] ?>
                                </span>
                            </td>
                            <td><small class="fw-semibold text-dark"><?= sanitize($m['concepto']) ?></small></td>
                            <td class="fw-bold <?= in_array($m['tipo'], ['INGRESO', 'DEPOSITO']) ? 'text-success' : 'text-danger' ?>">
                                <?= formatMoney($m['valor']) ?>
                            </td>
                            <td><?= formatMoney($m['saldo_anterior']) ?></td>
                            <td class="fw-bold text-dark"><?= formatMoney($m['saldo_nuevo']) ?></td>
                            <td><small class="text-muted"><?= sanitize($m['usuario_nombre']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para registrar Ingreso o Egreso Extraordinario -->
<div class="modal fade" id="modalNuevoMovimiento" tabindex="-1" aria-labelledby="modalNuevoMovimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= url('cuentas/movimientos/store') ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalNuevoMovimientoLabel">Registrar Movimiento de Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_cuenta" class="form-label fw-semibold">Cuenta Afectada *</label>
                        <select class="form-select" id="id_cuenta" name="id_cuenta" required>
                            <?php foreach ($cuentas as $cta): ?>
                                <option value="<?= $cta['id_cuenta'] ?>"><?= sanitize($cta['nombre']) ?> (Disponible: <?= formatMoney($cta['saldo']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label fw-semibold">Tipo de Movimiento *</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="INGRESO">INGRESO (Aumenta Saldo)</option>
                            <option value="EGRESO">EGRESO (Disminuye Saldo)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="valor" class="form-label fw-semibold">Valor / Monto ($) *</label>
                        <input type="number" step="0.01" class="form-control" id="valor" name="valor" min="1" required placeholder="0.00">
                    </div>

                    <div class="mb-3">
                        <label for="concepto" class="form-label fw-semibold">Concepto / Motivo *</label>
                        <input type="text" class="form-control" id="concepto" name="concepto" required placeholder="Ej: Pago de servicios públicos / Aporte extraordinario">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Guardar Movimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
