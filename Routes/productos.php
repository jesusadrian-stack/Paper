<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: productos.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP para el catálogo de productos,
 * categorías, historial de auditoría de precios y búsqueda instantánea para el POS.
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador / POS ──> Router (HTTP GET/POST) ──> ProductoController / CategoriaController / HistorialPrecioController ──> Modelos (Producto, Categoria, HistorialPrecio) / Vistas
 */

// Importa los controladores de productos, categorías e historial de precios.
// CONEXIÓN: Conecta con /Controllers/ProductoController.php, /CategoriaController.php e /HistorialPrecioController.php
require_once __DIR__ . '/../Controllers/ProductoController.php';
require_once __DIR__ . '/../Controllers/CategoriaController.php';
require_once __DIR__ . '/../Controllers/HistorialPrecioController.php';

// ============================================================================
// SECCIÓN 1: RUTAS DE PRODUCTOS
// ============================================================================

/**
 * RUTA 1: Listado General de Productos
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la tabla del inventario con código de barras, precios de compra/venta, margen y stock.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/productos
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: index()
 *   - Modelos Conectados: 'models/Producto.php' y 'models/Categoria.php'
 *   - Vista Conectada: Renderiza 'Views/Productos/index.php'
 */
Router::get('productos', ['ProductoController', 'index']);

/**
 * RUTA 2: Ficha Técnica y Detalle del Producto
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la información completa, código de barras, categoría y rentabilidad de un producto.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/productos/show?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: show()
 *   - Modelos Conectados: 'models/Producto.php' (findById($id)) e 'models/HistorialPrecio.php'
 *   - Vista Conectada: Renderiza 'Views/Productos/show.php'
 */
Router::get('productos/show', ['ProductoController', 'show']);

/**
 * RUTA 3: Formulario de Creación de Producto
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga la pantalla visual para registrar un nuevo artículo con su categoría, precio y stock inicial.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/productos/create
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: create()
 *   - Modelo Conectado: 'models/Categoria.php' (obtiene categorías activas para el <select>)
 *   - Vista Conectada: Renderiza 'Views/Productos/create.php'
 */
Router::get('productos/create', ['ProductoController', 'create']);

/**
 * RUTA 4: Guardar Nuevo Producto en la Base de Datos
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Valida datos (código único, precios positivos) y guarda el nuevo producto en la tabla 'productos'.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'productos/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: store()
 *   - Modelos Conectados:
 *     1. 'models/Producto.php' -> Inserta en la tabla 'productos'.
 *     2. 'models/HistorialPrecio.php' -> Registra el precio inicial en la tabla 'historial_precios'.
 *   - Redirección Conectada: Redirige a 'productos' con alerta de éxito.
 */
Router::post('productos/store', ['ProductoController', 'store']);

/**
 * RUTA 5: Formulario de Edición de Producto
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga el formulario con los datos actuales del producto para modificar su nombre, precio, etc.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/productos/edit?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: edit()
 *   - Modelos Conectados: 'models/Producto.php' y 'models/Categoria.php'
 *   - Vista Conectada: Renderiza 'Views/Productos/edit.php'
 */
Router::get('productos/edit', ['ProductoController', 'edit']);

/**
 * RUTA 6: Guardar Cambios del Producto
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Actualiza las propiedades del producto y, si el precio cambió, registra el historial.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'productos/update'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: update()
 *   - Modelos Conectados: 'models/Producto.php' y 'models/HistorialPrecio.php'
 *   - Redirección Conectada: Redirige a 'productos'.
 */
Router::post('productos/update', ['ProductoController', 'update']);

/**
 * RUTA 7: Alternar Estado Activo/Inactivo del Producto
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Habilita o deshabilita la venta de un producto sin eliminarlo del historial del sistema.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: Botón de estado apuntando a 'productos/toggle?id=1'
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: toggle()
 *   - Modelo Conectado: 'models/Producto.php' (método toggleEstado($id))
 *   - Redirección Conectada: Redirige a 'productos'.
 */
