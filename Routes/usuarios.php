<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: usuarios.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP para la administración 
 * de usuarios del sistema (Administradores, Trabajadores) y consulta de roles.
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador ──> Router (HTTP GET/POST) ──> UsuarioController / RolController ──> Modelos (Usuario, Rol) / Vistas (Views/Usuarios, Views/Roles)
 */

// Importa los controladores de usuarios y roles.
// CONEXIÓN: Conecta con /Controllers/UsuarioController.php y /Controllers/RolController.php
require_once __DIR__ . '/../Controllers/UsuarioController.php';
require_once __DIR__ . '/../Controllers/RolController.php';

// ============================================================================
// SECCIÓN 1: RUTAS DE USUARIOS
// ============================================================================

/**
 * RUTA 1: Listado General de Usuarios
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la tabla de usuarios registrados, roles asignados, correo y estado (Activo/Inactivo).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/usuarios
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: UsuarioController
 *   - Método Invocado: index()
 *   - Modelos Conectados: 'models/Usuario.php' y 'models/Rol.php'
 *   - Vista Conectada: Renderiza 'Views/Usuarios/index.php'
 */
Router::get('usuarios', ['UsuarioController', 'index']);

/**
 * RUTA 2: Formulario de Creación de Usuario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga la pantalla para registrar un nuevo empleado o administrador en la plataforma.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/usuarios/create
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: UsuarioController
 *   - Método Invocado: create()
 *   - Modelo Conectado: 'models/Rol.php' (para llenar el desplegable de roles)
 *   - Vista Conectada: Renderiza 'Views/Usuarios/create.php'
 */
Router::get('usuarios/create', ['UsuarioController', 'create']);

/**
 * RUTA 3: Guardar Nuevo Usuario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Encripta la contraseña de acceso (bcrypt/Argon2) y registra al usuario en la BD.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'usuarios/store'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: UsuarioController
 *   - Método Invocado: store()
 *   - Modelo Conectado: 'models/Usuario.php' (método create($data))
 *   - Base de Datos: Inserta el registro en la tabla 'usuarios'.
 *   - Redirección Conectada: Redirige a 'usuarios' con mensaje de confirmación.
 */
Router::post('usuarios/store', ['UsuarioController', 'store']);

/**
 * RUTA 4: Formulario de Edición de Usuario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Carga el formulario para editar datos, cambiar rol o actualizar contraseña de un usuario (?id=X).
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/usuarios/edit?id=1
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: UsuarioController
 *   - Método Invocado: edit()
 *   - Modelos Conectados: 'models/Usuario.php' (findById($id)) y 'models/Rol.php'
 *   - Vista Conectada: Renderiza 'Views/Usuarios/edit.php'
 */
Router::get('usuarios/edit', ['UsuarioController', 'edit']);

/**
 * RUTA 5: Guardar Modificaciones del Usuario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Aplica los cambios en la BD y actualiza contraseña solo si fue suministrada una nueva.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario apuntando a 'usuarios/update'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: UsuarioController
 *   - Método Invocado: update()
 *   - Modelo Conectado: 'models/Usuario.php' (método update($id, $data))
 *   - Redirección Conectada: Redirige a 'usuarios'.
 */
Router::post('usuarios/update', ['UsuarioController', 'update']);

/**
 * RUTA 6: Alternar Estado del Usuario (Activar / Inactivar)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Bloquea o reactiva el acceso de un usuario al sistema sin borrar su historial de ventas.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: Botón de estado apuntando a 'usuarios/toggle?id=1'
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: UsuarioController
 *   - Método Invocado: toggle()
 *   - Modelo Conectado: 'models/Usuario.php' (método toggleEstado($id))
 *   - Redirección Conectada: Redirige a 'usuarios'.
 */
Router::get('usuarios/toggle', ['UsuarioController', 'toggle']);

// ============================================================================
// SECCIÓN 2: RUTAS DE ROLES
// ============================================================================

/**
 * RUTA 7: Listado de Roles del Sistema
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra los roles configurados (Administrador, Trabajador) y sus permisos asociados.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/roles
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: RolController
 *   - Método Invocado: index()
 *   - Modelo Conectado: 'models/Rol.php' (método allWithUserCount)
 *   - Vista Conectada: Renderiza 'Views/Roles/index.php'
 */
Router::get('roles', ['RolController', 'index']);
