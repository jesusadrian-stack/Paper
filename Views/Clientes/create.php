<?php
$pageTitle = 'Registrar Nuevo Cliente';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 750px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Nuevo Cliente</h5>
            <p class="text-muted small mb-0">Registre un nuevo cliente para comprobantes de venta y corresponsal.</p>
        </div>
        <a href="<?= url('clientes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('clientes/store') ?>" method="POST">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="tipo_identificacion" class="form-label fw-semibold">Tipo Doc. *</label>
                <select class="form-select" id="tipo_identificacion" name="tipo_identificacion" required>
                    <option value="CC" selected>Cédula de Ciudadanía (CC)</option>
                    <option value="CE">Cédula de Extranjería (CE)</option>
                    <option value="NIT">NIT / Empresa</option>
                    <option value="TI">Tarjeta de Identidad (TI)</option>
                    <option value="PASAPORTE">Pasaporte</option>
                </select>
            </div>

            <div class="col-md-8">
                <label for="numero_identificacion" class="form-label fw-semibold">Número de Identificación *</label>
                <input type="text" class="form-control" id="numero_identificacion" name="numero_identificacion" required placeholder="Ej: 1015456789" autofocus>
            </div>

            <div class="col-md-6">
                <label for="nombre" class="form-label fw-semibold">Nombres / Razón Social *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Juan Pablo">
            </div>

            <div class="col-md-6">
                <label for="apellido" class="form-label fw-semibold">Apellidos</label>
                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ej: Gómez">
            </div>

            <div class="col-md-6">
                <label for="telefono" class="form-label fw-semibold">Teléfono / Celular</label>
                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej: 3151234567">
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="cliente@correo.com">
            </div>

            <div class="col-12">
                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Calle 45 # 12-34">
            </div>

            <div class="col-md-6">
                <label for="estado" class="form-label fw-semibold">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="ACTIVO" selected>ACTIVO</option>
                    <option value="INACTIVO">INACTIVO</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="<?= url('clientes') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-save me-1"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
