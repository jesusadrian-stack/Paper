<?php
$pageTitle = 'Auditoría Global de Historial de Precios';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Historial General de Cambios de Precio</h5>
            <p class="text-muted small mb-0">Trazabilidad y auditoría completa de los ajustes de tarifas realizados en el catálogo.</p>
        </div>
        <a href="<?= url('productos') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver a Productos
        </a>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control table-search-input" data-table="tabla-historial-precios" placeholder="Buscar por código, nombre de producto o usuario...">
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0" id="tabla-historial-precios">
            <thead>
                <tr>
                    <th>Fecha Cambio</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Precio Anterior</th>
                    <th>Precio Nuevo</th>
                    <th>Variación</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay modificaciones de precios registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($historial as $h): 
                        $diff = ($h['precio_anterior'] !== null) ? ($h['precio_nuevo'] - $h['precio_anterior']) : 0;
                    ?>
                        <tr>
                            <td><small><?= date('d/m/Y H:i', strtotime($h['fecha_cambio'])) ?></small></td>
                            <td><code><?= sanitize($h['producto_codigo']) ?></code></td>
                            <td class="fw-semibold"><?= sanitize($h['producto_nombre']) ?></td>
                            <td><?= $h['precio_anterior'] !== null ? formatMoney($h['precio_anterior']) : '<span class="text-muted">Inicial</span>' ?></td>
                            <td class="fw-bold text-primary"><?= formatMoney($h['precio_nuevo']) ?></td>
                            <td>
                                <?php if ($h['precio_anterior'] === null): ?>
                                    <span class="badge bg-light text-muted border">Nuevo</span>
                                <?php elseif ($diff > 0): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">+<?= formatMoney($diff) ?></span>
                                <?php elseif ($diff < 0): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><?= formatMoney($diff) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">Sin cambio</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= sanitize($h['usuario_nombre'] . ' ' . $h['usuario_apellido']) ?> (<?= sanitize($h['nombre_usuario']) ?>)</small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
