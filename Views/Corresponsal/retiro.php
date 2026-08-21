<?php
$pageTitle = 'Registrar Retiro - Corresponsal';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 650px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon danger fs-3"><i class="bi bi-box-arrow-up"></i></div>
            <div>
                <h5 class="fw-bold mb-0 text-danger">Retiro de Efectivo</h5>
                <p class="text-muted small mb-0">Entrega de dinero físico al cliente desde el fondo corresponsal.</p>
            </div>
        </div>
        <a href="<?= url('corresponsal') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <!-- Saldo disponible -->
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <i class="bi bi-wallet2 me-1"></i> Saldo disponible en caja corresponsal:
        </div>
        <strong class="fs-5 text-dark"><?= formatMoney($cuenta['saldo']) ?></strong>
    </div>

    <form action="<?= url('corresponsal/store') ?>" method="POST">
        <input type="hidden" name="tipo" value="RETIRO">

        <div class="mb-3">
            <label for="valor" class="form-label fw-semibold">Monto del Retiro ($) *</label>
            <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-danger" id="valor" name="valor" max="<?= $cuenta['saldo'] ?>" required placeholder="0.00" autofocus min="1000">
            <small class="text-muted">El monto no puede superar el saldo disponible en la cuenta corresponsal.</small>
        </div>

        <div class="mb-3">
            <label for="id_cliente" class="form-label fw-semibold">Cliente Titular / Beneficiario</label>
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
            <label for="referencia" class="form-label fw-semibold">Código de Aprobación / OTP / Token Bancario *</label>
            <input type="text" class="form-control" id="referencia" name="referencia" required placeholder="Ej: Token # 984512 / Ref # 885412">
        </div>

        <div class="mb-4">
            <label for="descripcion" class="form-label fw-semibold">Observaciones / Entidad</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Ej: Retiro Nequi / Daviplata / Banco de Bogotá">
        </div>

        <div class="text-end">
            <a href="<?= url('corresponsal') ?>" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-danger px-4 fw-bold">
                <i class="bi bi-dash-circle me-1"></i> Autorizar y Entregar Efectivo
            </button>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
