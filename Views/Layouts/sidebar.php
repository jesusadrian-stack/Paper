<?php
$currentUri = $_GET['r'] ?? '';
if (empty($currentUri)) {
    $req = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $script = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $script = str_replace('\\', '/', $script);
    $currentUri = trim(str_replace($script, '', $req), '/');
}
$isAdmin = ($_SESSION['user_role'] ?? '') === 'ADMINISTRADOR';

function isActiveRoute(string $prefix, string $currentUri): string {
    if ($prefix === 'dashboard' && ($currentUri === '' || $currentUri === 'dashboard')) return 'active';
    return (strpos($currentUri, $prefix) === 0) ? 'active' : '';
}

// Determinar si alguna ruta interna de "Ver más" está activa para mantenerlo abierto
$isMoreActive = (
    strpos($currentUri, 'corresponsal') === 0 ||
    strpos($currentUri, 'cuentas') === 0 ||
    strpos($currentUri, 'transferencias') === 0 ||
    strpos($currentUri, 'reportes') === 0 ||
    strpos($currentUri, 'ia') === 0
);
?>
<aside id="sidebar">
    <a href="<?= url('dashboard') ?>" class="sidebar-brand">
        <img src="<?= url('images/brand_logo.jpg') ?>" alt="Logo" class="rounded-circle shadow-sm" style="width: 38px; height: 38px; object-fit: cover;">
        <span><?= APP_NAME ?></span>
    </a>

    <div class="py-2">
        <a href="<?= url('dashboard') ?>" class="nav-link-custom <?= isActiveRoute('dashboard', $currentUri) ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <?php if ($isAdmin): ?>
            <!-- Administración -->
            <div class="sidebar-heading">Administración</div>
            <a href="<?= url('usuarios') ?>" class="nav-link-custom <?= isActiveRoute('usuarios', $currentUri) ?>">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
            <a href="<?= url('roles') ?>" class="nav-link-custom <?= isActiveRoute('roles', $currentUri) ?>">
                <i class="bi bi-shield-lock"></i>
                <span>Roles</span>
            </a>
        <?php endif; ?>

        <!-- Ventas y POS -->
        <div class="sidebar-heading">Ventas y Clientes</div>
        <a href="<?= url('ventas/create') ?>" class="nav-link-custom <?= isActiveRoute('ventas/create', $currentUri) ?>">
            <i class="bi bi-cart-plus text-success"></i>
            <span class="fw-bold text-white">Nueva Venta (POS)</span>
        </a>
        <a href="<?= url('ventas') ?>" class="nav-link-custom <?= ($currentUri === 'ventas' || strpos($currentUri, 'ventas/show') === 0) ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i>
            <span>Historial Ventas</span>
        </a>
        <a href="<?= url('clientes') ?>" class="nav-link-custom <?= isActiveRoute('clientes', $currentUri) ?>">
            <i class="bi bi-person-lines-fill"></i>
            <span>Clientes</span>
        </a>

        <!-- Inventario -->
        <div class="sidebar-heading">Inventario y Stock</div>
        <a href="<?= url('productos') ?>" class="nav-link-custom <?= isActiveRoute('productos', $currentUri) ?>">
            <i class="bi bi-box-seam"></i>
            <span>Productos</span>
        </a>
        <?php if ($isAdmin): ?>
            <a href="<?= url('categorias') ?>" class="nav-link-custom <?= isActiveRoute('categorias', $currentUri) ?>">
                <i class="bi bi-tags"></i>
                <span>Categorías</span>
            </a>
            <a href="<?= url('inventario') ?>" class="nav-link-custom <?= ($currentUri === 'inventario' || strpos($currentUri, 'inventario/entrada') === 0 || strpos($currentUri, 'inventario/salida') === 0 || strpos($currentUri, 'inventario/ajuste') === 0) ? 'active' : '' ?>">
                <i class="bi bi-sliders"></i>
                <span>Gestión Inventario</span>
            </a>
        <?php else: ?>
            <a href="<?= url('inventario') ?>" class="nav-link-custom <?= isActiveRoute('inventario', $currentUri) ?>">
                <i class="bi bi-eye"></i>
                <span>Consultar Stock</span>
            </a>
        <?php endif; ?>
        <a href="<?= url('inventario/historial') ?>" class="nav-link-custom <?= isActiveRoute('inventario/historial', $currentUri) ?>">
            <i class="bi bi-clock-history"></i>
            <span>Movimientos</span>
        </a>
        <a href="<?= url('alertas') ?>" class="nav-link-custom <?= isActiveRoute('alertas', $currentUri) ?>">
            <i class="bi bi-exclamation-triangle text-warning"></i>
            <span>Alertas de Stock</span>
        </a>

        <!-- Botón Ver Más Opciones -->
        <div class="sidebar-heading mt-2">Opciones Avanzadas</div>
        <a class="nav-link-custom sidebar-toggle-more <?= $isMoreActive ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#menuVerMasOpciones" role="button" aria-expanded="<?= $isMoreActive ? 'true' : 'false' ?>" aria-controls="menuVerMasOpciones">
            <i class="bi bi-grid-3x3-gap text-primary"></i>
            <span class="flex-grow-1">Ver más opciones</span>
            <i class="bi bi-chevron-down transition-icon ms-auto"></i>
        </a>

        <!-- Contenido colapsable: todo a partir de Corresponsal hacia abajo -->
        <div class="collapse <?= $isMoreActive ? 'show' : '' ?> sidebar-collapse-section" id="menuVerMasOpciones">
            <!-- Corresponsal Bancario -->
            <div class="sidebar-heading">Corresponsal</div>
            <a href="<?= url('corresponsal') ?>" class="nav-link-custom <?= ($currentUri === 'corresponsal' || strpos($currentUri, 'corresponsal/deposito') === 0 || strpos($currentUri, 'corresponsal/retiro') === 0) ? 'active' : '' ?>">
                <i class="bi bi-bank2"></i>
                <span>Operaciones</span>
            </a>
            <a href="<?= url('corresponsal/historial') ?>" class="nav-link-custom <?= isActiveRoute('corresponsal/historial', $currentUri) ?>">
                <i class="bi bi-journal-text"></i>
                <span>Historial Bancario</span>
            </a>

            <?php if ($isAdmin): ?>
                <!-- Finanzas & Tesorería -->
                <div class="sidebar-heading">Finanzas y Cuentas</div>
                <a href="<?= url('cuentas/papeleria') ?>" class="nav-link-custom <?= isActiveRoute('cuentas/papeleria', $currentUri) ?>">
                    <i class="bi bi-cash-stack"></i>
                    <span>Caja Papelería</span>
                </a>
                <a href="<?= url('cuentas/corresponsal') ?>" class="nav-link-custom <?= isActiveRoute('cuentas/corresponsal', $currentUri) ?>">
                    <i class="bi bi-wallet2"></i>
                    <span>Fondo Corresponsal</span>
                </a>
                <a href="<?= url('cuentas/movimientos') ?>" class="nav-link-custom <?= isActiveRoute('cuentas/movimientos', $currentUri) ?>">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Movimientos Caja</span>
                </a>
                <a href="<?= url('transferencias') ?>" class="nav-link-custom <?= isActiveRoute('transferencias', $currentUri) ?>">
                    <i class="bi bi-send"></i>
                    <span>Transferencias</span>
                </a>

                <!-- Reportes y Analítica -->
                <div class="sidebar-heading">Reportes & Analítica</div>
                <a href="<?= url('reportes') ?>" class="nav-link-custom <?= isActiveRoute('reportes', $currentUri) ?>">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Reportes Globales</span>
                </a>

                <!-- Inteligencia Artificial -->
                <div class="sidebar-heading">Inteligencia Artificial</div>
                <a href="<?= url('ia') ?>" class="nav-link-custom <?= isActiveRoute('ia', $currentUri) ?>">
                    <i class="bi bi-cpu text-info"></i>
                    <span>Diagnósticos IA</span>
                </a>
                <a href="<?= url('ia/recomendaciones') ?>" class="nav-link-custom <?= isActiveRoute('ia/recomendaciones', $currentUri) ?>">
                    <i class="bi bi-lightbulb text-warning"></i>
                    <span>Recomendaciones</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Sesión (Por fuera de Ver Más Opciones) -->
        <div class="sidebar-heading">Sesión</div>
        <a href="<?= url('auth/logout') ?>" class="nav-link-custom text-danger">
            <i class="bi bi-box-arrow-right"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>
