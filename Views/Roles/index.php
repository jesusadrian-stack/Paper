<?php
$pageTitle = 'Roles del Sistema';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4">
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Roles y Permisos de Seguridad</h5>
        <p class="text-muted small mb-0">Estructura de perfiles de acceso que rigen los privilegios y limitaciones en la aplicación.</p>
    </div>

    <div class="row g-4">
        <?php foreach ($roles as $r): ?>
            <div class="col-12 col-md-6">
                <div class="card border h-100 p-4 rounded-4 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="stat-icon <?= $r['nombre'] === 'ADMINISTRADOR' ? 'primary' : 'warning' ?>">
                            <i class="bi <?= $r['nombre'] === 'ADMINISTRADOR' ? 'bi-shield-check' : 'bi-person-badge' ?>"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?= sanitize($r['nombre']) ?></h5>
                            <small class="text-muted">Nivel de Acceso <?= $r['nombre'] === 'ADMINISTRADOR' ? 'Total (Nivel 1)' : 'Operativo (Nivel 2)' ?></small>
                        </div>
                    </div>

                    <p class="text-secondary small mb-3"><?= sanitize($r['descripcion']) ?></p>

                    <h6 class="fw-bold small text-uppercase text-muted mb-2">Privilegios Concedidos:</h6>
                    <ul class="list-unstyled small mb-0">
                        <?php if ($r['nombre'] === 'ADMINISTRADOR'): ?>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Control total de usuarios, roles y contraseñas.</li>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Creación y ajuste de productos, precios y categorías.</li>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Modificación de stock y autorización de ajustes físicos.</li>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Gestión de cuentas (Papelería y Corresponsal) y transferencias.</li>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Generación de reportes globales y consultas de Inteligencia Artificial.</li>
                        <?php else: ?>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Consulta y búsqueda de productos y stock.</li>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Registro de ventas por mostrador y emisión de tickets.</li>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Creación y consulta de clientes.</li>
                            <li class="mb-1 text-success"><i class="bi bi-check-circle-fill me-2"></i> Registro de depósitos y retiros de corresponsal.</li>
                            <li class="mb-1 text-danger"><i class="bi bi-x-circle-fill me-2"></i> Sin acceso a configuración, finanzas críticas ni eliminación de datos.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
