<?php
$pageTitle = 'Crear Categoría';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 600px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Nueva Categoría</h5>
            <p class="text-muted small mb-0">Defina una nueva categoría para organizar productos.</p>
        </div>
        <a href="<?= url('categorias') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('categorias/store') ?>" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label fw-semibold">Nombre de la Categoría *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Arte y Manualidades" required autofocus>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Detalle de productos agrupados en esta categoría..."></textarea>
        </div>

        <div class="mb-4">
            <label for="estado" class="form-label fw-semibold">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="ACTIVO" selected>ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>

        <div class="text-end">
            <a href="<?= url('categorias') ?>" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-save me-1"></i> Guardar Categoría
            </button>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
