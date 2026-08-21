<?php
$pageTitle = 'Editar Usuario';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Editar Usuario #<?= $usuario['id_usuario'] ?></h5>
            <p class="text-muted small mb-0">Modifique los datos y roles del usuario.</p>
        </div>
        <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="<?= url('usuarios/update?id=' . $usuario['id_usuario']) ?>" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label fw-semibold">Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= sanitize($usuario['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="apellido" class="form-label fw-semibold">Apellido *</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?= sanitize($usuario['apellido']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="documento" class="form-label fw-semibold">Documento de Identidad *</label>
                <input type="text" class="form-control" id="documento" name="documento" value="<?= sanitize($usuario['documento']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= sanitize($usuario['telefono'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?= sanitize($usuario['correo'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="id_rol" class="form-label fw-semibold">Rol de Acceso *</label>
                <select class="form-select" id="id_rol" name="id_rol" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id_rol'] ?>" <?= $r['id_rol'] == $usuario['id_rol'] ? 'selected' : '' ?>>
                            <?= sanitize($r['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="nombre_usuario" class="form-label fw-semibold">Nombre de Usuario *</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?= sanitize($usuario['nombre_usuario']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="contrasena" class="form-label fw-semibold">Nueva Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Dejar en blanco para no cambiar">
            </div>

            <div class="col-md-6">
                <label for="estado" class="form-label fw-semibold">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="ACTIVO" <?= $usuario['estado'] === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                    <option value="INACTIVO" <?= $usuario['estado'] === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="<?= url('usuarios') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-check2 me-1"></i> Actualizar Usuario
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
