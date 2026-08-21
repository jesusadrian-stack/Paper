<?php
$pageTitle = 'Crear Nuevo Usuario';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Nuevo Usuario</h5>
            <p class="text-muted small mb-0">Ingrese la información personal y credenciales de acceso del colaborador.</p>
        </div>
        <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('usuarios/store') ?>" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label fw-semibold">Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Carlos">
            </div>

            <div class="col-md-6">
                <label for="apellido" class="form-label fw-semibold">Apellido *</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required placeholder="Ej: Gomez">
            </div>

            <div class="col-md-6">
                <label for="documento" class="form-label fw-semibold">Documento de Identidad *</label>
                <input type="text" class="form-control" id="documento" name="documento" required placeholder="Cédula / Pasaporte">
            </div>

            <div class="col-md-6">
                <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej: 3001234567">
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="correo@ejemplo.com">
            </div>

            <div class="col-md-6">
                <label for="id_rol" class="form-label fw-semibold">Rol de Acceso *</label>
                <select class="form-select" id="id_rol" name="id_rol" required>
                    <option value="">-- Seleccionar Rol --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id_rol'] ?>"><?= sanitize($r['nombre']) ?> - <?= sanitize($r['descripcion']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="nombre_usuario" class="form-label fw-semibold">Nombre de Usuario (Login) *</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required placeholder="Ej: cgomez">
            </div>

            <div class="col-md-6">
                <label for="contrasena" class="form-label fw-semibold">Contraseña *</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required placeholder="Mínimo 6 caracteres">
            </div>

            <div class="col-md-6">
                <label for="estado" class="form-label fw-semibold">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="ACTIVO" selected>ACTIVO</option>
                    <option value="INACTIVO">INACTIVO</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="<?= url('usuarios') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-save me-1"></i> Guardar Usuario
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
