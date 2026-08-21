<?php
$pageTitle = 'Registrar Depósito - Corresponsal';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 650px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon success fs-3"><i class="bi bi-box-arrow-in-down"></i></div>
            <div>
                <h5 class="fw-bold mb-0 text-success">Depósito / Recaudo Bancario</h5>
                <p class="text-muted small mb-0">El dinero recibido se sumará al fondo de corresponsal.</p>
            </div>
        </div>
        <a href="<?= url('corresponsal') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('corresponsal/store') ?>" method="POST">
        <input type="hidden" name="tipo" value="DEPOSITO">

        <div class="mb-3">
            <label for="valor" class="form-label fw-semibold">Monto del Depósito ($) *</label>
            <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-success" id="valor" name="valor" required placeholder="0.00" autofocus min="1000">
        </div>

        <div class="mb-3">
            <label for="id_cliente" class="form-label fw-semibold">Cliente Titular / Depositante</label>
            <select class="form-select" id="id_cliente" name="id_cliente">
                <option value="">-- Cliente Mostrador / Sin Registro --</option>
                <?php foreach ($clientes as $cl): ?>
                    <option value="<?= $cl['id_cliente'] ?>">
                        <?= sanitize($cl['nombre'] . ' ' . ($cl['apellido'] ?? '')) ?> (<?= sanitize($cl['numero_identificacion']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="referencia" class="form-label fw-semibold">Número de Cuenta / Referencia Bancaria *</label>
            <input type="text" class="form-control" id="referencia" name="referencia" required placeholder="Ej: Cuenta de Ahorros # 123-456789-00">
        </div>

        <div class="mb-4">
            <label for="descripcion" class="form-label fw-semibold">Observaciones / Entidad Financiera</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Ej: Bancolombia / Davivienda / Nequi">
        </div>

        <div class="p-3 bg-light rounded-3 mb-4 small text-muted">
            <i class="bi bi-info-circle text-primary me-1"></i> Al confirmar, se generará el movimiento correspondiente y se actualizará el balance en tiempo real.
        </div>

        <div class="text-end">
            <a href="<?= url('corresponsal') ?>" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-bold">
                <i class="bi bi-check2-circle me-1"></i> Confirmar Depósito
            </button>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
