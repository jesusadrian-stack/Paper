<?php
/**
 * ============================================================================
 * ARCHIVO DE RUTAS: auth.php
 * ============================================================================
 * 
 * PROPÓSITO:
 * Centralizar y conectar todas las solicitudes HTTP relacionadas con la 
 * autenticación de usuarios (inicio de sesión, registro y cierre de sesión).
 * 
 * MAPA DE CONEXIÓN GENERAL:
 * Navegador (URL) ──> Router (HTTP GET/POST) ──> AuthController ──> Modelos (Usuario, Rol) / Vistas (Views/Auth)
 */

// Importa el controlador de autenticación donde se encuentra la lógica de negocio y sesiones.
// CONEXIÓN: Este archivo se conecta con el archivo físico: /Controllers/AuthController.php
require_once __DIR__ . '/../Controllers/AuthController.php';

/**
 * RUTA 1: Mostrar Formulario de Inicio de Sesión
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la pantalla visual donde el usuario ingresa su usuario/email y contraseña.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/auth/login (o ?r=auth/login)
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: AuthController (Clase)
 *   - Método Invocado: showLogin()
 *   - Vista Conectada: Renderiza el archivo 'Views/Auth/login.php'
 *   - Modelo / Base de Datos: Ninguno (solo muestra la interfaz HTML).
 */
Router::get('auth/login', ['AuthController', 'showLogin']);

/**
 * RUTA 2: Procesar Credenciales de Inicio de Sesión
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Recibe los datos enviados desde el formulario de login, valida las credenciales y crea la sesión.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario de login apuntando a 'auth/login'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: AuthController (Clase)
 *   - Método Invocado: login()
 *   - Modelo Conectado: Invoca a 'models/Usuario.php' (método findByEmailOrUsername)
 *   - Base de Datos: Consulta la tabla 'usuarios' para verificar contraseña cifrada (password_verify) y estado activo.
 *   - Redirección Conectada: Si es correcto redirige a 'dashboard', si falla redirige a 'auth/login' con mensaje flash de error.
 */
Router::post('auth/login', ['AuthController', 'login']);

/**
 * RUTA 3: Mostrar Formulario de Registro de Usuarios
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Muestra la pantalla visual para registrar una nueva cuenta de usuario en el sistema.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: http://localhost/Paper/auth/register (o ?r=auth/register)
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: AuthController (Clase)
 *   - Método Invocado: showRegister()
 *   - Vista Conectada: Renderiza el archivo 'Views/Auth/register.php'
 *   - Modelo Conectado: Invoca a 'models/Rol.php' para listar los roles disponibles en el selector.
 */
Router::get('auth/register', ['AuthController', 'showRegister']);

/**
 * RUTA 4: Procesar Registro de Usuario
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Recibe los datos del formulario de registro, valida que no se duplique el correo/usuario y crea el registro.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: POST
 *   - URL en Navegador: Formulario de registro apuntando a 'auth/register'
 *   - Enrutador: Router::post(...)
 *   - Controlador Destino: AuthController (Clase)
 *   - Método Invocado: register()
 *   - Modelo Conectado: Invoca a 'models/Usuario.php' (método create con password_hash)
 *   - Base de Datos: Inserta un nuevo registro en la tabla 'usuarios'.
 *   - Redirección Conectada: Redirige a 'auth/login' con alerta de éxito para que inicie sesión.
 */
Router::post('auth/register', ['AuthController', 'register']);

/**
 * RUTA 5: Cerrar Sesión (Logout)
 * ----------------------------------------------------------------------------
 * - ¿PARA QUÉ SIRVE?: Destruye la sesión activa del usuario, borra cookies de sesión y limpia variables en memoria.
 * - ¿QUÉ CONECTA CON QUÉ?:
 *   - Petición HTTP: GET
 *   - URL en Navegador: Enlace del sidebar/navbar apuntando a 'auth/logout'
 *   - Enrutador: Router::get(...)
 *   - Controlador Destino: AuthController (Clase)
 *   - Método Invocado: logout()
 *   - Lógica Interna: Ejecuta session_unset(), session_destroy() y setFlash().
 *   - Redirección Conectada: Redirige al usuario a la pantalla de 'auth/login'.
 */
Router::get('auth/logout', ['AuthController', 'logout']);
