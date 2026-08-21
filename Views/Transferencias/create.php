<?php
$pageTitle = 'Realizar Transferencia entre Cuentas';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 650px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon primary fs-3"><i class="bi bi-arrow-left-right"></i></div>
            <div>
                <h5 class="fw-bold mb-0">Nueva Transferencia</h5>
                <p class="text-muted small mb-0">Mueva fondos de forma segura entre cuentas con doble asiento contable.</p>
            </div>
        </div>
        <a href="<?= url('transferencias') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('transferencias/store') ?>" method="POST">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="id_cuenta_origen" class="form-label fw-semibold">Cuenta Origen (Egreso) *</label>
                <select class="form-select" id="id_cuenta_origen" name="id_cuenta_origen" required>
                    <option value="">-- Seleccione Origen --</option>
                    <?php foreach ($cuentas as $cta): ?>
                        <option value="<?= $cta['id_cuenta'] ?>">
                            <?= sanitize($cta['nombre']) ?> (Disponible: <?= formatMoney($cta['saldo']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label for="id_cuenta_destino" class="form-label fw-semibold">Cuenta Destino (Ingreso) *</label>
                <select class="form-select" id="id_cuenta_destino" name="id_cuenta_destino" required>
                    <option value="">-- Seleccione Destino --</option>
                    <?php foreach ($cuentas as $cta): ?>
                        <option value="<?= $cta['id_cuenta'] ?>">
                            <?= sanitize($cta['nombre']) ?> (Saldo: <?= formatMoney($cta['saldo']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label for="valor" class="form-label fw-semibold">Monto a Transferir ($) *</label>
                <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-primary" id="valor" name="valor" required placeholder="0.00" min="1000">
            </div>

            <div class="col-12">
                <label for="concepto" class="form-label fw-semibold">Concepto / Motivo de la Transferencia</label>
                <input type="text" class="form-control" id="concepto" name="concepto" placeholder="Ej: Fondeo de caja corresponsal para inicio de jornada">
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="<?= url('transferencias') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-send-check me-1"></i> Ejecutar Transferencia
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
