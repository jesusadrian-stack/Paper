<?php
$pageTitle = 'Crear Nuevo Producto';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Registrar Producto</h5>
            <p class="text-muted small mb-0">Ingrese los datos comerciales y de inventario del producto.</p>
        </div>
        <a href="<?= url('productos') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('productos/store') ?>" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="codigo" class="form-label fw-semibold">Código / Código de Barras *</label>
                <input type="text" class="form-control" id="codigo" name="codigo" required placeholder="Ej: PAP001" autofocus>
            </div>

            <div class="col-md-6">
                <label for="id_categoria" class="form-label fw-semibold">Categoría *</label>
                <select class="form-select" id="id_categoria" name="id_categoria" required>
                    <option value="">-- Seleccionar Categoría --</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>"><?= sanitize($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label for="nombre" class="form-label fw-semibold">Nombre del Producto *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Resma Papel Carta 75g">
            </div>

            <div class="col-12">
                <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Detalles de marca, color, presentación..."></textarea>
            </div>

            <div class="col-md-4">
                <label for="precio" class="form-label fw-semibold">Precio de Venta ($) *</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required placeholder="0.00">
            </div>

            <div class="col-md-4">
                <label for="stock_actual" class="form-label fw-semibold">Stock Inicial</label>
                <input type="number" class="form-control" id="stock_actual" name="stock_actual" value="0" min="0">
            </div>

            <div class="col-md-4">
                <label for="stock_minimo" class="form-label fw-semibold">Stock Mínimo (Alerta)</label>
                <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="5" min="0">
            </div>

            <div class="col-md-6">
                <label for="estado" class="form-label fw-semibold">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="ACTIVO" selected>ACTIVO</option>
                    <option value="INACTIVO">INACTIVO</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="<?= url('productos') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-save me-1"></i> Guardar Producto
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
