<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <script>
        window.APP_BASE_URL = "<?= url() ?>";
        window.APP_URL_VENTAS_STORE = "<?= url('ventas/store') ?>";
        window.APP_URL_VENTAS_SHOW = "<?= url('ventas/show') ?>";
    </script>
</head>
<body>
    <div id="wrapper">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php require_once VIEWS_PATH . '/Layouts/sidebar.php'; ?>
            <div id="content-wrapper">
                <!-- Top Navbar -->
                <header class="topbar">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <h5 class="mb-0 fw-bold text-dark"><?= isset($pageTitle) ? sanitize($pageTitle) : 'Panel de Control' ?></h5>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <?php if (($_SESSION['user_role'] ?? '') === 'ADMINISTRADOR'): ?>
                            <a href="<?= url('ia') ?>" class="btn btn-sm btn-outline-primary d-none d-sm-inline-flex align-items-center gap-1">
                                <i class="bi bi-stars text-warning"></i> Asistente IA
                            </a>
                        <?php endif; ?>

                        <div class="user-badge dropdown">
                            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div class="d-none d-sm-block text-start">
                                    <div class="fw-bold fs-6 lh-1"><?= sanitize($_SESSION['user_name'] ?? 'Usuario') ?></div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;"><?= sanitize($_SESSION['user_role'] ?? '') ?></small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><h6 class="dropdown-header">Conectado como: <?= sanitize($_SESSION['user_username'] ?? '') ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </header>

                <main class="container-fluid p-4">
                    <?php require_once VIEWS_PATH . '/Components/alerts.php'; ?>
        <?php endif; ?>
