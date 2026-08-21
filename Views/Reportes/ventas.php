<?php
$pageTitle = 'Reporte Consolidado de Ventas';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Reporte de Ventas por Período</h5>
            <p class="text-muted small mb-0">Filtre por rango de fechas o cajero y analice el volumen total recaudado.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Imprimir</button>
            <a href="<?= url('reportes') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    <!-- Formulario de Filtros -->
    <form action="<?= url('reportes/ventas') ?>" method="GET" class="row g-2 mb-4">
        <div class="col-12 col-md-3">
            <label class="form-label small text-muted">Fecha Inicial</label>
            <input type="date" class="form-control form-control-sm" name="fecha_inicio" value="<?= sanitize($fechaInicio) ?>">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label small text-muted">Fecha Final</label>
            <input type="date" class="form-control form-control-sm" name="fecha_fin" value="<?= sanitize($fechaFin) ?>">
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted">Vendedor</label>
            <select class="form-select form-select-sm" name="usuario">
                <option value="">-- Todos los Vendedores --</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id_usuario'] ?>" <?= (isset($_GET['usuario']) && $_GET['usuario'] == $u['id_usuario']) ? 'selected' : '' ?>>
                        <?= sanitize($u['nombre'] . ' ' . $u['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter me-1"></i> Generar</button>
        </div>
    </form>

    <!-- Resumen de Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Total de Transacciones</small>
                <span class="fs-4 fw-bold text-dark"><?= $reporte['total_ventas'] ?></span>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="p-3 bg-light rounded-3 border text-center">
                <small class="text-muted text-uppercase fw-semibold d-block">Ingresos Totales Recaudados</small>
                <span class="fs-4 fw-bold text-success"><?= formatMoney($reporte['total_recaudado']) ?></span>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Artículos</th>
                    <th>Total</th>
                    <th>Vendedor</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reporte['ventas'])): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No se encontraron ventas para los criterios especificados.</td></tr>
                <?php else: ?>
                    <?php foreach ($reporte['ventas'] as $v): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border">#<?= $v['id_venta'] ?></span></td>
                            <td><small><?= date('d/m/Y H:i', strtotime($v['fecha_venta'])) ?></small></td>
                            <td><?= sanitize($v['cliente_nombre'] ?? 'Cliente Mostrador') ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $v['items_count'] ?></span></td>
                            <td class="fw-bold text-success"><?= formatMoney($v['total']) ?></td>
                            <td><small class="text-muted"><?= sanitize($v['usuario_nombre']) ?></small></td>
                            <td>
                                <a href="<?= url('ventas/show?id=' . $v['id_venta']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Ver Comprobante">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
