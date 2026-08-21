# 📦 Sistema Web Completo de Gestión de Inventario, Ventas y Corresponsal para Papelería con Inteligencia Artificial

Sistema web profesional desarrollado bajo la arquitectura **Modelo-Vista-Controlador (MVC)** en **PHP 8+** conectado a **MySQL/MariaDB en Laragon**, con interfaz moderna en **Bootstrap 5**, punto de venta (POS) interactivo en tiempo real, control de kárdex de inventario, conciliación financiera, módulo de corresponsal bancario y soporte de diagnósticos con **Inteligencia Artificial**.

---

## 🚀 Tecnologías Utilizadas

- **Backend:** PHP 8.1+ (Arquitectura MVC pura con Enrutador Modular, Controladores, Modelos PDO y Servicios de Negocio).
- **Base de Datos:** MySQL 8.0 / MariaDB administrado localmente mediante **Laragon** (`InnoDB`, `utf8mb4_unicode_ci`, transacciones ACID y claves foráneas).
- **Frontend:** HTML5, CSS3, JavaScript Vanilla (ES6+), Bootstrap 5.3, Bootstrap Icons y Chart.js.
- **Seguridad:** Hash seguro de contraseñas con `password_hash()` (Bcrypt), Prepared Statements con PDO, sanitización de datos y Middlewares de autenticación y autorización por roles.
- **Inteligencia Artificial:** Módulo desacoplado (`LocalAIProvider` / `RemoteAIProvider`) con soporte nativo de análisis predictivo de rotación de inventarios y conector configurable para API REST (Gemini / OpenAI).

---

## 📋 Requisitos del Sistema

1. **Laragon** (Recomendado versión 5 o 6 con PHP 8.1+ y MySQL 8 / MariaDB).
2. Extensiones PHP requeridas (activas por defecto en Laragon):
   - `pdo_mysql`
   - `mbstring`
   - `curl`
   - `openssl`
   - `json`
3. Navegador Web moderno (Chrome, Edge, Firefox, Brave).

---

## 🛠️ Instalación y Puesta en Marcha en Laragon

### 1. Ubicación del Proyecto
Coloca la carpeta del proyecto en el directorio `www` de tu instalación de Laragon:
```text
C:\laragon\www\Paper\
```

### 2. Iniciar Servicios en Laragon
1. Abre el panel de control de **Laragon**.
2. Haz clic en el botón **"Start All"** (Iniciar Todo) para levantar los servicios de Apache y MySQL.

### 3. Configuración de Variables de Entorno (`.env`)
El archivo `.env` ya se encuentra configurado en la raíz del proyecto. Si necesitas adaptarlo a credenciales personalizadas:
```ini
APP_NAME="Papelería y Corresponsal"
APP_ENV=local
APP_URL=http://localhost:8080/Paper/public
APP_TIMEZONE=America/Bogota

# Conexión Base de Datos MySQL (Laragon)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=papeleria_corresponsal
DB_USERNAME=root
DB_PASSWORD=

# Configuración de Inteligencia Artificial (local o remote)
AI_PROVIDER=local
AI_API_KEY=
AI_MODEL=gemini-1.5-flash
```

### 4. Creación e Importación de la Base de Datos
Puedes importar la base de datos usando **HeidiSQL** (incluido en Laragon), **phpMyAdmin** o la terminal de Laragon:

#### Opción A: Desde la Terminal de Laragon / CMD:
```bash
mysql -u root -e "source C:/laragon/www/Paper/Database/schema.sql; source C:/laragon/www/Paper/Database/seed.sql;"
```

#### Opción B: Desde HeidiSQL:
1. Abre HeidiSQL desde el menú de Laragon.
2. Abre y ejecuta el archivo `Database/schema.sql`.
3. Abre y ejecuta el archivo `Database/seed.sql`.

---

## 🔑 Credenciales de Acceso Iniciales

