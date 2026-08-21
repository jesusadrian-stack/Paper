<?php
$pageTitle = 'Informe de Análisis IA: ' . sanitize($analisis['titulo']);
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div class="row g-4 mx-auto" style="max-width: 1000px;">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-3 mb-4 gap-2">
                <div>
                    <span class="badge bg-primary-subtle text-primary mb-1">MÓDULO DE INTELIGENCIA ARTIFICIAL</span>
                    <h4 class="fw-bold mb-1 text-dark"><?= sanitize($analisis['titulo']) ?></h4>
                    <small class="text-muted">Generado el <?= date('d/m/Y H:i', strtotime($analisis['fecha_analisis'])) ?> por <strong><?= sanitize($analisis['usuario_nombre'] ?? 'Sistema') ?></strong></small>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Imprimir</button>
                    <a href="<?= url('ia') ?>" class="btn btn-outline-secondary btn-sm">Volver a IA</a>
                </div>
            </div>

            <!-- Contenido del Análisis en Formato Markdown / HTML -->
            <div class="p-4 bg-light rounded-4 mb-4" style="line-height: 1.7;">
                <?php
                // Convertir markdown básico a HTML seguro
                $parsed = htmlspecialchars($analisis['resultado']);
                // Títulos
                $parsed = preg_replace('/### (.*?)\n/', '<h5 class="fw-bold text-primary mt-3 mb-2">$1</h5>', $parsed);
                $parsed = preg_replace('/#### (.*?)\n/', '<h6 class="fw-bold text-dark mt-3 mb-2">$1</h6>', $parsed);
                // Negritas
                $parsed = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $parsed);
                // Listas
                $parsed = preg_replace('/- (.*?)\n/', '<li class="mb-1">$1</li>', $parsed);
                $parsed = nl2br($parsed);
                echo $parsed;
                ?>
            </div>

            <!-- Recomendaciones Asociadas a este Análisis -->
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-list-check text-primary me-2"></i>Acciones Recomendadas por el Asistente</h5>
            <?php if (empty($recomendaciones)): ?>
                <p class="text-muted small">No se generaron recomendaciones puntuales asociadas.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($recomendaciones as $r): ?>
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded-3 h-100 bg-white shadow-sm d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-<?= $r['prioridad'] === 'ALTA' ? 'danger' : 'warning' ?>-subtle text-<?= $r['prioridad'] === 'ALTA' ? 'danger' : 'dark' ?> fw-bold">
                                            <?= $r['prioridad'] ?>
                                        </span>
                                        <small class="text-muted"><?= $r['tipo'] ?></small>
                                    </div>
                                    <p class="small text-dark mb-3"><?= sanitize($r['recomendacion']) ?></p>
                                </div>
                                <div class="text-end border-top pt-2">
                                    <?php if ($r['atendida']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-all"></i> Atendida</span>
                                    <?php else: ?>
                                        <a href="<?= url('ia/recomendaciones/resolve?id=' . $r['id_recomendacion']) ?>" class="btn btn-sm btn-outline-success py-0 px-2">
                                            <i class="bi bi-check2"></i> Marcar Atendida
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
