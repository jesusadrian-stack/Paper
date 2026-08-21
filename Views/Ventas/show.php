<?php
$pageTitle = 'Comprobante de Venta #' . $venta['id_venta'];
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 850px;">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-3 mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1">Comprobante de Venta #<?= $venta['id_venta'] ?></h4>
            <p class="text-muted small mb-0">Fecha y Hora: <strong><?= date('d/m/Y H:i:s', strtotime($venta['fecha_venta'])) ?></strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('ventas/ticket?id=' . $venta['id_venta']) ?>" target="_blank" class="btn btn-primary btn-sm">
                <i class="bi bi-printer me-1"></i> Imprimir Ticket
            </a>
            <a href="<?= url('ventas') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a Ventas
            </a>
        </div>
    </div>

    <!-- Información de Cabecera -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
                <h6 class="fw-bold small text-uppercase text-muted mb-2">Datos del Cliente</h6>
                <div class="fw-bold text-dark"><?= sanitize($venta['cliente_nombre'] ?? 'Cliente Mostrador') ?> <?= sanitize($venta['cliente_apellido'] ?? '') ?></div>
                <?php if (!empty($venta['numero_identificacion'])): ?>
                    <small class="text-muted d-block">Documento: <?= sanitize($venta['numero_identificacion']) ?></small>
                <?php endif; ?>
                <?php if (!empty($venta['telefono'])): ?>
                    <small class="text-muted d-block">Teléfono: <?= sanitize($venta['telefono']) ?></small>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
                <h6 class="fw-bold small text-uppercase text-muted mb-2">Datos de Operación</h6>
                <div>Atendido por: <strong><?= sanitize($venta['usuario_nombre'] . ' ' . $venta['usuario_apellido']) ?></strong></div>
                <div>Estado de Venta: <span class="badge bg-success"><?= $venta['estado'] ?></span></div>
                <small class="text-muted">Impacto contable: Ingreso registrado en Caja Papelería.</small>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos Vendidos -->
    <h6 class="fw-bold mb-3">Artículos y Detalle</h6>
    <div class="table-responsive mb-4">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-end">Precio Unit.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $d): ?>
                    <tr>
                        <td><code><?= sanitize($d['producto_codigo']) ?></code></td>
                        <td class="fw-semibold"><?= sanitize($d['producto_nombre']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= sanitize($d['categoria_nombre']) ?></span></td>
                        <td class="text-center fw-bold"><?= $d['cantidad'] ?></td>
                        <td class="text-end"><?= formatMoney($d['precio_unitario']) ?></td>
                        <td class="text-end fw-bold"><?= formatMoney($d['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div class="row justify-content-end">
        <div class="col-md-5">
            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-bold"><?= formatMoney($venta['subtotal']) ?></span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top">
                    <span class="fs-5 fw-bold text-dark">TOTAL:</span>
                    <span class="fs-5 fw-bold text-primary"><?= formatMoney($venta['total']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
