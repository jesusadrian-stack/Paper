<?php
/**
 * ============================================================================
 * VISTA: Views/Categorias/index.php (Listado General de Categorías)
 * ============================================================================
 * 
 * ¿PARA QUÉ SIRVE ESTA VISTA?:
 * Muestra la tabla principal con todas las categorías de productos de la papelería,
 * detallando su nombre, descripción, cantidad de productos asociados, estado 
 * (ACTIVO/INACTIVO) y botones de acción rápida para editar o alternar su estado.
 * 
 * MAPA DE CONEXIONES DE ESTA VISTA:
 * 1. CONTROLADOR ORIGEN:
 *    - Invocada por: CategoriaController::index() en /Controllers/CategoriaController.php
 *    - Ruta HTTP que la dispara: GET http://localhost/Paper/categorias (registrada en Routes/productos.php)
 * 2. VARIABLES PHP RECIBIDAS DEL CONTROLADOR:
 *    - `$categorias`: Array asociativo obtenido desde el modelo Categoria::allWithProductCount()
 *      Contiene: id_categoria, nombre, descripcion, estado y el conteo total_productos.
 * 3. LAYOUTS Y COMPONENTES INCLUIDOS:
 *    - Incluye el encabezado: Views/Layouts/header.php (Sidebar, Navbar, Bootstrap, CSS principal y alertas).
 *    - Incluye el pie de página: Views/Layouts/footer.php (Scripts JS y cierre de estructura HTML).
 * 4. ENLACES Y ACCIONES CONECTADAS:
 *    - Botón "Nueva Categoría" ──> url('categorias/create') ──> CategoriaController::create()
 *    - Botón "Editar" ──────────> url('categorias/edit?id=X') ──> CategoriaController::edit()
 *    - Botón "Cambiar Estado" ──> url('categorias/toggle?id=X') ──> CategoriaController::toggle()
 * 5. FRONTEND & SCRIPTS:
 *    - Input de filtro en tiempo real: 'table-search-input' conectado con el buscador dinámico JS en Public/js/main.js
 * ============================================================================
 */

// Define el título de la página que se inyecta en el <head> y en la barra superior del Layout
$pageTitle = 'Categorías de Productos';

// CONEXIÓN: Carga la plantilla superior común (Navbar, Sidebar, librerías CSS y contenedor principal)
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<!-- Tarjeta principal que envuelve la interfaz del módulo de categorías -->
<div class="card shadow-sm border-0 rounded-4 p-4">
    
    <!-- Encabezado de la vista con título y botón de acción para crear nueva categoría -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1">Categorías de Artículos</h5>
            <p class="text-muted small mb-0">Organice y clasifique el catálogo de productos de la papelería.</p>
        </div>
        
        <!-- CONEXIÓN: Botón que redirige a la ruta GET 'categorias/create' -> CategoriaController::create() -->
        <a href="<?= url('categorias/create') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-tag-fill me-1"></i> Nueva Categoría
        </a>
    </div>

    <!-- Campo de búsqueda instantánea para filtrar filas de la tabla mediante JS -->
    <div class="mb-3">
        <input type="text" class="form-control table-search-input" data-table="tabla-categorias" placeholder="Buscar categoría...">
    </div>

    <!-- Tabla responsiva con el listado de categorías registradas -->
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="tabla-categorias">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Total Productos</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- 
                    CONEXIÓN DE DATOS:
                    Itera sobre el array asociativo `$categorias` inyectado por CategoriaController::index()
                -->
                <?php foreach ($categorias as $c): ?>
                    <tr>
                        <!-- Columna: Identificador único de la categoría -->
                        <td>#<?= $c['id_categoria'] ?></td>

                        <!-- Columna: Nombre de la categoría protegido contra inyección XSS con sanitize() -->
                        <td class="fw-bold text-dark"><?= sanitize($c['nombre']) ?></td>

                        <!-- Columna: Descripción o guión '-' si es nula -->
                        <td><small class="text-muted"><?= sanitize($c['descripcion'] ?? '-') ?></small></td>

                        <!-- Columna: Cantidad de productos asociados calculada en la BD mediante COUNT() -->
                        <td><span class="badge bg-light text-dark border"><?= $c['total_productos'] ?? 0 ?> productos</span></td>

                        <!-- Columna: Estado visual (Badge Verde si está ACTIVO, Badge Gris/Rojo si está INACTIVO) -->
                        <td>
                            <span class="badge-custom <?= $c['estado'] === 'ACTIVO' ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $c['estado'] ?>
                            </span>
                        </td>

                        <!-- Columna: Botones de Acción (Editar y Alternar Estado) -->
                        <td class="text-end">
                            <!-- 
                                CONEXIÓN DE EDICIÓN:
                                Redirige a Routes/productos.php (GET 'categorias/edit') pasando el id por query string
                                ──> Despacha a CategoriaController::edit() ──> Carga Views/Categorias/edit.php
                            -->
                            <a href="<?= url('categorias/edit?id=' . $c['id_categoria']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <!-- 
                                CONEXIÓN DE CAMBIO DE ESTADO:
                                Redirige a Routes/productos.php (GET 'categorias/toggle') pasando el id
                                ──> Despacha a CategoriaController::toggle() ──> Actualiza la BD en el modelo Categoria::toggleEstado()
                            -->
                            <a href="<?= url('categorias/toggle?id=' . $c['id_categoria']) ?>" class="btn btn-sm btn-outline-<?= $c['estado'] === 'ACTIVO' ? 'danger' : 'success' ?> py-0 px-2 btn-confirm" data-confirm="¿Está seguro de cambiar el estado de esta categoría?" title="<?= $c['estado'] === 'ACTIVO' ? 'Desactivar' : 'Activar' ?>">
                                <i class="bi bi-power"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- CONEXIÓN: Carga el pie de página común (cierre de etiquetas HTML y carga de scripts JavaScript) -->
<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