| Rol | Usuario / Correo | Contraseña | Perfil y Acceso |
| :--- | :--- | :--- | :--- |
| **ADMINISTRADOR** | `admin@papeleria.com` *(o `admin`)* | `12345` | Control total del sistema, finanzas, inventarios, usuarios, reportes e IA. |
| **TRABAJADOR** | `trabajador@papeleria.com` *(o `trabajador`)* | `trabajador123` | Operador de ventas (POS), depósitos/retiros de corresponsal y consulta de stock. |

---

## 🌐 URLs de Acceso Local

Dependiendo de tu configuración en Laragon, puedes acceder a través de:

- **Ruta Directa Apache:** [http://localhost:8080/Paper/public/](http://localhost:8080/Paper/public/) o [http://localhost/Paper/public/](http://localhost/Paper/public/)
- **Virtual Host de Laragon (si está activo):** [http://paper.test/](http://paper.test/)

---

## 📂 Estructura del Proyecto (MVC)

```text
c:/laragon/www/Paper/
├── AI/                     # Interfaces y fábrica del motor de Inteligencia Artificial
│   ├── AIProvider.php
│   └── AIService.php
├── Assets/                 # Recursos y estilos globales
│   ├── css/
│   └── js/
├── Config/                 # Configuración de base de datos PDO y variables globales
│   ├── config.php
│   └── database.php
├── Controllers/            # Controladores de la aplicación
│   ├── AlertaInventarioController.php
│   ├── AnalisisIAController.php
│   ├── AuthController.php
│   ├── CategoriaController.php
│   ├── ClienteController.php
│   ├── CorresponsalController.php
│   ├── CuentaController.php
│   ├── DashboardController.php
│   ├── HistorialPrecioController.php
│   ├── InventarioController.php
│   ├── MovimientoCuentaController.php
│   ├── ProductoController.php
│   ├── RecomendacionIAController.php
│   ├── ReporteController.php
│   ├── RolController.php
│   ├── TransferenciaController.php
│   ├── UsuarioController.php
│   └── VentaController.php
├── Database/               # Esquema DDL y datos iniciales (Seed)
│   ├── schema.sql
│   └── seed.sql
├── Middleware/             # Control de autenticación y autorización por roles
│   ├── AuthMiddleware.php
│   └── RoleMiddleware.php
├── Models/                 # Capa de datos y modelos relacionales (16 tablas)
│   ├── AlertaInventario.php
│   ├── AnalisisIA.php
│   ├── Categoria.php
│   ├── Cliente.php
│   ├── Cuenta.php
│   ├── DetalleVenta.php
│   ├── HistorialPrecio.php
│   ├── MovimientoCuenta.php
│   ├── MovimientoInventario.php
│   ├── OperacionCorresponsal.php
│   ├── Producto.php
│   ├── RecomendacionIA.php
│   ├── Rol.php
│   ├── Transferencia.php
│   ├── Usuario.php
│   └── Venta.php
├── Public/                 # Punto de entrada público y assets web
│   ├── css/style.css
│   ├── js/app.js
│   ├── js/pos.js
│   ├── .htaccess
│   └── index.php
├── Routes/                 # Enrutador modular de la aplicación
│   ├── auth.php
│   ├── clientes.php
│   ├── corresponsal.php
│   ├── cuentas.php
│   ├── ia.php
│   ├── inventario.php
│   ├── productos.php
│   ├── reportes.php
│   ├── transferencias.php
│   ├── usuarios.php
│   ├── ventas.php
│   └── web.php
├── Services/               # Servicios de lógica de negocio transaccional (ACID)
│   ├── AIService.php
│   ├── AuthService.php
│   ├── CorresponsalService.php
│   ├── CuentaService.php
│   ├── InventarioService.php
│   ├── ReporteService.php
│   ├── TransferenciaService.php
│   └── VentaService.php
├── Views/                  # Vistas organizadas por módulo
│   ├── Auth/
│   ├── Categorias/
│   ├── Clientes/
│   ├── Components/
│   ├── Corresponsal/
│   ├── Cuentas/
│   ├── Dashboard/
│   ├── IA/
│   ├── Inventario/
│   ├── Layouts/
│   ├── Productos/
│   ├── Reportes/
│   ├── Roles/
│   ├── Transferencias/
│   ├── Usuarios/
│   └── Ventas/
├── .env
├── .env.example
├── .htaccess
├── index.php
├── README.md
└── test_system.php         # Suite de pruebas automatizadas
```

---

## 💡 Módulos Principales y Reglas de Negocio

1. **Punto de Venta (POS):**
   - Carrito dinámico, buscador por nombre o código de barras, cálculo de cambio en tiempo real y generación de tickets imprimibles en formato térmico (80mm).
   - Ejecución transaccional: descuenta stock físico, genera movimiento en kárdex, dispara alertas automáticas si el stock es menor o igual al mínimo, y registra el ingreso contable en la cuenta **PAPELERIA**.

2. **Control de Inventario y Kárdex:**
   - Registro de entradas, salidas y ajustes físicos de inventario.
   - Auditoría histórica de cambios de precios (`historial_precio`).
   - Alertas automáticas ante niveles críticos de existencias.

3. **Módulo de Corresponsal Bancario:**
   - Registro de depósitos (recaudo de efectivo) y retiros de dinero.
   - Validación estricta: No permite retiros si el saldo de la cuenta corresponsal es insuficiente.
   - Registro simultáneo de `operacion_corresponsal` y `movimiento_cuenta`.

4. **Finanzas y Transferencias:**
   - Separación contable entre cuenta **PAPELERIA** y **CORRESPONSAL**.
   - Transferencias entre cuentas con doble asiento contable e historial inmutable.

5. **Inteligencia Artificial (IA):**
   - Diagnósticos predictivos de reabastecimiento para artículos de alta rotación.
   - Detección de tendencias comerciales y sugerencias de venta cruzada.
   - Almacenamiento persistente en base de datos (`analisis_ia` y `recomendacion_ia`) con posibilidad de marcar recomendaciones como atendidas.
   - Conexión con modelos LLM remotos (Gemini / OpenAI) mediante configuración en `.env`.

---

## 🧪 Ejecución de Pruebas Automatizadas

El sistema incluye una suite de pruebas para validar la integridad de la base de datos y la lógica de negocio. Para ejecutarla:

```bash
php test_system.php
```

Resultado verificado:
```text
====================================================
INICIANDO BATERÍA DE PRUEBAS DEL SISTEMA PAPELERÍA
====================================================
 [OK] Conexión PDO a MySQL (Laragon)
 [OK] Autenticación de Administrador (admin/admin123)
 [OK] Autenticación de Trabajador (trabajador/trabajador123)
 [OK] Rechazo de credenciales incorrectas
 [OK] Búsqueda de producto por código 'ESC001'
 [OK] Registro de entrada de inventario (+10)
 [OK] Verificación de nuevo stock físico en base de datos
 [OK] Procesamiento de Venta transaccional
 [OK] Impacto contable automático en Caja Papelería
 [OK] Operación de Depósito Corresponsal ($50.000)
 [OK] Operación de Retiro Corresponsal ($20.000)
 [OK] Validación estricta de saldo insuficiente en Corresponsal
 [OK] Transferencia exitosa Papelería -> Corresponsal ($25.000)
 [OK] Validación: No permitir transferencia a la misma cuenta
 [OK] Generación de Diagnóstico Predictivo con IA
 [OK] Generación de Análisis de Tendencias de Ventas con IA
 [OK] Generación de Reporte de Ventas Consolidadas
 [OK] Generación de Reporte de Valoración de Inventario

====================================================
RESULTADOS DE PRUEBAS: 18 PASADAS / 0 FALLIDAS
====================================================
¡TODAS LAS PRUEBAS COMPLETADAS CON ÉXITO AL 100%!
```