Router::get('productos/toggle', ['ProductoController', 'toggle']);

/**
 * RUTA 8: Endpoint JSON para Búsqueda Rápida en el POS
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Permite al buscador del POS o lector de código de barras consultar productos vía AJAX en tiempo real.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador / AJAX: http://localhost/Paper/productos/search-api?q=cuaderno
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ProductoController
 *   - Método Invocado: searchApi()
 *   - Frontend Conectado: Archivo 'Public/js/pos.js'
 *   - Modelo Conectado: 'models/Producto.php' (método search($term))
 *   - Respuesta: JSON con lista de productos (id, nombre, código, precio, stock).
 */
Router::get('productos/search-api', ['ProductoController', 'searchApi']);

// ============================================================================
// SECCIÓN 2: RUTAS DE CATEGORÍAS
// ============================================================================

/**
 * RUTA 9: Listado de Categorías
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la lista de familias/categorías de artículos (Papelería, Oficina, Escolar, etc.).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/categorias
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CategoriaController
 *   - Método Invocado: index()
 *   - Modelo Conectado: 'models/Categoria.php' (método allWithProductCount)
 *   - Vista Conectada: Renderiza 'Views/Categorias/index.php'
 */
Router::get('categorias', ['CategoriaController', 'index']);

/**
 * RUTA 10: Formulario de Creación de Categoría
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga la pantalla para agregar una nueva clasificación de productos.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/categorias/create
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CategoriaController
 *   - Método Invocado: create()
 *   - Vista Conectada: Renderiza 'Views/Categorias/create.php'
 */
Router::get('categorias/create', ['CategoriaController', 'create']);

/**
 * RUTA 11: Guardar Nueva Categoría
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Inserta la nueva categoría en la tabla 'categorias'.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'categorias/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: CategoriaController
 *   - Método Invocado: store()
 *   - Modelo Conectado: 'models/Categoria.php'
 *   - Redirección Conectada: Redirige a 'categorias'.
 */
Router::post('categorias/store', ['CategoriaController', 'store']);

/**
 * RUTA 12: Formulario de Edición de Categoría
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga el formulario para editar el nombre o descripción de una categoría (?id=X).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/categorias/edit?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CategoriaController
 *   - Método Invocado: edit()
 *   - Modelo Conectado: 'models/Categoria.php'
 *   - Vista Conectada: Renderiza 'Views/Categorias/edit.php'
 */
Router::get('categorias/edit', ['CategoriaController', 'edit']);

/**
 * RUTA 13: Guardar Cambios de la Categoría
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Actualiza el registro de la categoría modificada.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'categorias/update'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: CategoriaController
 *   - Método Invocado: update()
 *   - Modelo Conectado: 'models/Categoria.php'
 *   - Redirección Conectada: Redirige a 'categorias'.
 */
Router::post('categorias/update', ['CategoriaController', 'update']);

/**
 * RUTA 14: Alternar Estado de la Categoría
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Activa o desactiva una categoría completa.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: Botón de estado apuntando a 'categorias/toggle?id=1'
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: CategoriaController
 *   - Método Invocado: toggle()
 *   - Modelo Conectado: 'models/Categoria.php'
 *   - Redirección Conectada: Redirige a 'categorias'.
 */
Router::get('categorias/toggle', ['CategoriaController', 'toggle']);

// ============================================================================
// SECCIÓN 3: RUTAS DE HISTORIAL DE PRECIOS
// ============================================================================

/**
 * RUTA 15: Auditoría Histórica de Precios
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la línea de tiempo de cambios de precio de venta/compra y quién hizo cada ajuste.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/productos/historial-precios?producto_id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: HistorialPrecioController
 *   - Método Invocado: index()
 *   - Modelos Conectados: 'models/HistorialPrecio.php' y 'models/Producto.php'
 *   - Vista Conectada: Renderiza 'Views/Productos/historial_precios.php'
 */
Router::get('productos/historial-precios', ['HistorialPrecioController', 'index']);
