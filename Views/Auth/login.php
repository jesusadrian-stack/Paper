<!--
============================================================================
VISTA DE AUTENTICACIÓN: login.php (Pantalla de Inicio de Sesión)
============================================================================

¿PARA QUÉ SIRVE ESTA VISTA?:
Presenta la interfaz gráfica para que los usuarios (Administradores o Trabajadores)
autentiquen su identidad mediante usuario/correo y contraseña para acceder al sistema.

MAPA DE CONEXIONES DE ESTA VISTA:
1. CONTROLADOR ORIGEN: Invocada por AuthController::showLogin() mediante la función render('Auth/login').
2. CONSTANTES GLOBALES: Utiliza APP_NAME (definida en Config/config.php) para el título y encabezados.
3. HOJAS DE ESTILO & FUENTES:
   - Bootstrap 5.3 CDN (diseño responsivo y componentes UI).
   - Bootstrap Icons CDN (iconografía del sistema).
   - Google Fonts 'Plus Jakarta Sans' (tipografía moderna).
   - Estilo personalizado: public/css/style.css mediante url('css/style.css').
4. ASSETS E IMÁGENES:
   - public/images/brand_logo.jpg (Logo de la marca).
   - public/images/login_hero.jpg (Ilustración del banner visual).
5. COMPONENTES REUTILIZABLES:
   - Incluye Views/Components/alerts.php para mostrar mensajes flash de error, advertencia o éxito.
6. FORMULARIO Y ENRUTADOR:
   - <form action="<?= url('auth/login') ?>" method="POST">: Envía las credenciales a Routes/auth.php 
     -> Router::post('auth/login') -> AuthController::login() -> Modelo Usuario::findByEmailOrUsername.
7. ENLACES DE NAVEGACIÓN:
   - Enlace a Registro: <?= url('auth/register') ?> conecta con Routes/auth.php -> AuthController::showRegister().
8. SCRIPTS JS:
   - Bootstrap Bundle JS (CDN).
   - Función fillCredentials(user, pass) en JavaScript para auto-rellenar credenciales de prueba.
