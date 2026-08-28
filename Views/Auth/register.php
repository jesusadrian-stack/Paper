<!--
============================================================================
VISTA DE AUTENTICACIÓN: register.php (Pantalla de Registro de Operadores)
============================================================================

¿PARA QUÉ SIRVE ESTA VISTA?:
Presenta el formulario de captura para crear nuevos operadores y trabajadores
en el sistema, asignando nombres, documento, teléfono, correo, usuario, rol y clave.

MAPA DE CONEXIONES DE ESTA VISTA:
1. CONTROLADOR ORIGEN: Invocada por AuthController::showRegister() mediante render('Auth/register', ['roles' => $roles]).
2. VARIABLES PHP RECIBIDAS:
   - `$roles`: Array asociativo proveniente del modelo Rol::all() para listar los roles en el <select>.
3. CONSTANTES GLOBALES:
   - APP_NAME (definida en Config/config.php) para el título y marca.
   - VIEWS_PATH (definida en Config/config.php) para incluir componentes.
4. HOJAS DE ESTILO & FUENTES:
   - Bootstrap 5.3 CSS (CDN).
   - Bootstrap Icons (CDN).
   - Google Fonts 'Plus Jakarta Sans' (CDN).
   - public/css/style.css mediante url('css/style.css').
5. ASSETS E IMÁGENES:
   - public/images/brand_logo.jpg (Logo de la marca).
   - public/images/register_hero.jpg (Ilustración del banner visual de registro).
6. COMPONENTES REUTILIZABLES:
   - Views/Components/alerts.php para mostrar errores de validación (ej: "Las contraseñas no coinciden").
7. FORMULARIO Y ENRUTADOR:
   - <form action="<?= url('auth/register') ?>" method="POST">: Envía los datos a Routes/auth.php 
     -> Router::post('auth/register') -> AuthController::register() -> Modelo Usuario::create.
8. ENLACES DE NAVEGACIÓN:
   - Enlace a Iniciar Sesión: <?= url('auth/login') ?> conecta con Routes/auth.php -> AuthController::showLogin().
9. SCRIPTS JS:
   - Bootstrap 5.3 Bundle JS (CDN).
