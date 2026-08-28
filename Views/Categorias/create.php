<?php
/**
 * ============================================================================
 * VISTA: Views/Categorias/create.php (Formulario de Creación de Categoría)
 * ============================================================================
 * 
 * ¿PARA QUÉ SIRVE ESTA VISTA?:
 * Presenta el formulario de captura para registrar una nueva categoría en la base
 * de datos (por ejemplo: "Útiles Escolares", "Papelería Comercial", "Tecnología").
 * 
 * MAPA DE CONEXIONES DE ESTA VISTA:
 * 1. CONTROLADOR ORIGEN:
 *    - Invocada por: CategoriaController::create() en /Controllers/CategoriaController.php
 *    - Ruta HTTP que la dispara: GET http://localhost/Paper/categorias/create (Routes/productos.php)
 * 2. LAYOUTS Y COMPONENTES INCLUIDOS:
 *    - Views/Layouts/header.php (Estructura superior, Sidebar, Navbar y alertas del sistema).
 *    - Views/Layouts/footer.php (Estructura inferior y librerías JavaScript).
 * 3. DESTINO DEL FORMULARIO (POST):
 *    - <form action="<?= url('categorias/store') ?>" method="POST">:
 *      Conecta con: Routes/productos.php -> Router::post('categorias/store')
 *      ──> CategoriaController::store() ──> Modelo Categoria::create($data)
 *      ──> Inserta en la tabla 'categorias' de la BD y redirige a 'categorias' con mensaje flash de éxito.
 * 4. BOTONES DE RETORNO Y CANCELACIÓN:
 *    - Botón "Volver" / "Cancelar" ──> url('categorias') ──> Regresa al listado principal.
 * ============================================================================
 */

// Define el título que se mostrará en la pestaña del navegador y barra superior
$pageTitle = 'Crear Categoría';

// CONEXIÓN: Incluye la cabecera del Layout general
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<!-- Contenedor centrado tipo tarjeta para el formulario -->
<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 600px;">
    
    <!-- Encabezado del formulario y botón de retorno al listado -->
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Nueva Categoría</h5>
            <p class="text-muted small mb-0">Defina una nueva categoría para organizar productos.</p>
        </div>
        
        <!-- CONEXIÓN: Enlace de retorno hacia el listado de categorías (GET 'categorias') -->
        <a href="<?= url('categorias') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <!-- 
        CONEXIÓN DEL FORMULARIO:
        - action="<?= url('categorias/store') ?>" ──> Envía por POST a Routes/productos.php
        - Procesado por: CategoriaController::store() ──> Guarda en tabla 'categorias'
    -->
    <form action="<?= url('categorias/store') ?>" method="POST">
        
        <!-- Campo: Nombre de la Categoría (Requerido) -->
        <div class="mb-3">
            <label for="nombre" class="form-label fw-semibold">Nombre de la Categoría *</label>
            <!-- Leído en CategoriaController como $_POST['nombre'] -->
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Arte y Manualidades" required autofocus>
        </div>

        <!-- Campo: Descripción Opcional -->
        <div class="mb-3">
            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
            <!-- Leído en CategoriaController como $_POST['descripcion'] -->
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Detalle de productos agrupados en esta categoría..."></textarea>
        </div>

        <!-- Campo: Estado Inicial de la Categoría -->
        <div class="mb-4">
            <label for="estado" class="form-label fw-semibold">Estado</label>
            <!-- Leído en CategoriaController como $_POST['estado'] -->
            <select class="form-select" id="estado" name="estado">
                <option value="ACTIVO" selected>ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>

        <!-- Botones de Acción: Cancelar y Guardar -->
        <div class="text-end">
            <!-- CONEXIÓN: Botón cancelar redirige al listado 'categorias' -->
            <a href="<?= url('categorias') ?>" class="btn btn-secondary me-2">Cancelar</a>
            
            <!-- Botón Submit que despacha el formulario POST -->
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-save me-1"></i> Guardar Categoría
            </button>
        </div>
    </form>
</div>

<!-- CONEXIÓN: Incluye el pie de página del Layout general -->
<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
