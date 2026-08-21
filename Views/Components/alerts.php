<?php
$flash = getFlashMessage();
if ($flash):
    $alertClass = 'alert-' . ($flash['type'] ?? 'info');
    $iconClass = match($flash['type'] ?? 'info') {
        'success' => 'bi-check-circle-fill',
        'danger'  => 'bi-exclamation-octagon-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        default   => 'bi-info-circle-fill'
    };
?>
    <div class="alert <?= $alertClass ?> alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
        <i class="bi <?= $iconClass ?> fs-5"></i>
        <div><?= sanitize($flash['message']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>
