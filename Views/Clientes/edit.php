<?php
$pageTitle = 'Editar Cliente';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 750px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Editar Cliente #<?= $cliente['id_cliente'] ?></h5>
            <p class="text-muted small mb-0">Modifique los datos de identificación o contacto del cliente.</p>
        </div>
        <a href="<?= url('clientes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('clientes/update?id=' . $cliente['id_cliente']) ?>" method="POST">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="tipo_identificacion" class="form-label fw-semibold">Tipo Doc. *</label>
                <select class="form-select" id="tipo_identificacion" name="tipo_identificacion" required>
                    <?php foreach (['CC', 'CE', 'NIT', 'TI', 'PASAPORTE'] as $td): ?>
                        <option value="<?= $td ?>" <?= $cliente['tipo_identificacion'] === $td ? 'selected' : '' ?>><?= $td ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-8">
                <label for="numero_identificacion" class="form-label fw-semibold">Número de Identificación *</label>
                <input type="text" class="form-control" id="numero_identificacion" name="numero_identificacion" value="<?= sanitize($cliente['numero_identificacion']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="nombre" class="form-label fw-semibold">Nombres / Razón Social *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= sanitize($cliente['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="apellido" class="form-label fw-semibold">Apellidos</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?= sanitize($cliente['apellido'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="telefono" class="form-label fw-semibold">Teléfono / Celular</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= sanitize($cliente['telefono'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?= sanitize($cliente['correo'] ?? '') ?>">
            </div>

            <div class="col-12">
                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?= sanitize($cliente['direccion'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="estado" class="form-label fw-semibold">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="ACTIVO" <?= $cliente['estado'] === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                    <option value="INACTIVO" <?= $cliente['estado'] === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="<?= url('clientes') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-check2 me-1"></i> Actualizar Cliente
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
