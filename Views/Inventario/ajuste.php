<?php
$pageTitle = 'Ajuste Físico de Inventario';
require_once VIEWS_PATH . '/Layouts/header.php';
$selectedProdId = (int)($_GET['producto'] ?? 0);
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 650px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1 text-warning text-dark"><i class="bi bi-sliders me-2"></i>Ajuste Físico de Inventario</h5>
            <p class="text-muted small mb-0">Establezca el conteo físico real verificado en estanterías.</p>
        </div>
        <a href="<?= url('inventario') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('inventario/ajuste') ?>" method="POST">
        <div class="mb-3">
            <label for="id_producto" class="form-label fw-semibold">Seleccionar Producto *</label>
            <select class="form-select" id="id_producto" name="id_producto" required>
                <option value="">-- Seleccione un Producto --</option>
                <?php foreach ($productos as $p): ?>
                    <option value="<?= $p['id_producto'] ?>" <?= $p['id_producto'] === $selectedProdId ? 'selected' : '' ?>>
                        <?= sanitize($p['codigo']) ?> - <?= sanitize($p['nombre']) ?> (Stock actual en sistema: <?= $p['stock_actual'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="stock_fisico" class="form-label fw-semibold">Nuevo Stock Físico Real *</label>
            <input type="number" class="form-control" id="stock_fisico" name="stock_fisico" min="0" required placeholder="0">
            <small class="text-muted">El sistema calculará automáticamente la diferencia y registrará el movimiento.</small>
        </div>

        <div class="mb-4">
            <label for="motivo" class="form-label fw-semibold">Motivo del Ajuste *</label>
            <input type="text" class="form-control" id="motivo" name="motivo" required placeholder="Ej: Conteo de auditoría mensual de fin de mes">
        </div>

        <div class="text-end">
            <a href="<?= url('inventario') ?>" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-warning text-dark px-4 fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> Aplicar Ajuste
            </button>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
