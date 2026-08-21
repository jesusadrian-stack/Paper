<?php
$pageTitle = 'Editar Producto';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Editar Producto: <?= sanitize($producto['nombre']) ?></h5>
            <p class="text-muted small mb-0">Modifique los atributos comerciales. Si cambia el precio, quedará registrado automáticamente en el historial de precios.</p>
        </div>
        <a href="<?= url('productos') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('productos/update?id=' . $producto['id_producto']) ?>" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="codigo" class="form-label fw-semibold">Código / Código de Barras *</label>
                <input type="text" class="form-control" id="codigo" name="codigo" value="<?= sanitize($producto['codigo']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="id_categoria" class="form-label fw-semibold">Categoría *</label>
                <select class="form-select" id="id_categoria" name="id_categoria" required>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>" <?= $c['id_categoria'] == $producto['id_categoria'] ? 'selected' : '' ?>>
                            <?= sanitize($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label for="nombre" class="form-label fw-semibold">Nombre del Producto *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= sanitize($producto['nombre']) ?>" required>
            </div>

            <div class="col-12">
                <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?= sanitize($producto['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="col-md-4">
                <label for="precio" class="form-label fw-semibold">Precio de Venta ($) *</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?= $producto['precio'] ?>" required>
                <small class="text-muted">Precio actual: <?= formatMoney($producto['precio']) ?></small>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Stock Actual (Lectura)</label>
                <input type="text" class="form-control bg-light" value="<?= $producto['stock_actual'] ?> uds" readonly>
                <small class="text-muted"><a href="<?= url('inventario') ?>">Ajustar en módulo de Inventario</a></small>
            </div>

            <div class="col-md-4">
                <label for="stock_minimo" class="form-label fw-semibold">Stock Mínimo (Alerta)</label>
                <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="<?= $producto['stock_minimo'] ?>" min="0">
            </div>

            <div class="col-md-6">
                <label for="estado" class="form-label fw-semibold">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="ACTIVO" <?= $producto['estado'] === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                    <option value="INACTIVO" <?= $producto['estado'] === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="<?= url('productos') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-check2 me-1"></i> Actualizar Producto
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
