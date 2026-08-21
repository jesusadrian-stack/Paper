<?php
$pageTitle = 'Módulo de Inteligencia Artificial';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<!-- Header de Inteligencia Artificial -->
<div class="card card-ai p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge-ai"><i class="bi bi-stars"></i> Asistente de Negocio IA</span>
                <span class="badge bg-white text-dark small">Motor Analítico Conectado</span>
            </div>
            <h4 class="fw-bold mb-1">Diagnóstico Comercial e Inteligencia de Inventarios</h4>
            <p class="text-white-50 small mb-0">Genere análisis predictivos de abastecimiento, detección de tendencias comerciales y optimización de catálogo con un solo clic.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= url('ia/generar?tipo=reabastecimiento') ?>" class="btn btn-warning text-dark fw-bold btn-sm shadow-sm">
                <i class="bi bi-box-seam me-1"></i> Analizar Reabastecimiento
            </a>
            <a href="<?= url('ia/generar?tipo=tendencias') ?>" class="btn btn-light text-primary fw-bold btn-sm shadow-sm">
                <i class="bi bi-graph-up me-1"></i> Analizar Tendencias
            </a>
            <a href="<?= url('ia/recomendaciones') ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-lightbulb me-1"></i> Recomendaciones (<?= $totalPendientes ?>)
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Lista de Análisis Históricos -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-text text-primary me-2"></i>Historial de Diagnósticos Generados</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Sugerencias</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($analisisList)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">No se ha generado ningún análisis de IA aún.<br>Haga clic en uno de los botones superiores para ejecutar un diagnóstico.</td></tr>
                        <?php else: ?>
                            <?php foreach ($analisisList as $a): ?>
                                <tr>
                                    <td><small><?= date('d/m/Y H:i', strtotime($a['fecha_analisis'])) ?></small></td>
                                    <td><span class="badge bg-light text-dark border"><?= $a['tipo'] ?></span></td>
                                    <td class="fw-semibold"><?= sanitize($a['titulo']) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= $a['total_recomendaciones'] ?> acciones</span></td>
                                    <td class="text-end">
                                        <a href="<?= url('ia/show?id=' . $a['id_analisis']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Ver Análisis">
                                            <i class="bi bi-eye"></i> Leer
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

    <!-- Recomendaciones Pendientes -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-lightbulb text-warning me-2"></i>Recomendaciones Pendientes</h5>
                <a href="<?= url('ia/recomendaciones') ?>" class="btn btn-sm btn-link text-decoration-none">Ver todas &rarr;</a>
            </div>

            <?php if (empty($recomendacionesPendientes)): ?>
                <div class="text-center text-muted py-5 my-auto">
                    <i class="bi bi-check-circle text-success fs-1 mb-2 d-block"></i>
                    <p class="small mb-0">¡No hay recomendaciones pendientes por atender!</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recomendacionesPendientes as $rec): ?>
                        <div class="list-group-item px-0 py-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-<?= $rec['prioridad'] === 'ALTA' ? 'danger' : 'warning' ?>-subtle text-<?= $rec['prioridad'] === 'ALTA' ? 'danger' : 'dark' ?> fw-bold" style="font-size: 0.7rem;">
                                    PRIORIDAD <?= $rec['prioridad'] ?>
                                </span>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($rec['fecha_recomendacion'])) ?></small>
                            </div>
                            <p class="small text-dark mb-2"><?= sanitize($rec['recomendacion']) ?></p>
                            <div class="text-end">
                                <a href="<?= url('ia/recomendaciones/resolve?id=' . $rec['id_recomendacion']) ?>" class="btn btn-sm btn-outline-success py-0 px-2">
                                    <i class="bi bi-check2"></i> Marcar como Atendida
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
