<?php
$pageTitle = 'Panel de Control Principal';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<!-- Hero Banner Ilustrativo -->
<div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #ffffff;">
    <div class="row g-0 align-items-center">
        <div class="col-12 col-md-7 p-4 p-lg-5">
            <span class="badge bg-white text-dark mb-2 px-3 py-2 fw-semibold"><i class="bi bi-shop me-1 text-primary"></i> <?= APP_NAME ?></span>
            <h3 class="fw-bold text-white mb-2">¡Hola, <?= sanitize($_SESSION['user_name'] ?? 'Usuario') ?>! 👋</h3>
            <p class="text-white-50 mb-4" style="max-width: 520px;">Bienvenido/a al panel de control de tu papelería y corresponsal bancario. Gestiona ventas en tiempo real, controla existencias y optimiza con Inteligencia Artificial.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= url('ventas/create') ?>" class="btn btn-success fw-bold px-3 py-2 shadow-sm">
                    <i class="bi bi-cart-plus me-1"></i> Punto de Venta (POS)
                </a>
                <a href="<?= url('corresponsal') ?>" class="btn btn-outline-light px-3 py-2">
                    <i class="bi bi-bank2 me-1"></i> Corresponsal Bancario
                </a>
                <?php if ($isAdmin): ?>
                    <a href="<?= url('ia') ?>" class="btn btn-warning text-dark fw-bold px-3 py-2">
                        <i class="bi bi-stars me-1"></i> Asistente IA
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-12 col-md-5 d-none d-md-block text-center p-3">
            <img src="<?= url('images/banner.jpg') ?>" alt="Sistema Papelería" class="img-fluid rounded-4 shadow" style="max-height: 220px; object-fit: cover; width: 95%;">
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php if ($isAdmin): ?>
        <!-- Tarjeta 1: Ventas Hoy -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Ventas Hoy</div>
                        <h3 class="fw-bold my-1 text-primary"><?= formatMoney($ventasHoy['total_ingresos']) ?></h3>
                        <small class="text-muted"><i class="bi bi-bag-check text-success"></i> <?= $ventasHoy['total_ventas'] ?> transacciones</small>
                    </div>
                    <div class="stat-icon primary">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Saldo Papelería -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Caja Papelería</div>
                        <h3 class="fw-bold my-1 text-success"><?= formatMoney($saldoPapeleria) ?></h3>
                        <small class="text-muted"><i class="bi bi-cash"></i> Saldo disponible</small>
                    </div>
                    <div class="stat-icon success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Saldo Corresponsal -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Fondo Corresponsal</div>
                        <h3 class="fw-bold my-1 text-info"><?= formatMoney($saldoCorresponsal) ?></h3>
                        <small class="text-muted"><i class="bi bi-bank2"></i> Operaciones bancarias</small>
                    </div>
                    <div class="stat-icon info">
                        <i class="bi bi-bank"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Stock Crítico / Alertas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Stock Bajo / Alertas</div>
                        <h3 class="fw-bold my-1 <?= $stockBajoCount > 0 ? 'text-danger' : 'text-secondary' ?>"><?= $stockBajoCount ?></h3>
                        <small class="text-muted"><?= $totalProductos ?> productos totales</small>
                    </div>
                    <div class="stat-icon <?= $stockBajoCount > 0 ? 'danger' : 'warning' ?>">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Dashboard Trabajador -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-stat p-4 h-100 bg-primary text-white">
                <h5 class="fw-bold mb-2">Punto de Venta Rápido</h5>
                <p class="mb-3 opacity-75">Inicie una nueva venta con escáner o búsqueda directa de productos.</p>
                <a href="<?= url('ventas/create') ?>" class="btn btn-light text-primary fw-bold">
                    <i class="bi bi-cart-plus me-1"></i> Abrir POS de Venta
                </a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-stat p-4 h-100">
                <div class="text-muted small fw-semibold text-uppercase">Mis Ventas del Día</div>
                <h3 class="fw-bold my-2 text-success"><?= formatMoney($ventasHoy['total_ingresos']) ?></h3>
                <span class="badge bg-success-subtle text-success border border-success-subtle"><?= $ventasHoy['total_ventas'] ?> ventas completadas</span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-stat p-4 h-100">
                <div class="text-muted small fw-semibold text-uppercase">Operaciones Corresponsal</div>
                <h3 class="fw-bold my-2 text-info"><?= $operacionesHoy['total_operaciones'] ?></h3>
                <small class="text-muted">Depósitos: <?= formatMoney($operacionesHoy['total_depositos']) ?> &bull; Retiros: <?= formatMoney($operacionesHoy['total_retiros']) ?></small>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($isAdmin): ?>
    <!-- Tarjeta Especial de Inteligencia Artificial -->
    <div class="card card-ai p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge-ai"><i class="bi bi-stars"></i> IA Estratégica Activa</span>
                    <span class="badge bg-white text-dark small">Motor Analítico Conectado</span>
                </div>
                <h5 class="fw-bold mb-1">Diagnóstico Predictivo de Abastecimiento y Ventas</h5>
                <p class="mb-0 text-white-50 small">Genere análisis automáticos para anticipar rotación de útiles, prevenir quiebres de stock y maximizar márgenes.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('ia/generar?tipo=reabastecimiento') ?>" class="btn btn-warning text-dark fw-bold btn-sm shadow-sm">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Analizar Stock con IA
                </a>
                <a href="<?= url('ia') ?>" class="btn btn-outline-light btn-sm">
                    Ver Informes IA
                </a>
            </div>
        </div>
    </div>

    <!-- Gráficos y Top Ventas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up text-primary me-2"></i>Tendencia de Ventas (Últimos 7 Días)</h6>
                    <a href="<?= url('reportes/ventas') ?>" class="btn btn-sm btn-link text-decoration-none">Ver reporte detallado &rarr;</a>
                </div>
                <div style="height: 260px;">
                    <canvas id="salesTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-trophy text-warning me-2"></i>Productos Más Vendidos</h6>
                <?php if (empty($topProducts)): ?>
                    <p class="text-muted small my-auto text-center">No hay suficientes registros de ventas aún.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($topProducts as $tp): ?>
                            <div class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-truncate" style="max-width: 170px;"><?= sanitize($tp['nombre']) ?></div>
                                    <small class="text-muted"><?= sanitize($tp['categoria_nombre']) ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill"><?= $tp['total_unidades'] ?> uds</span>
                                    <div class="small fw-bold text-dark"><?= formatMoney($tp['total_recaudado']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Tablas de Actividad Reciente y Alertas -->
<div class="row g-4">
    <!-- Ventas Recientes -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-primary me-2"></i>Ventas Recientes</h6>
                <a href="<?= url('ventas') ?>" class="btn btn-sm btn-link text-decoration-none">Ver todas &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Vendedor</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ventasRecientes)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No hay ventas registradas recientemente.</td></tr>
                        <?php else: ?>
                            <?php foreach ($ventasRecientes as $v): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border">#<?= $v['id_venta'] ?></span></td>
                                    <td><?= sanitize($v['cliente_nombre'] ?? 'Cliente Mostrador') ?></td>
                                    <td class="fw-bold text-success"><?= formatMoney($v['total']) ?></td>
                                    <td><small class="text-muted"><?= sanitize($v['usuario_nombre']) ?></small></td>
                                    <td>
                                        <a href="<?= url('ventas/show?id=' . $v['id_venta']) ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Ver Venta">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alertas de Stock Crítico -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-bell text-danger me-2"></i>Alertas de Inventario</h6>
                <a href="<?= url('alertas') ?>" class="btn btn-sm btn-link text-decoration-none">Ver alertas &rarr;</a>
            </div>
            <?php if (empty($alertasPendientes)): ?>
                <div class="text-center py-4 my-auto">
                    <i class="bi bi-shield-check text-success fs-1"></i>
                    <p class="text-muted small mt-2 mb-0">¡Todos los productos cuentan con niveles óptimos de inventario!</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($alertasPendientes, 0, 4) as $alt): ?>
                        <div class="list-group-item px-0 py-2 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold text-dark small"><?= sanitize($alt['producto_nombre']) ?></div>
                                    <small class="text-muted">Stock actual: <strong class="text-danger"><?= $alt['stock_actual'] ?></strong> (Mín: <?= $alt['stock_minimo'] ?>)</small>
                                </div>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Crítico</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($isAdmin && !empty($salesChartData)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesTrendsChart');
    if (!ctx) return;

    const chartData = <?= json_encode($salesChartData) ?>;
    const labels = chartData.map(item => item.fecha);
    const totals = chartData.map(item => parseFloat(item.total));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas ($)',
                data: totals,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#4f46e5',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '$' + value.toLocaleString(); }
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
