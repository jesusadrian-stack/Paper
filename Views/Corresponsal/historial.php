<?php
$pageTitle = 'Historial de Operaciones de Corresponsal';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Historial y Conciliación de Corresponsal</h5>
            <p class="text-muted small mb-0">Consulte el registro de todas las transacciones bancarias ejecutadas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('corresponsal/deposito') ?>" class="btn btn-success btn-sm">+ Depósito</a>
            <a href="<?= url('corresponsal/retiro') ?>" class="btn btn-danger btn-sm">- Retiro</a>
        </div>
    </div>

    <!-- Filtros -->
    <form action="<?= url('corresponsal/historial') ?>" method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-3">
            <select class="form-select" name="tipo">
                <option value="">-- Todos los Tipos --</option>
                <option value="DEPOSITO" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'DEPOSITO') ? 'selected' : '' ?>>DEPÓSITOS</option>
                <option value="RETIRO" <?= (isset($_GET['tipo']) && $_GET['tipo'] === 'RETIRO') ? 'selected' : '' ?>>RETIROS</option>
            </select>
        </div>

        <div class="col-6 col-md-3">
            <input type="date" class="form-control" name="fecha_inicio" value="<?= sanitize($_GET['fecha_inicio'] ?? '') ?>" placeholder="Desde">
        </div>

        <div class="col-6 col-md-3">
            <input type="date" class="form-control" name="fecha_fin" value="<?= sanitize($_GET['fecha_fin'] ?? '') ?>" placeholder="Hasta">
        </div>

        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-filter me-1"></i> Filtrar</button>
            <a href="<?= url('corresponsal/historial') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha / Hora</th>
                    <th>Operación</th>
                    <th>Monto</th>
                    <th>Referencia</th>
                    <th>Cliente</th>
                    <th>Detalle / Banco</th>
                    <th>Operador</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($operaciones)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No se encontraron operaciones en el período seleccionado.</td></tr>
                <?php else: ?>
                    <?php foreach ($operaciones as $op): ?>
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
                            <td>
                                <?= sanitize($op['cliente_nombre'] ?? 'Cliente General') ?>
                                <?php if (!empty($op['numero_identificacion'])): ?>
                                    <small class="text-muted d-block"><?= sanitize($op['numero_identificacion']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= sanitize($op['descripcion'] ?? '-') ?></small></td>
                            <td><small><?= sanitize($op['usuario_nombre']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
