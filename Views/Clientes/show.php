<?php
$pageTitle = 'Detalle de Cliente: ' . sanitize($cliente['nombre']);
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon primary fs-3">
                <i class="bi bi-person"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0"><?= sanitize($cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '')) ?></h5>
                <small class="text-muted"><?= $cliente['tipo_identificacion'] ?>: <?= sanitize($cliente['numero_identificacion']) ?></small>
            </div>
        </div>
        <div>
            <span class="badge-custom <?= $cliente['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                <?= $cliente['estado'] ?>
            </span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="text-muted small fw-semibold">Teléfono / Celular</label>
            <div class="fs-6 fw-bold"><?= sanitize($cliente['telefono'] ?? 'No registrado') ?></div>
        </div>

        <div class="col-md-6">
            <label class="text-muted small fw-semibold">Correo Electrónico</label>
            <div class="fs-6 fw-bold"><?= sanitize($cliente['correo'] ?? 'No registrado') ?></div>
        </div>

        <div class="col-12">
            <label class="text-muted small fw-semibold">Dirección</label>
            <div class="fs-6 fw-bold"><?= sanitize($cliente['direccion'] ?? 'No registrada') ?></div>
        </div>

        <div class="col-md-6">
            <label class="text-muted small fw-semibold">Fecha de Registro</label>
            <div><?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?></div>
        </div>
    </div>

    <div class="d-flex justify-content-between pt-3 border-top">
        <a href="<?= url('clientes') ?>" class="btn btn-outline-secondary btn-sm">Volver al Directorio</a>
        <div class="d-flex gap-2">
            <a href="<?= url('ventas/create') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-cart-plus me-1"></i> Realizar Venta
            </a>
            <a href="<?= url('clientes/edit?id=' . $cliente['id_cliente']) ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Editar Cliente
            </a>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
