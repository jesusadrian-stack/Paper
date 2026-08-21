<?php
$pageTitle = 'Detalle de Producto: ' . sanitize($producto['nombre']);
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="row g-4">
    <!-- Información Principal del Producto -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-light text-dark border">Código: <?= sanitize($producto['codigo']) ?></span>
                <span class="badge-custom <?= $producto['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                    <?= $producto['estado'] ?>
                </span>
            </div>

            <div class="text-center bg-light rounded-4 p-3 mb-3">
                <img src="<?= getProductImage($producto) ?>" alt="<?= sanitize($producto['nombre']) ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 180px; object-fit: contain;">
            </div>

            <h4 class="fw-bold text-dark mb-1"><?= sanitize($producto['nombre']) ?></h4>
            <p class="text-muted small mb-3"><i class="bi bi-tag me-1"></i> Categoría: <strong><?= sanitize($producto['categoria_nombre']) ?></strong></p>

            <?php if (!empty($producto['descripcion'])): ?>
                <div class="p-3 bg-light rounded-3 mb-4 small text-secondary">
                    <?= nl2br(sanitize($producto['descripcion'])) ?>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="border rounded-3 p-3 text-center bg-white">
                        <small class="text-muted text-uppercase fw-semibold d-block">Precio de Venta</small>
                        <span class="fs-4 fw-bold text-primary"><?= formatMoney($producto['precio']) ?></span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded-3 p-3 text-center bg-white">
                        <small class="text-muted text-uppercase fw-semibold d-block">Stock Actual</small>
                        <span class="fs-4 fw-bold <?= $producto['stock_actual'] <= $producto['stock_minimo'] ? 'text-danger' : 'text-success' ?>">
                            <?= $producto['stock_actual'] ?> uds
                        </span>
                    </div>
                </div>
            </div>

            <div class="small text-muted mb-4">
                <div>&bull; Stock Mínimo para Alerta: <strong><?= $producto['stock_minimo'] ?> uds</strong></div>
                <div>&bull; Fecha de Registro: <?= date('d/m/Y H:i', strtotime($producto['fecha_registro'])) ?></div>
                <?php if ($producto['fecha_actualizacion']): ?>
                    <div>&bull; Última Actualización: <?= date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])) ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-auto">
                <a href="<?= url('productos') ?>" class="btn btn-outline-secondary btn-sm flex-grow-1">Volver al Catálogo</a>
                <?php if ($isAdmin): ?>
                    <a href="<?= url('productos/edit?id=' . $producto['id_producto']) ?>" class="btn btn-primary btn-sm flex-grow-1">Editar Producto</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Historial de Precios -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Historial de Modificaciones de Precio</h6>
            
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Precio Anterior</th>
                            <th>Precio Nuevo</th>
                            <th>Modificado Por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historialPrecios)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No hay cambios de precio registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($historialPrecios as $hp): ?>
                                <tr>
                                    <td><small><?= date('d/m/Y H:i', strtotime($hp['fecha_cambio'])) ?></small></td>
                                    <td><?= $hp['precio_anterior'] !== null ? formatMoney($hp['precio_anterior']) : '<span class="text-muted">Precio inicial</span>' ?></td>
                                    <td class="fw-bold text-primary"><?= formatMoney($hp['precio_nuevo']) ?></td>
                                    <td><small class="text-muted"><?= sanitize($hp['usuario_nombre'] . ' ' . $hp['usuario_apellido']) ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
