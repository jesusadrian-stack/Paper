<?php
$pageTitle = 'Registrar Entrada de Mercancía';
require_once VIEWS_PATH . '/Layouts/header.php';
$selectedProdId = (int)($_GET['producto'] ?? 0);
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 650px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1 text-success"><i class="bi bi-box-arrow-in-down me-2"></i>Entrada de Inventario</h5>
            <p class="text-muted small mb-0">Incremente el stock por compras a distribuidores o devoluciones de clientes.</p>
        </div>
        <a href="<?= url('inventario') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('inventario/entrada') ?>" method="POST">
        <div class="mb-3">
            <label for="id_producto" class="form-label fw-semibold">Seleccionar Producto *</label>
            <select class="form-select" id="id_producto" name="id_producto" required>
                <option value="">-- Seleccione un Producto --</option>
                <?php foreach ($productos as $p): ?>
                    <option value="<?= $p['id_producto'] ?>" <?= $p['id_producto'] === $selectedProdId ? 'selected' : '' ?>>
                        <?= sanitize($p['codigo']) ?> - <?= sanitize($p['nombre']) ?> (Stock actual: <?= $p['stock_actual'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label fw-semibold">Cantidad a Ingresar *</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required placeholder="Ej: 50">
        </div>

        <div class="mb-4">
            <label for="motivo" class="form-label fw-semibold">Motivo / Factura de Proveedor</label>
            <input type="text" class="form-control" id="motivo" name="motivo" placeholder="Ej: Compra Factura #1234 - Distribuidora Escolar">
        </div>

        <div class="text-end">
            <a href="<?= url('inventario') ?>" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> Registrar Entrada
            </button>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
