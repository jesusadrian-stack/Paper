<?php
/**
 * ============================================================================
 * VISTA: Views/Categorias/edit.php (Formulario de Edición de Categoría)
 * ============================================================================
 * 
 * ¿PARA QUÉ SIRVE ESTA VISTA?:
 * Permite modificar los datos existentes de una categoría previamente registrada
 * (cambiar su nombre, descripción o su estado ACTIVO/INACTIVO).
 * 
 * MAPA DE CONEXIONES DE ESTA VISTA:
 * 1. CONTROLADOR ORIGEN:
 *    - Invocada por: CategoriaController::edit() en /Controllers/CategoriaController.php
 *    - Ruta HTTP: GET http://localhost/Paper/categorias/edit?id=X (Routes/productos.php)
 * 2. VARIABLES PHP RECIBIDAS DEL CONTROLADOR:
 *    - `$categoria`: Array asociativo obtenido de Categoria::findById($id) con los datos del registro actual.
 * 3. LAYOUTS INCLUIDOS:
 *    - Views/Layouts/header.php (Estructura superior, Sidebar, Navbar y alertas del sistema).
 *    - Views/Layouts/footer.php (Estructura inferior y librerías JavaScript).
 * 4. DESTINO DEL FORMULARIO (POST):
 *    - <form action="<?= url('categorias/update?id=' . $categoria['id_categoria']) ?>" method="POST">:
 *      Conecta con: Routes/productos.php -> Router::post('categorias/update')
 *      ──> CategoriaController::update() ──> Modelo Categoria::update($id, $data)
 *      ──> Actualiza la fila en la tabla 'categorias' y redirige a 'categorias' con mensaje de confirmación.
 * 5. BOTONES DE RETORNO Y CANCELACIÓN:
 *    - Botón "Volver" / "Cancelar" ──> url('categorias') ──> Regresa al listado sin guardar cambios.
 * ============================================================================
 */

// Define el título dinámico de la página
$pageTitle = 'Editar Categoría';

// CONEXIÓN: Carga el encabezado del Layout principal
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<!-- Contenedor centrado tipo tarjeta para el formulario de edición -->
<div class="card shadow-sm border-0 rounded-4 p-4 mx-auto" style="max-width: 600px;">
    
    <!-- Encabezado con ID de la categoría y botón para volver al listado -->
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
        <div>
            <!-- Muestra el ID de la categoría siendo editada -->
            <h5 class="fw-bold mb-1">Editar Categoría #<?= $categoria['id_categoria'] ?></h5>
            <p class="text-muted small mb-0">Modifique los datos de la categoría.</p>
        </div>
        
        <!-- CONEXIÓN: Enlace de retorno hacia el listado de categorías (GET 'categorias') -->
        <a href="<?= url('categorias') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <!-- 
        CONEXIÓN DEL FORMULARIO DE ACTUALIZACIÓN:
        - action="<?= url('categorias/update?id=' . $categoria['id_categoria']) ?>" ──> Envía por POST a Routes/productos.php
        - Procesado por: CategoriaController::update() ──> Actualiza en base de datos mediante Categoria::update()
    -->
    <form action="<?= url('categorias/update?id=' . $categoria['id_categoria']) ?>" method="POST">
        
        <!-- Campo: Nombre de la Categoría con su valor actual precargado -->
        <div class="mb-3">
            <label for="nombre" class="form-label fw-semibold">Nombre de la Categoría *</label>
            <!-- CONEXIÓN DE DATO: Muestra el nombre actual protegido con sanitize() -->
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= sanitize($categoria['nombre']) ?>" required>
        </div>

        <!-- Campo: Descripción con su valor actual precargado -->
        <div class="mb-3">
            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
            <!-- CONEXIÓN DE DATO: Muestra la descripción actual protegida con sanitize() -->
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= sanitize($categoria['descripcion'] ?? '') ?></textarea>
        </div>

        <!-- Campo: Estado de la Categoría (Pre-selecciona el estado actual) -->
        <div class="mb-4">
            <label for="estado" class="form-label fw-semibold">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <!-- Marca 'selected' según el valor actual en la base de datos -->
                <option value="ACTIVO" <?= $categoria['estado'] === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
                <option value="INACTIVO" <?= $categoria['estado'] === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
        </div>

        <!-- Botones de Acción: Cancelar y Guardar Cambios -->
        <div class="text-end">
            <!-- CONEXIÓN: Botón cancelar regresa al listado 'categorias' -->
            <a href="<?= url('categorias') ?>" class="btn btn-secondary me-2">Cancelar</a>
            
            <!-- Botón Submit que envía la actualización -->
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check2 me-1"></i> Actualizar Categoría
            </button>
        </div>
    </form>
</div>

<!-- CONEXIÓN: Carga el pie de página del Layout principal -->
<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
