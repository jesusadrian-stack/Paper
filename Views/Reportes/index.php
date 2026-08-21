<?php
$pageTitle = 'Centro de Reportes y Analítica';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
    <h5 class="fw-bold mb-1">Centro de Reportes Ejecutivos</h5>
    <p class="text-muted small mb-0">Seleccione el tipo de análisis que desea generar y exportar para auditoría o toma de decisiones.</p>
</div>

<div class="row g-4">
    <!-- Reporte de Ventas -->
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
            <div class="stat-icon primary mb-3 fs-3"><i class="bi bi-cart-check"></i></div>
            <h5 class="fw-bold">Reporte de Ventas</h5>
            <p class="text-muted small mb-4">Consolide ventas por período, rendimiento por cajero, total recaudado y desglose de comprobantes.</p>
            <a href="<?= url('reportes/ventas') ?>" class="btn btn-primary btn-sm mt-auto fw-semibold">
                Abrir Reporte de Ventas &rarr;
            </a>
        </div>
    </div>

    <!-- Reporte de Inventario -->
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
            <div class="stat-icon warning mb-3 fs-3"><i class="bi bi-box-seam"></i></div>
            <h5 class="fw-bold">Reporte de Inventario</h5>
            <p class="text-muted small mb-4">Valor total del stock en bodega, conteo de unidades físicas, alertas de stock mínimo y clasificación.</p>
            <a href="<?= url('reportes/inventario') ?>" class="btn btn-warning text-dark btn-sm mt-auto fw-semibold">
                Abrir Reporte de Inventario &rarr;
            </a>
        </div>
    </div>

    <!-- Reporte Financiero -->
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
            <div class="stat-icon success mb-3 fs-3"><i class="bi bi-cash-stack"></i></div>
            <h5 class="fw-bold">Reporte Financiero y Caja</h5>
            <p class="text-muted small mb-4">Flujo de caja consolidado, balance de ingresos, egresos, depósitos y retiros de corresponsal.</p>
            <a href="<?= url('reportes/finanzas') ?>" class="btn btn-success btn-sm mt-auto fw-semibold">
                Abrir Reporte Financiero &rarr;
            </a>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