============================================================================
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración del juego de caracteres UTF-8 para soporte de tildes y caracteres especiales -->
    <meta charset="UTF-8">
    <!-- Viewport para garantizar diseño 100% responsivo en smartphones, tablets y pantallas de escritorio -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CONEXIÓN: Título dinámico conectado con la constante APP_NAME definida en Config/config.php -->
    <title>Iniciar Sesión - <?= APP_NAME ?></title>

    <!-- CONEXIÓN EXTERNA: Framework CSS Bootstrap 5.3 para la grilla y utilidades visuales -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CONEXIÓN EXTERNA: Biblioteca de iconos Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- CONEXIÓN EXTERNA: Tipografía moderna Google Fonts 'Plus Jakarta Sans' -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CONEXIÓN LOCAL: Hoja de estilos principal del proyecto ubicada en /public/css/style.css -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">

    <!-- Estilos específicos para la tarjeta flotante de login y el fondo en degradado oscuro -->
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
            max-width: 960px;
            width: 100%;
        }
        .login-main-card {
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
            height: 240px;
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
    <!-- Contenedor general centrado de la tarjeta de autenticación -->
    <div class="auth-card-container">
        <div class="card login-main-card">
            <div class="row g-0">
                
                <!-- ========================================================== -->
                <!-- COLUMNA IZQUIERDA: Banner Visual, Marca & Presentación     -->
                <!-- ========================================================== -->
                <div class="col-lg-6 hero-banner-side text-white p-4 p-md-5 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Encabezado de la marca con Logo y Nombre -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <!-- CONEXIÓN: Logo del sistema ubicado en public/images/brand_logo.jpg -->
                            <img src="<?= url('images/brand_logo.jpg') ?>" alt="Logo Marca" class="rounded-circle shadow-lg" style="width: 52px; height: 52px; object-fit: cover; border: 2px solid rgba(255,255,255,0.6);">
                            <div>
                                <h5 class="fw-bold mb-0 text-white"><?= APP_NAME ?></h5>
                                <small class="text-white-50">Plataforma Integrada POS & Bancario</small>
                            </div>
                        </div>

                        <!-- CONEXIÓN: Imagen ilustrativa ubicada en public/images/login_hero.jpg -->
                        <div class="hero-img-wrap mb-4">
                            <img src="<?= url('images/login_hero.jpg') ?>" alt="Papelería y Corresponsal Moderno">
                        </div>

                        <h4 class="fw-bold mb-2">Punto de Venta e Inventario</h4>
                        <p class="text-white-50 small mb-0">
                            Gestión integral de ventas rápidas, catálogo de productos, corresponsalía bancaria y reportes inteligentes impulsados por IA.
                        </p>
                    </div>

                    <!-- Pie del banner con distintivos de seguridad y tecnología -->
                    <div class="pt-4 d-flex align-items-center justify-content-between border-top border-white border-opacity-10 mt-4">
                        <span class="brand-badge"><i class="bi bi-shield-lock-fill text-warning"></i> Acceso Seguro</span>
                        <span class="text-white-50 small"><i class="bi bi-bootstrap-fill text-info"></i> Bootstrap 5.3</span>
                    </div>
                </div>

                <!-- ========================================================== -->
                <!-- COLUMNA DERECHA: Formulario de Inicio de Sesión             -->
                <!-- ========================================================== -->
                <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-white">
                    <div class="mb-4 text-center text-lg-start">
                        <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill mb-2">Bienvenido de nuevo</span>
                        <h3 class="fw-bold text-dark mb-1">Iniciar Sesión</h3>
                        <p class="text-muted small">Ingresa tus credenciales para acceder al sistema</p>
                    </div>

                    <!-- 
                        CONEXIÓN DE COMPONENTE: 
                        Incluye /Views/Components/alerts.php para renderizar mensajes flash
                        de error o éxito generados por AuthController (ej: "Usuario o clave incorrecta").
                    -->
                    <?php require_once VIEWS_PATH . '/Components/alerts.php'; ?>

                    <!-- 
                        CONEXIÓN DEL FORMULARIO (POST):
                        - action="<?= url('auth/login') ?>" ──> Envía a Routes/auth.php (Router::post('auth/login'))
                        - Controlador Conectado: AuthController::login()
                        - Modelo Conectado: models/Usuario.php (valida password_verify y estado activo)
                    -->
                    <form action="<?= url('auth/login') ?>" method="POST">
                        
                        <!-- Campo: Usuario o Correo Electrónico -->
                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label fw-semibold text-secondary small">Usuario o Correo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <!-- Atributo 'name="nombre_usuario"' es leído por AuthController mediante $_POST['nombre_usuario'] -->
                                <input type="text" class="form-control border-start-0 ps-0" id="nombre_usuario" name="nombre_usuario" placeholder="Ej: admin o trabajador" required autofocus>
                            </div>
                        </div>

                        <!-- Campo: Contraseña -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="contrasena" class="form-label fw-semibold text-secondary small mb-0">Contraseña</label>
                            </div>
                            <div class="input-group mt-1">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <!-- Atributo 'name="contrasena"' es leído por AuthController mediante $_POST['contrasena'] -->
                                <input type="password" class="form-control border-start-0 ps-0" id="contrasena" name="contrasena" placeholder="••••••••" required>
                            </div>
                        </div>

                        <!-- Botón de Envío del Formulario -->
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Ingresar al Sistema
                        </button>

                        <!-- CONEXIÓN: Enlace a la pantalla de registro -> Routes/auth.php -> AuthController::showRegister() -->
                        <div class="text-center">
                            <span class="small text-muted">¿Nuevo operador? </span>
                            <a href="<?= url('auth/register') ?>" class="small text-primary fw-bold text-decoration-none">
                                <i class="bi bi-person-plus-fill me-1"></i> Regístrate aquí
                            </a>
                        </div>
                    </form>

                    <!-- Acceso rápido con botones para desarrollo / demostración rápida -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="text-muted small fw-semibold mb-2 text-center text-lg-start">Acceso rápido de prueba:</div>
                        <div class="d-flex flex-column gap-2">
                            <!-- Dispara la función JS fillCredentials() con credenciales de Administrador -->
                            <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-between text-start" onclick="fillCredentials('admin', '12345')">
                                <span><i class="bi bi-shield-check text-primary me-2"></i><strong>Admin:</strong> admin</span>
                                <span class="badge bg-secondary-subtle text-secondary">12345</span>
                            </button>
                            <!-- Dispara la función JS fillCredentials() con credenciales de Trabajador -->
                            <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-between text-start" onclick="fillCredentials('trabajador', 'trabajador123')">
                                <span><i class="bi bi-person-check text-success me-2"></i><strong>Trabajador:</strong> trabajador</span>
                                <span class="badge bg-secondary-subtle text-secondary">trabajador123</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONEXIÓN EXTERNA: Javascript de Bootstrap 5.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script interactivo local para rellenar los inputs del formulario automáticamente -->
    <script>
        function fillCredentials(user, pass) {
            document.getElementById('nombre_usuario').value = user;
            document.getElementById('contrasena').value = pass;
        }
    </script>
</body>
</html>
