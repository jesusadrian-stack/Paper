<?php
$pageTitle = 'Módulo Corresponsal Bancario';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<!-- Banner Ilustrativo de Corresponsal -->
<div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 100%); color: #ffffff;">
    <div class="row g-0 align-items-center">
        <div class="col-12 col-md-8 p-4 p-lg-5">
            <span class="badge bg-white text-primary mb-2 px-3 py-2 fw-semibold"><i class="bi bi-shield-check me-1"></i> Red de Corresponsalía Segura</span>
            <h3 class="fw-bold text-white mb-2">Operaciones Bancarias en Línea</h3>
            <p class="text-white-50 mb-0" style="max-width: 550px;">Realiza depósitos y retiros de efectivo con acreditación inmediata y conciliación de caja en tiempo real.</p>
        </div>
        <div class="col-12 col-md-4 d-none d-md-block text-center p-3">
            <img src="<?= url('images/corresponsal.jpg') ?>" alt="Corresponsal Bancario" class="img-fluid rounded-4 shadow" style="max-height: 160px; object-fit: cover; width: 90%;">
        </div>
    </div>
</div>

<!-- Tarjetas Principales del Corresponsal -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Fondo Disponible</span>
                <div class="stat-icon info"><i class="bi bi-bank"></i></div>
            </div>
            <h2 class="fw-bold text-primary mb-1"><?= formatMoney($cuenta['saldo']) ?></h2>
            <small class="text-muted">Reserva para pago de retiros en efectivo</small>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Depósitos Hoy</span>
                <div class="stat-icon success"><i class="bi bi-arrow-down-left"></i></div>
            </div>
            <h2 class="fw-bold text-success mb-1"><?= formatMoney($operacionesHoy['total_depositos']) ?></h2>
            <small class="text-muted">Efectivo recaudado por corresponsal hoy</small>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Retiros Hoy</span>
                <div class="stat-icon danger"><i class="bi bi-arrow-up-right"></i></div>
            </div>
            <h2 class="fw-bold text-danger mb-1"><?= formatMoney($operacionesHoy['total_retiros']) ?></h2>
            <small class="text-muted">Efectivo entregado a usuarios hoy</small>
        </div>
    </div>
</div>

<!-- Acciones de Operación Rápida -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 rounded-4 p-4 border-start border-success border-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="stat-icon success fs-3"><i class="bi bi-box-arrow-in-down"></i></div>
                <div>
                    <h5 class="fw-bold mb-0">Registrar Depósito / Consignación</h5>
                    <p class="text-muted small mb-0">El cliente entrega dinero en efectivo para su cuenta bancaria.</p>
                </div>
            </div>
            <a href="<?= url('corresponsal/deposito') ?>" class="btn btn-success fw-semibold w-100 py-2">
                <i class="bi bi-plus-circle me-1"></i> Abrir Operación de Depósito
            </a>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 rounded-4 p-4 border-start border-danger border-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="stat-icon danger fs-3"><i class="bi bi-box-arrow-up"></i></div>
                <div>
                    <h5 class="fw-bold mb-0">Registrar Retiro de Efectivo</h5>
                    <p class="text-muted small mb-0">Se entrega dinero en efectivo al cliente desde el fondo corresponsal.</p>
                </div>
            </div>
            <a href="<?= url('corresponsal/retiro') ?>" class="btn btn-danger fw-semibold w-100 py-2">
                <i class="bi bi-dash-circle me-1"></i> Abrir Operación de Retiro
            </a>
        </div>
    </div>
</div>

<!-- Últimas Operaciones -->
<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Operaciones Recientes de Corresponsal</h6>
        <a href="<?= url('corresponsal/historial') ?>" class="btn btn-sm btn-link text-decoration-none">Ver historial completo &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Referencia</th>
                    <th>Cliente</th>
                    <th>Operador</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($operacionesRecientes)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay operaciones de corresponsal registradas recientemente.</td></tr>
                <?php else: ?>
                    <?php foreach ($operacionesRecientes as $op): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border">#<?= $op['id_operacion'] ?></span></td>
                            <td><small><?= date('d/m/Y H:i', strtotime($op['fecha_operacion'])) ?></small></td>
                            <td>
                                <span class="badge bg-<?= $op['tipo'] === 'DEPOSITO' ? 'success' : 'danger' ?>-subtle text-<?= $op['tipo'] === 'DEPOSITO' ? 'success' : 'danger' ?>">
                                    <?= $op['tipo'] ?>
                                </span>
                            </td>
                            <td class="fw-bold <?= $op['tipo'] === 'DEPOSITO' ? 'text-success' : 'text-danger' ?>">
                                <?= formatMoney($op['valor']) ?>
                            </td>
                            <td><code><?= sanitize($op['referencia'] ?? '-') ?></code></td>
                            <td><?= sanitize($op['cliente_nombre'] ?? 'Cliente General') ?></td>
                            <td><small class="text-muted"><?= sanitize($op['usuario_nombre']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
