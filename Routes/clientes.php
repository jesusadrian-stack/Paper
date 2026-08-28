<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: clientes.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP relacionadas con la 
 * administración de clientes (CRUD, activación/desactivación y API de búsqueda rápida).
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador / AJAX ──> Router (HTTP GET/POST) ──> ClienteController ──> Modelo (Cliente) / Vistas (Views/Clientes)
 */

// Importa el controlador de clientes que contiene la lógica para gestionar clientes en la BD.
// CONEXIÓN: Conecta con el archivo físico: /Controllers/ClienteController.php
require_once __DIR__ . '/../Controllers/ClienteController.php';

/**
 * RUTA 1: Listado General de Clientes
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la tabla principal con todos los clientes registrados y sus datos de contacto.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/clientes
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: index()
 *   - Modelo Conectado: Invoca a 'models/Cliente.php' (método all / paginate)
 *   - Base de Datos: Consulta la tabla 'clientes'
 *   - Vista Conectada: Renderiza 'Views/Clientes/index.php'
 */
Router::get('clientes', ['ClienteController', 'index']);

/**
 * RUTA 2: Ver Perfil y Detalle del Cliente
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la información detallada de un cliente y su historial de compras o transacciones.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/clientes/show?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: show()
 *   - Modelo Conectado: Invoca a 'models/Cliente.php' (método findById($id)) y 'models/Venta.php'
 *   - Vista Conectada: Renderiza 'Views/Clientes/show.php'
 */
Router::get('clientes/show', ['ClienteController', 'show']);

/**
 * RUTA 3: Mostrar Formulario de Creación de Cliente
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga la pantalla visual con el formulario para registrar un nuevo cliente.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/clientes/create
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: create()
 *   - Vista Conectada: Renderiza 'Views/Clientes/create.php'
 */
Router::get('clientes/create', ['ClienteController', 'create']);

/**
 * RUTA 4: Guardar Nuevo Cliente en la Base de Datos
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Recibe los datos enviados desde el formulario de creación, los valida y los inserta en la BD.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'clientes/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: store()
 *   - Modelo Conectado: Invoca a 'models/Cliente.php' (método create($data))
 *   - Base de Datos: Inserta un nuevo registro en la tabla 'clientes'
 *   - Redirección Conectada: Redirige a 'clientes' con mensaje de éxito.
 */
Router::post('clientes/store', ['ClienteController', 'store']);

/**
 * RUTA 5: Mostrar Formulario de Edición de Cliente
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga el formulario con los datos precargados de un cliente específico para modificarlos.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/clientes/edit?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: edit()
 *   - Modelo Conectado: Invoca a 'models/Cliente.php' (método findById($id))
 *   - Vista Conectada: Renderiza 'Views/Clientes/edit.php' con los datos del cliente
 */
Router::get('clientes/edit', ['ClienteController', 'edit']);

/**
 * RUTA 6: Procesar y Guardar Cambios del Cliente
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Recibe los datos modificados del formulario de edición y actualiza el registro en la BD.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'clientes/update'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: update()
 *   - Modelo Conectado: Invoca a 'models/Cliente.php' (método update($id, $data))
 *   - Base de Datos: Modifica el registro en la tabla 'clientes'
 *   - Redirección Conectada: Redirige a 'clientes' con mensaje de confirmación.
 */
Router::post('clientes/update', ['ClienteController', 'update']);

/**
 * RUTA 7: Cambiar Estado Activo / Inactivo del Cliente
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Alterna el estado del cliente (activar/desactivar) sin borrar sus registros históricos.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: Botón de estado apuntando a 'clientes/toggle?id=1'
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: toggle()
 *   - Modelo Conectado: Invoca a 'models/Cliente.php' (método toggleEstado($id))
 *   - Base de Datos: Actualiza la columna 'estado' (1 o 0) en la tabla 'clientes'
 *   - Redirección Conectada: Redirige a la vista 'clientes'.
 */
Router::get('clientes/toggle', ['ClienteController', 'toggle']);

/**
 * RUTA 8: Endpoint API JSON para Búsqueda Rápida de Clientes
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Permite al frontend buscar clientes en tiempo real por nombre o documento sin recargar la página.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador / AJAX: http://localhost/Paper/clientes/search-api?q=juan
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: ClienteController
 *   - Método Invocado: searchApi()
 *   - Frontend Conectado: Conecta con 'Public/js/pos.js' (buscador de clientes en el POS) y módulo corresponsal
 *   - Modelo Conectado: Invoca a 'models/Cliente.php' (método search($query))
 *   - Salida / Respuesta: Devuelve un JSON estructurado con el array de clientes encontrados.
 */
Router::get('clientes/search-api', ['ClienteController', 'searchApi']);