============================================================================
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración del juego de caracteres UTF-8 para admitir caracteres en español -->
    <meta charset="UTF-8">
    <!-- Viewport responsivo para adaptación automática en móviles, tablets y PCs -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CONEXIÓN: Título de la pestaña conectado dinámicamente con la constante APP_NAME de Config/config.php -->
    <title>Crear Cuenta / Registrar Operador - <?= APP_NAME ?></title>

    <!-- CONEXIÓN EXTERNA: Hoja de estilos Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CONEXIÓN EXTERNA: Iconografía Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- CONEXIÓN EXTERNA: Tipografía Google Fonts 'Plus Jakarta Sans' -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CONEXIÓN LOCAL: Archivo CSS principal ubicado en public/css/style.css -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">

    <!-- Estilos específicos para la tarjeta flotante de registro y estética oscura -->
    <style>
        body {
            background: linear-gradient(135deg, #090d16 0%, #111827 50%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 1.5rem 0.75rem;
        }
        .auth-card-container {
            max-width: 1050px;
            width: 100%;
        }
        .register-main-card {
            border: none;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            overflow: hidden;
        }
        .hero-banner-side {
            background: linear-gradient(145deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .hero-img-wrap {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            border: 2px solid rgba(255,255,255,0.15);
        }
        .hero-img-wrap img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .hero-img-wrap:hover img {
            transform: scale(1.03);
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            padding: 0.4rem 0.9rem;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Contenedor principal de la tarjeta de registro -->
    <div class="auth-card-container">
        <div class="card register-main-card">
            <div class="row g-0">
                
                <!-- ========================================================== -->
                <!-- COLUMNA IZQUIERDA: Banner Visual & Beneficios del Sistema  -->
                <!-- ========================================================== -->
                <div class="col-lg-5 hero-banner-side text-white p-4 p-md-5 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Logo y Nombre del Sistema -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <!-- CONEXIÓN: Logo en public/images/brand_logo.jpg -->
                            <img src="<?= url('images/brand_logo.jpg') ?>" alt="Logo Marca" class="rounded-circle shadow-lg" style="width: 52px; height: 52px; object-fit: cover; border: 2px solid rgba(255,255,255,0.6);">
                            <div>
                                <h5 class="fw-bold mb-0 text-white"><?= APP_NAME ?></h5>
                                <small class="text-white-50">Registro de Nuevos Operadores</small>
                            </div>
                        </div>

                        <!-- CONEXIÓN: Ilustración de onboarding en public/images/register_hero.jpg -->
                        <div class="hero-img-wrap mb-4">
                            <img src="<?= url('images/register_hero.jpg') ?>" alt="Registro y Onboarding de Operadores">
                        </div>

                        <h5 class="fw-bold mb-2">Acceso Seguro al Punto de Venta</h5>
                        <p class="text-white-50 small mb-0">
                            Crea tu cuenta de operador para registrar ventas, gestionar movimientos de inventario, corresponsal bancario y consultar auditoría.
                        </p>
                    </div>

                    <!-- Pie del banner con badges informativos -->
                    <div class="pt-4 d-flex align-items-center justify-content-between border-top border-white border-opacity-10 mt-4">
                        <span class="brand-badge"><i class="bi bi-person-badge text-warning"></i> Control de Roles</span>
                        <span class="text-white-50 small"><i class="bi bi-shield-check text-success"></i> Verificado</span>
                    </div>
                </div>

                <!-- ========================================================== -->
                <!-- COLUMNA DERECHA: Formulario de Captura de Datos            -->
                <!-- ========================================================== -->
                <div class="col-lg-7 p-4 p-md-5 bg-white">
                    <div class="mb-4">
                        <span class="badge bg-success-subtle text-success fw-semibold px-3 py-2 rounded-pill mb-2">Nuevo Usuario</span>
                        <h3 class="fw-bold text-dark mb-1">Registro de Operador</h3>
                        <p class="text-muted small">Completa los campos para crear tu cuenta en <?= APP_NAME ?></p>
                    </div>

                    <!-- 
                        CONEXIÓN DE COMPONENTE: 
                        Incluye /Views/Components/alerts.php para mostrar mensajes de validación
                        (ej: correo duplicado, campos vacíos o contraseñas que no coinciden).
                    -->
                    <?php require_once VIEWS_PATH . '/Components/alerts.php'; ?>

                    <!-- 
                        CONEXIÓN DEL FORMULARIO (POST):
                        - action="<?= url('auth/register') ?>" ──> Envía a Routes/auth.php (Router::post('auth/register'))
                        - Controlador Destino: AuthController::register()
                        - Modelo Destino: models/Usuario.php (método create con cifrado password_hash)
                    -->
                    <form action="<?= url('auth/register') ?>" method="POST">
                        <div class="row g-3">
                            
                            <!-- Campo: Nombres -->
                            <div class="col-sm-6">
                                <label for="nombre" class="form-label fw-semibold small text-secondary">Nombres *</label>
                                <!-- Leído en AuthController como $_POST['nombre'] -->
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Daniel" required autofocus>
                            </div>

                            <!-- Campo: Apellidos -->
                            <div class="col-sm-6">
                                <label for="apellido" class="form-label fw-semibold small text-secondary">Apellidos *</label>
                                <!-- Leído en AuthController como $_POST['apellido'] -->
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ej: Morales" required>
                            </div>

                            <!-- Campo: Documento de Identidad / Cédula -->
                            <div class="col-sm-6">
                                <label for="documento" class="form-label fw-semibold small text-secondary">Documento de Identidad *</label>
                                <!-- Leído en AuthController como $_POST['documento'] -->
                                <input type="text" class="form-control" id="documento" name="documento" placeholder="Cédula / ID" required>
                            </div>

                            <!-- Campo: Teléfono -->
                            <div class="col-sm-6">
                                <label for="telefono" class="form-label fw-semibold small text-secondary">Teléfono</label>
                                <!-- Leído en AuthController como $_POST['telefono'] -->
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej: 3101234567">
                            </div>

                            <!-- Campo: Correo Electrónico -->
                            <div class="col-12">
                                <label for="correo" class="form-label fw-semibold small text-secondary">Correo Electrónico</label>
                                <!-- Leído en AuthController como $_POST['correo'] -->
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="usuario@papeleria.com">
                            </div>

                            <!-- Campo: Nombre de Usuario para Login -->
                            <div class="col-sm-6">
                                <label for="nombre_usuario" class="form-label fw-semibold small text-secondary">Nombre de Usuario (Login) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">@</span>
                                    <!-- Leído en AuthController como $_POST['nombre_usuario'] -->
                                    <input type="text" class="form-control ps-2" id="nombre_usuario" name="nombre_usuario" placeholder="dmorales" required>
                                </div>
                            </div>

                            <!-- 
                                Campo: Selector de Rol de Acceso
                                CONEXIÓN DE DATOS: Itera sobre el array `$roles` enviado desde 
                                AuthController::showRegister() mediante el modelo `Rol::all()`.
                            -->
                            <div class="col-sm-6">
                                <label for="id_rol" class="form-label fw-semibold small text-secondary">Rol de Acceso *</label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r['id_rol'] ?>" <?= $r['nombre'] === 'TRABAJADOR' ? 'selected' : '' ?>>
                                            <?= sanitize($r['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Campo: Contraseña -->
                            <div class="col-sm-6">
                                <label for="contrasena" class="form-label fw-semibold small text-secondary">Contraseña *</label>
                                <!-- Leído en AuthController como $_POST['contrasena'] -->
                                <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="••••••••" required>
                            </div>

                            <!-- Campo: Confirmación de Contraseña -->
                            <div class="col-sm-6">
                                <label for="confirmar_contrasena" class="form-label fw-semibold small text-secondary">Confirmar Contraseña *</label>
                                <!-- Comparado en AuthController para validar coincidencia exacta -->
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="••••••••" required>
                            </div>

                            <!-- Botón de Envío para Procesar Registro -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-person-check-fill me-1"></i> Completar Registro e Ingresar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- CONEXIÓN: Enlace de retorno al Login -> Routes/auth.php -> AuthController::showLogin() -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="small text-muted mb-0">
                            ¿Ya tienes una cuenta registrada? 
                            <a href="<?= url('auth/login') ?>" class="text-primary fw-bold text-decoration-none">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Inicia Sesión aquí
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONEXIÓN EXTERNA: Javascript de Bootstrap 5.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
