# DOCUMENTO DE PROPUESTA TÉCNICA Y ESPECIFICACIÓN DE CASOS DE USO

---

<br><br><br>

# PROPUESTA DE ESPECIFICACIÓN DE CASOS DE USO
## SISTEMA INTEGRAL DE GESTIÓN DE INVENTARIO, PUNTO DE VENTA (POS), CORRESPONSAL BANCARIO Y ASISTENCIA CON INTELIGENCIA ARTIFICIAL ("PAPER")

<br><br><br>

**ELABORADO POR:**  
Equipo de Desarrollo de Software y Consultoría Tecnológica  

**LÍDER DE PROYECTO:**  
Ing. Jesús Adrián  

**PRESENTADO A:**  
Comité Directivo y Gerencia General de Papelería & Servicios  

**ORGANIZACIÓN:**  
Papelería & Corresponsal  

**FECHA DE PRESENTACIÓN:**  
28 de Agosto de 2026  

**CIUDAD:**  
Bogotá D.C. / Local  

<br><br><br>

---
*(Salto de página / Page Break)*
---

# TABLA DE CONTENIDO

1. **CONTROL DEL DOCUMENTO** ............................................................................ Pág. 3  
   1.1 Historial de Versiones .................................................................................. Pág. 3  
   1.2 Información General del Proyecto ................................................................ Pág. 3  
   1.3 Registro de Aprobaciones y Firmas ............................................................... Pág. 4  

2. **PROPUESTA Y RESUMEN EJECUTIVO** ......................................................... Pág. 5  
   2.1 Justificación y Objetivos de la Propuesta ..................................................... Pág. 5  
   2.2 Alcance del Sistema y Áreas Organizacionales Involucradas ....................... Pág. 6  
   2.3 Módulos del Sistema Propuesto ................................................................... Pág. 6  

3. **MODELADO Y DIAGRAMA DE CASOS DE USO (UML)** ................................. Pág. 7  
   3.1 Estructura General del Modelo .................................................................... Pág. 7  
   3.2 Matriz y Diagrama de Relaciones ................................................................ Pág. 8  

4. **DESCRIPCIÓN DETALLADA DE ACTORES** .................................................... Pág. 9  
   4.1 ACT-01: Administrador del Sistema .............................................................. Pág. 9  
   4.2 ACT-02: Trabajador / Operador POS ............................................................ Pág. 10  
   4.3 ACT-03: Cliente ........................................................................................... Pág. 11  
   4.4 ACT-04: Motor de Inteligencia Artificial (IA) ................................................. Pág. 12  

5. **ESPECIFICACIÓN FORMAL DE CASOS DE USO** ........................................... Pág. 13  
   5.1 Módulo 1: Seguridad y Acceso  
       - CU-001: Autenticación e Inicio de Sesión ................................................... Pág. 13  
       - CU-002: Gestión Integral de Usuarios y Permisos ....................................... Pág. 15  
   5.2 Módulo 2: Catálogo y Control de Inventarios  
       - CU-003: Gestión de Categorías de Productos .............................................. Pág. 17  
       - CU-004: Gestión de Catálogo y Precios de Productos ................................. Pág. 19  
       - CU-005: Control de Movimientos de Inventario (Kárdex) ............................ Pág. 21  
       - CU-006: Monitoreo y Gestión de Alertas de Stock Mínimo .......................... Pág. 23  
   5.3 Módulo 3: Clientes y Facturación en Punto de Venta (POS)  
       - CU-007: Gestión y Registro de Clientes ...................................................... Pág. 25  
       - CU-008: Procesamiento de Venta y Facturación POS .................................... Pág. 27  
       - CU-009: Consulta, Arqueo y Anulación de Ventas ........................................ Pág. 29  
   5.4 Módulo 4: Operaciones de Corresponsal Bancario y Cuentas  
       - CU-010: Registro de Depósitos y Retiros de Corresponsal ........................... Pág. 31  
       - CU-011: Gestión de Cuentas y Transferencias Financieras .......................... Pág. 33  
   5.5 Módulo 5: Analítica Gerencial e Inteligencia Artificial  
       - CU-012: Visualización de Tablero de Control (Dashboard) y Reportes .......... Pág. 35  
       - CU-013: Diagnóstico Predictivo y Recomendaciones con IA ........................ Pág. 37  

---
*(Salto de página / Page Break)*
---

# 1. CONTROL DEL DOCUMENTO

### 1.1 Historial de Versiones

| Fecha | Versión | Autor(es) | Organización | Descripción del Cambio |
| :---: | :---: | :---: | :---: | :--- |
| 28/08/2026 | 1.0.0 | Ing. Jesús Adrián & Equipo de Desarrollo | Papelería & Servicios | Elaboración inicial de la propuesta técnica y especificación formal de casos de uso para el sistema Paper. |

---

### 1.2 Información General del Proyecto

| Parámetro | Detalle Institucional |
| :--- | :--- |
| **Empresa / Organización** | Papelería y Corresponsal Bancario |
| **Nombre del Proyecto** | Sistema Integral de Gestión de Inventario, POS, Corresponsal e IA ("Paper") |
| **Fecha de Preparación** | 28 de Agosto de 2026 |
| **Cliente / Beneficiario** | Área de Administración, Ventas y Operaciones Financieras |
| **Patrocinador Principal** | Gerencia General |
| **Gerente / Líder de Proyecto** | Ing. Jesús Adrián |
| **Líder de Desarrollo de Software** | Equipo de Ingeniería y Arquitectura MVC |

---

### 1.3 Registro de Aprobaciones y Firmas

| Nombre y Apellido | Cargo | Departamento u Organización | Fecha | Firma |
| :--- | :--- | :--- | :---: | :---: |
| **Ing. Jesús Adrián** | Líder de Proyecto | Dirección de Tecnología | 28/08/2026 | __________________ |
| **Administrador General** | Gerente Operativo | Operaciones y Finanzas | 28/08/2026 | __________________ |
| **Coordinador de Punto** | Supervisor de Ventas | Atención al Cliente / POS | 28/08/2026 | __________________ |

---
*(Salto de página / Page Break)*
---

# 2. PROPUESTA Y RESUMEN EJECUTIVO

### 2.1 Justificación y Objetivos de la Propuesta
La presente propuesta técnica tiene como objetivo formalizar el diseño funcional y lógico del software **"Paper"**, una solución tecnológica concebida para solventar las necesidades críticas de control operativo, facturación rápida en mostrador, conciliación de caja en corresponsalía bancaria y toma de decisiones comerciales apoyada en algoritmos de Inteligencia Artificial.

Actualmente, los establecimientos comerciales mixtos (papelería y corresponsal bancario) enfrentan desajustes contables debido a la mezcla de dinero en efectivo proveniente de ventas mercantiles y operaciones financieras (depósitos y retiros de terceros). La arquitectura propuesta asegura la total segregación de saldos en tiempo real, garantizando trazabilidad y auditoría completa.

### 2.2 Alcance del Sistema y Áreas Organizacionales Involucradas
El sistema cubrirá de extremo a extremo las siguientes áreas de la organización:
- **Área de Mostrador y Ventas:** Atención ágil al cliente, cobro POS, emisión de comprobantes y validación instantánea de existencias.
- **Área de Almacén e Inventarios:** Control de stock físico, kárdex permanente de entradas, salidas y mermas, alertas de stock mínimo y registro histórico de variaciones de precios.
- **Área Financiera y de Corresponsalía:** Gestión de transacciones de corresponsal (depósitos/retiros), monitoreo de saldos segregados y transferencias controladas entre cuentas.
- **Área Gerencial y de Dirección Estratégica:** Indicadores clave de rendimiento (KPIs), reportes exportables y diagnósticos predictivos mediante Inteligencia Artificial (IA) para optimizar compras y evitar desabastecimiento.

### 2.3 Módulos del Sistema Propuesto
1. **Módulo de Seguridad y Accesos (AUTH & USERS):** Control de acceso por roles (ADMINISTRADOR y TRABAJADOR), cifrado de contraseñas Bcrypt y registro de accesos.
2. **Módulo de Catálogo e Inventario (INVENTORY):** Gestión de productos, categorías, kárdex y alertas tempranas.
3. **Módulo de Clientes (CLIENTS):** Directorio centralizado de clientes frecuentes con soporte para diversos tipos de documento (CC, NIT, CE, TI).
4. **Módulo de Punto de Venta (POS):** Facturación ultrarrápida con validación atómica de existencias.
5. **Módulo de Corresponsal Bancario y Cuentas (BANKING & ACCOUNTS):** Arqueo y conciliación independiente de fondos de papelería frente a fondos bancarios.
6. **Módulo de Reportes y Tableros (DASHBOARD & REPORTS):** Gráficos analíticos y balances consolidados.
7. **Módulo de Inteligencia Artificial (AI ENGINE):** Integración desacoplada con motor heurístico local y conectores a modelos avanzados (Gemini / OpenAI).

---
*(Salto de página / Page Break)*
---

# 3. MODELADO Y DIAGRAMA DE CASOS DE USO (UML)

### 3.1 Estructura General del Modelo
El modelo se fundamenta en el estándar UML (Unified Modeling Language), organizando las interacciones entre los cuatro actores del sistema y los trece casos de uso principales.

```
+----------------------------------------------------------------------------------------------------+
|                                  SISTEMA WEB INTEGRAL "PAPER"                                      |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|    [ MÓDULO DE SEGURIDAD ]                                                                         |
|    (CU-001: Iniciar Sesión) <----------------------------- [ACT-01: Administrador]                 |
|    (CU-002: Gestionar Usuarios) <------------------------- [ACT-01: Administrador]                 |
|                                                                                                    |
|    [ MÓDULO DE INVENTARIO Y CATÁLOGO ]                                                             |
|    (CU-003: Gestionar Categorías) <----------------------- [ACT-01: Administrador]                 |
|    (CU-004: Gestionar Productos y Precios) <-------------- [ACT-01: Administrador]                 |
|    (CU-005: Controlar Kárdex / Movimientos) <------------- [ACT-01: Administrador]                 |
|    (CU-006: Monitorear Alertas de Stock) <---------------- [ACT-01 / ACT-02: Trabajador]           |
|                                                                                                    |
|    [ MÓDULO POS Y CLIENTES ]                                                                       |
|    (CU-007: Registrar / Consultar Clientes) <------------- [ACT-01 / ACT-02 / ACT-03: Cliente]     |
|    (CU-008: Procesar Venta POS) <------------------------- [ACT-01 / ACT-02]                       |
|         |-- <<include>> Descuenta Stock (CU-004)                                                   |
|         |-- <<include>> Suma Saldo en Cuenta Papelería (CU-011)                                    |
|    (CU-009: Consultar y Anular Venta) <------------------- [ACT-01: Administrador]                 |
|                                                                                                    |
|    [ MÓDULO CORRESPONSAL Y CUENTAS ]                                                               |
|    (CU-010: Procesar Operación Corresponsal) <------------ [ACT-01 / ACT-02 / ACT-03: Cliente]     |
|         |-- <<include>> Actualiza Saldo Corresponsal (CU-011)                                      |
|    (CU-011: Transferencias y Conciliación) <-------------- [ACT-01: Administrador]                 |
|                                                                                                    |
|    [ MÓDULO REPORTES E INTELIGENCIA ARTIFICIAL ]                                                   |
|    (CU-012: Consultar Dashboard y Reportes) <------------- [ACT-01: Administrador]                 |
|    (CU-013: Generar Diagnóstico con IA) <----------------- [ACT-01 / ACT-04: Motor IA]            |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

### 3.2 Matriz de Interacción Actor vs. Caso de Uso

| Código | Nombre del Caso de Uso | Administrador (ACT-01) | Trabajador (ACT-02) | Cliente (ACT-03) | Motor IA (ACT-04) |
| :---: | :--- | :---: | :---: | :---: | :---: |
| **CU-001** | Autenticación e Inicio de Sesión | **X** | **X** | | |
| **CU-002** | Gestión de Usuarios del Sistema | **X** | | | |
| **CU-003** | Gestión de Categorías de Productos | **X** | | | |
| **CU-004** | Gestión de Catálogo de Productos | **X** | | | |
| **CU-005** | Control de Movimientos de Inventario | **X** | | | |
| **CU-006** | Monitoreo de Alertas de Stock | **X** | **X** | | |
| **CU-007** | Gestión y Registro de Clientes | **X** | **X** | **(Beneficiario)** | |
| **CU-008** | Procesamiento de Venta POS | **X** | **X** | **(Comprador)** | |
| **CU-009** | Consulta y Cancelación de Ventas | **X** | | | |
| **CU-010** | Operaciones de Corresponsal Bancario | **X** | **X** | **(Solicitante)** | |
| **CU-011** | Gestión de Cuentas y Transferencias | **X** | | | |
| **CU-012** | Visualización de Dashboard y Reportes | **X** | | | |
| **CU-013** | Diagnósticos y Recomendaciones con IA | **X** | | | **X** |

---
*(Salto de página / Page Break)*
---

# 4. DESCRIPCIÓN DETALLADA DE ACTORES

### 4.1 ACT-01: Administrador del Sistema

| Parámetro | Especificación del Actor |
| :--- | :--- |
| **Actor** | **Administrador del Sistema** |
| **Identificador** | **ACT-01** |
| **Descripción** | Rol con privilegios absolutos sobre la plataforma. Encargado de la supervisión financiera, auditoría de ventas, administración de personal, ajuste de inventarios y ejecución de análisis con IA. |
| **Características** | Usuario con perfil gerencial y de supervisión. Requiere visión 360° del negocio. |
| **Relación** | Posee jerarquía sobre el Trabajador/Cajero. |
| **Referencias** | CU-001 hasta CU-013 (Acceso integral). |

#### Atributos del Actor
| Atributo | Descripción | Tipo de Dato |
| :--- | :--- | :--- |
| `id_usuario` | Llave primaria del usuario | INT (Auto-increment) |
| `id_rol` | Identificador del Rol (1 = ADMINISTRADOR) | INT |
| `nombre` | Nombres del administrador | VARCHAR(100) |
| `apellido` | Apellidos del administrador | VARCHAR(100) |
| `documento` | Cédula de ciudadanía o extranjería | VARCHAR(30) UNIQUE |
| `correo` | Correo electrónico institucional | VARCHAR(150) UNIQUE |
| `nombre_usuario` | Credencial alfanumérica de acceso | VARCHAR(50) UNIQUE |
| `contrasena` | Clave de seguridad encriptada en Bcrypt | VARCHAR(255) |
| `estado` | Estado de la cuenta en el sistema | ENUM('ACTIVO','INACTIVO') |

#### Comentarios
Es el único usuario con potestad para anular facturas, crear usuarios y transferir dinero entre cuentas.

---

### 4.2 ACT-02: Trabajador / Operador POS

| Parámetro | Especificación del Actor |
| :--- | :--- |
| **Actor** | **Trabajador / Operador POS** |
| **Identificador** | **ACT-02** |
| **Descripción** | Colaborador operativo del establecimiento encargado de la atención en mostrador, registro de clientes, facturación en punto de venta y ejecución de depósitos/retiros de corresponsal. |
| **Características** | Perfil operativo optimizado para tareas de alta velocidad y baja fricción. |
| **Relación** | Subordinado al Administrador. Restringido para modificar usuarios o transferir cuentas. |
| **Referencias** | CU-001, CU-006, CU-007, CU-008, CU-010. |

#### Atributos del Actor
| Atributo | Descripción | Tipo de Dato |
| :--- | :--- | :--- |
| `id_usuario` | Llave primaria del colaborador | INT (Auto-increment) |
| `id_rol` | Identificador del Rol (2 = TRABAJADOR) | INT |
| `nombre` | Nombres del trabajador | VARCHAR(100) |
| `apellido` | Apellidos del trabajador | VARCHAR(100) |
| `documento` | Cédula de ciudadanía | VARCHAR(30) UNIQUE |
| `telefono` | Teléfono móvil o fijo de contacto | VARCHAR(20) |
| `nombre_usuario` | Usuario para inicio de sesión | VARCHAR(50) UNIQUE |
| `estado` | Estado de la cuenta | ENUM('ACTIVO','INACTIVO') |

#### Comentarios
Cada transacción queda asociada a su `id_usuario` para asegurar cuadre de caja individual al final del turno.

---

### 4.3 ACT-03: Cliente

| Parámetro | Especificación del Actor |
| :--- | :--- |
| **Actor** | **Cliente** |
| **Identificador** | **ACT-03** |
| **Descripción** | Persona natural o jurídica consumidora de bienes de papelería o usuaria de servicios de corresponsalía financiera. |
| **Características** | Entidad externa que no interactúa directamente con el software, pero cuyos datos tributarios y de contacto son capturados por el sistema. |
| **Relación** | Asociado a comprobantes de venta y recibos de corresponsal. |
| **Referencias** | CU-007, CU-008, CU-010. |

#### Atributos del Actor
| Atributo | Descripción | Tipo de Dato |
| :--- | :--- | :--- |
| `id_cliente` | Llave primaria del cliente | INT (Auto-increment) |
| `tipo_identificacion` | Tipo de documento tributario / legal | ENUM('CC','CE','NIT','TI','PASAPORTE') |
| `numero_identificacion` | Número oficial de documento | VARCHAR(30) UNIQUE |
| `nombre` | Nombre o Razón Social | VARCHAR(100) |
| `apellido` | Apellidos (persona natural) | VARCHAR(100) |
| `telefono` | Teléfono de contacto | VARCHAR(20) |
| `correo` | Correo electrónico de facturación | VARCHAR(150) |
| `direccion` | Dirección física | VARCHAR(200) |

#### Comentarios
Permite emitir comprobantes personalizados y soportar convenios comerciales con empresas o instituciones.

---

### 4.4 ACT-04: Motor de Inteligencia Artificial (IA)

| Parámetro | Especificación del Actor |
| :--- | :--- |
| **Actor** | **Motor de Inteligencia Artificial (Local / Remoto)** |
| **Identificador** | **ACT-04** |
| **Descripción** | Servicio computacional desacoplado que analiza patrones de venta, velocidad de rotación de inventarios y niveles de stock para generar sugerencias inteligentes. |
| **Características** | Agente automatizado con proveedor dual: heurístico local y conectores REST (Gemini / OpenAI). |
| **Relación** | Provee insumos analíticos y diagnósticos al Administrador. |
| **Referencias** | CU-013. |

#### Atributos del Actor
| Atributo | Descripción | Tipo de Dato |
| :--- | :--- | :--- |
| `id_analisis` | Identificador único del reporte de IA | INT (Auto-increment) |
| `tipo` | Tipología del análisis ejecutado | VARCHAR(100) |
| `titulo` | Título del dictamen emitido | VARCHAR(200) |
| `resultado` | Conclusiones e insights predictivos | TEXT |
| `prioridad` | Nivel de urgencia sugerido (ALTA / MEDIA / BAJA) | VARCHAR(20) |

---
*(Salto de página / Page Break)*
---

# 5. ESPECIFICACIÓN FORMAL DE CASOS DE USO

---

### 5.1 CU-001: Autenticación e Inicio de Sesión

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Autenticación e Inicio de Sesión** |
| **Identificador** | **CU-001** |
| **Actores** | Administrador (ACT-01), Trabajador (ACT-02) |
| **Tipo** | Primario / Seguridad |
| **Referencias** | Requerimiento Funcional RF-SEG-01 |
| **Precondición** | El usuario debe estar registrado en la base de datos con estado `ACTIVO`. |
| **Postcondición** | Se inicializa la sesión de usuario y el sistema redirige al módulo correspondiente a su rol. |
| **Descripción** | Permite verificar la identidad del colaborador mediante credenciales cifradas para otorgar acceso al entorno web. |
| **Resumen** | Captura de usuario/clave, validación con `password_verify` y apertura de sesión autenticada. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Usuario | Ingresa a la dirección web del sistema y visualiza la pantalla de inicio de sesión. |
| **2** | Usuario | Introduce su nombre de usuario o correo y contraseña, presionando "Iniciar Sesión". |
| **3** | Sistema | Sanitiza las entradas para prevenir inyecciones SQL y busca la cuenta en la tabla `usuario`. |
| **4** | Sistema | Verifica que el usuario exista y mantenga estado `ACTIVO`. |
| **5** | Sistema | Compara la contraseña ingresada contra el hash almacenado mediante `password_verify()`. |
| **6** | Sistema | Registra la marca de tiempo actual en el campo `ultimo_acceso`. |
| **7** | Sistema | Asigna los datos en la variable global `$_SESSION` (`id_usuario`, `nombre`, `id_rol`). |
| **8** | Sistema | Redirige al Dashboard principal (Administrador) o a la interfaz POS (Trabajador). |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **3.1** | **Campos vacíos:** El sistema alerta "Todos los campos son obligatorios" y detiene la petición. |
| **4.1** | **Usuario inexistente:** El sistema emite "Credenciales de acceso incorrectas". |
| **4.2** | **Cuenta Inactiva:** El sistema deniega el acceso con el mensaje: "Su cuenta se encuentra desactivada. Contacte al Administrador". |
| **5.1** | **Contraseña no coincide:** El sistema notifica "Credenciales de acceso incorrectas" sin detallar cuál campo falló por seguridad. |

---

### 5.2 CU-002: Gestión Integral de Usuarios y Permisos

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Gestión Integral de Usuarios y Permisos** |
| **Identificador** | **CU-002** |
| **Actores** | Administrador (ACT-01) |
| **Tipo** | Primario / Administración |
| **Referencias** | Requerimiento Funcional RF-ADM-01 |
| **Precondición** | El Administrador debe contar con una sesión activa en el sistema. |
| **Postcondición** | Se registra, actualiza o cambia el estado de la cuenta en la tabla `usuario`. |
| **Descripción** | Permite dar de alta a nuevos empleados, actualizar datos personales, resetear contraseñas y activar o inactivar accesos. |
| **Resumen** | Mantenimiento completo de cuentas de usuario con control de unicidad de documentos y correos. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Ingresa a la sección "Gestión de Usuarios" desde el menú lateral. |
| **2** | Sistema | Lista todos los usuarios con sus nombres, documento, rol, correo y estado actual. |
| **3** | Administrador | Hace clic en "Nuevo Usuario" y diligencia el formulario (Nombres, Apellidos, Documento, Rol, Usuario y Contraseña). |
| **4** | Sistema | Valida que el documento, correo y usuario no se encuentren previamente registrados. |
| **5** | Sistema | Aplica el algoritmo de cifrado `password_hash()` e inserta el nuevo registro. |
| **6** | Sistema | Confirma la operación con un mensaje de éxito y recarga el listado. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **4.1** | **Registro duplicado:** El sistema alerta "El documento o correo ya pertenece a otro usuario registrado". |
| **4.2** | **Edición de datos:** El Administrador modifica registros existentes; si no ingresa contraseña nueva, se preserva la actual. |
| **4.3** | **Desactivación de cuenta:** El Administrador conmuta el estado a `INACTIVO`, inhabilitando el acceso inmediato del empleado. |

---

### 5.3 CU-003: Gestión de Categorías de Productos

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Gestión de Categorías de Productos** |
| **Identificador** | **CU-003** |
| **Actores** | Administrador (ACT-01) |
| **Tipo** | Primario / Catálogo |
| **Referencias** | Requerimiento Funcional RF-CAT-01 |
| **Precondición** | Sesión de Administrador activa. |
| **Postcondición** | La categoría es almacenada en la tabla `categoria` con estado `ACTIVO`. |
| **Descripción** | Clasificación sistemática de los artículos para facilitar su búsqueda y filtrado en el POS. |
| **Resumen** | Creación, modificación y consulta de líneas de artículos (e.g., Escritura, Papeles, Carpetas). |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Accede al submódulo de "Categorías". |
| **2** | Sistema | Despliega la tabla con las categorías existentes y el total de productos asociados. |
| **3** | Administrador | Presiona "Crear Categoría", digita el Nombre y la Descripción y guarda. |
| **4** | Sistema | Comprueba que el nombre de la categoría sea único en el sistema. |
| **5** | Sistema | Inserta el registro en la base de datos y actualiza la vista. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **4.1** | **Categoría duplicada:** El sistema notifica "Ya existe una categoría registrada con este nombre". |

---

### 5.4 CU-004: Gestión de Catálogo y Precios de Productos

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Gestión de Catálogo y Precios de Productos** |
| **Identificador** | **CU-004** |
| **Actores** | Administrador (ACT-01) |
| **Tipo** | Primario / Inventario |
| **Referencias** | Requerimiento Funcional RF-INV-01 |
| **Precondición** | Debe existir al menos una categoría creada en el sistema. |
| **Postcondición** | El producto queda disponible para venta y se registra la traza en `historial_precio`. |
| **Descripción** | Alta de productos con código de barras, descripción, precio unitario, stock inicial y stock de seguridad mínimo. |
| **Resumen** | Administración del inventario comercial y control histórico de cambios tarifarios. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Ingresa a la sección "Productos". |
| **2** | Sistema | Presenta la tabla completa de artículos con filtros por categoría y stock. |
| **3** | Administrador | Presiona "Nuevo Producto" y completa los campos obligatorios. |
| **4** | Sistema | Valida que el código de barras/referencia sea único y que los precios y stock sean positivos. |
| **5** | Sistema | Inserta el producto en `producto` y crea el primer registro en `historial_precio`. |
| **6** | Sistema | Genera el movimiento inicial de entrada en el kárdex y emite alerta de éxito. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **4.1** | **Código duplicado:** El sistema rechaza el registro indicando "El código de barras ya está asignado a otro producto". |
| **5.1** | **Cambio de precio:** Si se edita el valor de venta, el sistema almacena el precio anterior, el nuevo precio, la fecha y el usuario en `historial_precio`. |

---

### 5.5 CU-005: Control de Movimientos de Inventario (Kárdex)

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Control de Movimientos de Inventario (Kárdex)** |
| **Identificador** | **CU-005** |
| **Actores** | Administrador (ACT-01) |
| **Tipo** | Primario / Auditoría |
| **Referencias** | Requerimiento Funcional RF-INV-02 |
| **Precondición** | El producto debe estar previamente registrado. |
| **Postcondición** | El stock actual del producto se actualiza y se asienta el registro en `movimiento_inventario`. |
| **Descripción** | Permite ingresar mercancía de proveedores (`ENTRADA`), dar de baja por daño (`SALIDA`) o cuadrar diferencias físicas (`AJUSTE`). |
| **Resumen** | Trazabilidad exacta de cada unidad física con cálculo de saldo anterior y saldo resultante. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Accede a "Movimientos de Inventario". |
| **2** | Administrador | Selecciona el producto, el tipo de movimiento (`ENTRADA`, `SALIDA`, `AJUSTE`), cantidad y motivo. |
| **3** | Sistema | Recupera el `stock_anterior` desde la base de datos. |
| **4** | Sistema | Calcula matemáticamente el `stock_nuevo`. |
| **5** | Sistema | Ejecuta la transacción: actualiza `producto.stock_actual` e inserta en `movimiento_inventario`. |
| **6** | Sistema | Evalúa si el nuevo stock es inferior al umbral mínimo para disparar alerta. |
| **7** | Sistema | Confirma la transacción con mensaje de éxito. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **4.1** | **Stock insuficiente en Salida:** Si la cantidad a dar de baja supera el inventario real, el sistema bloquea la acción. |
| **5.1** | **Fallo transaccional:** Si la base de datos genera error, se aplica `ROLLBACK` y ningún dato se altera. |

---

### 5.6 CU-006: Monitoreo y Gestión de Alertas de Stock Mínimo

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Monitoreo y Gestión de Alertas de Stock Mínimo** |
| **Identificador** | **CU-006** |
| **Actores** | Administrador (ACT-01), Trabajador (ACT-02) |
| **Tipo** | Secundario / Soporte |
| **Referencias** | Requerimiento Funcional RF-ALT-01 |
| **Precondición** | Algún producto tiene `stock_actual <= stock_minimo`. |
| **Postcondición** | Se crea un registro en `alerta_inventario` y se refleja visualmente en el Dashboard. |
| **Descripción** | Notificación temprana en pantalla para prevenir quiebres de inventario en artículos de alta rotación. |
| **Resumen** | Detección automática y atención manual de alertas de reabastecimiento. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Sistema | Tras cada venta o salida, evalúa si `stock_actual <= stock_minimo`. |
| **2** | Sistema | Crea o actualiza el registro en `alerta_inventario` con `atendida = 0`. |
| **3** | Usuario | Visualiza la insignia de alerta en la barra superior o en el Dashboard. |
| **4** | Administrador | Realiza la compra de reposición, registra la entrada y pulsa "Marcar Atendida". |
| **5** | Sistema | Cambia el estado a `atendida = 1` y retira la notificación activa. |

---

### 5.7 CU-007: Gestión y Registro de Clientes

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Gestión y Registro de Clientes** |
| **Identificador** | **CU-007** |
| **Actores** | Administrador (ACT-01), Trabajador (ACT-02), Cliente (ACT-03) |
| **Tipo** | Primario |
| **Referencias** | Requerimiento Funcional RF-CLI-01 |
| **Precondición** | Sesión de usuario activa en el sistema. |
| **Postcondición** | Los datos del cliente se almacenan en la tabla `cliente`. |
| **Descripción** | Registro de personas o empresas para emisión de facturas o vinculación en operaciones de corresponsal. |
| **Resumen** | Creación rápida desde el POS o administración centralizada en el módulo de clientes. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Operador | Accede al módulo "Clientes" o abre el modal rápido desde el punto de venta. |
| **2** | Operador | Digita el tipo de documento, número de identificación, nombres, teléfono y correo. |
| **3** | Sistema | Valida que el número de identificación no exista previamente. |
| **4** | Sistema | Guarda el cliente en estado `ACTIVO`. |
| **5** | Sistema | Retorna el cliente seleccionado para su facturación inmediata. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **3.1** | **Cliente existente:** El sistema muestra sus datos precargados para evitar duplicidades. |

---

### 5.8 CU-008: Procesamiento de Venta y Facturación POS

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Procesamiento de Venta y Facturación POS** |
| **Identificador** | **CU-008** |
| **Actores** | Administrador (ACT-01), Trabajador (ACT-02), Cliente (ACT-03) |
| **Tipo** | Primario / Facturación |
| **Referencias** | Requerimiento Funcional RF-POS-01 |
| **Precondición** | Existen productos con stock disponible y la cuenta `PAPELERIA` está activa. |
| **Postcondición** | Se genera la venta, se rebaja el inventario y se incrementa el saldo en la cuenta de papelería. |
| **Descripción** | Módulo de punto de venta interactivo con carrito en tiempo real, lectura por código de barras y liquidación en caja. |
| **Resumen** | Venta de mostrador con ejecución atómica de inventario y tesorería. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Operador | Abre el módulo Punto de Venta (POS). |
| **2** | Operador | Busca productos o escanea con lector de barras, indicando cantidades. |
| **3** | Sistema | Valida que la cantidad en el carrito no exceda las existencias en tiempo real. |
| **4** | Sistema | Calcula subtotales, impuestos (si aplica) y total general. |
| **5** | Operador | Selecciona el cliente y confirma el monto recibido. |
| **6** | Sistema | Inicia transacción SQL (ACID): <br>1. Inserta la cabecera en `venta`. <br>2. Inserta los ítems en `detalle_venta`. <br>3. Resta el stock en `producto`. <br>4. Genera salidas en `movimiento_inventario`. <br>5. Acredita el dinero en `movimiento_cuenta` para la cuenta `PAPELERIA`. |
| **7** | Sistema | Confirma la transacción (COMMIT), genera el ticket para impresión y reinicia el POS. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **3.1** | **Stock no disponible:** El sistema restringe la adición y notifica "Existencias insuficientes para este producto". |
| **6.1** | **Fallo en el proceso:** Se aplica `ROLLBACK` automático para evitar ventas a medias o desbalances de caja. |

---

### 5.9 CU-009: Consulta, Arqueo y Anulación de Ventas

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Consulta, Arqueo y Anulación de Ventas** |
| **Identificador** | **CU-009** |
| **Actores** | Administrador (ACT-01) |
| **Tipo** | Secundario / Control |
| **Referencias** | Requerimiento Funcional RF-AUD-01 |
| **Precondición** | La venta seleccionada debe encontrarse en estado `COMPLETADA`. |
| **Postcondición** | La venta cambia a `CANCELADA`, las unidades vuelven al stock y se descuenta el ingreso de caja. |
| **Descripción** | Auditoría y anulación justificada de tickets emitidos por equivocación o devolución de producto. |
| **Resumen** | Reversión integral de la operación comercial y restitución automática de existencias. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Ingresa a "Historial de Ventas". |
| **2** | Administrador | Filtra la venta por fecha, código o cajero y revisa el desglose del comprobante. |
| **3** | Administrador | Selecciona "Anular Venta", especificando el motivo. |
| **4** | Sistema | Inicia transacción: <br>1. Actualiza `venta.estado = 'CANCELADA'`. <br>2. Reintegra las cantidades al `stock_actual` de cada producto. <br>3. Inserta movimientos de entrada en kárdex por reversión. <br>4. Registra un `EGRESO` en `movimiento_cuenta` para restar el importe de la cuenta `PAPELERIA`. |
| **5** | Sistema | Confirma el reverso de fondos e inventario. |

---

### 5.10 CU-010: Registro de Depósitos y Retiros de Corresponsal

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Registro de Depósitos y Retiros de Corresponsal** |
| **Identificador** | **CU-010** |
| **Actores** | Administrador (ACT-01), Trabajador (ACT-02), Cliente (ACT-03) |
| **Tipo** | Primario / Corresponsal |
| **Referencias** | Requerimiento Funcional RF-COR-01 |
| **Precondición** | La cuenta `CORRESPONSAL` debe estar activa y poseer saldo suficiente en caso de retiro. |
| **Postcondición** | La transacción queda registrada en `operacion_corresponsal` y el saldo bancario se ajusta. |
| **Descripción** | Permite recibir dinero del cliente (`DEPOSITO`) o entregar dinero en efectivo (`RETIRO`) manteniendo las finanzas del corresponsal totalmente independientes de la papelería. |
| **Resumen** | Operaciones financieras con control estricto de caja y referencias de comprobante. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Operador | Accede al módulo "Corresponsal Bancario". |
| **2** | Operador | Selecciona el tipo de transacción (`DEPOSITO` o `RETIRO`), digita el valor, referencia y asocia al cliente. |
| **3** | Sistema | En retiros, verifica que el `saldo` de la cuenta `CORRESPONSAL` sea mayor o igual al importe solicitado. |
| **4** | Operador | Confirma el conteo físico del dinero en efectivo. |
| **5** | Sistema | Inicia transacción: <br>1. Inserta en `operacion_corresponsal`. <br>2. Registra el movimiento en `movimiento_cuenta` (`DEPOSITO` incrementa saldo; `RETIRO` descuenta saldo). <br>3. Actualiza el saldo en la tabla `cuenta`. |
| **6** | Sistema | Genera el comprobante de la transacción para entrega al cliente. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **3.1** | **Fondos insuficientes:** El sistema impide el retiro y muestra: "Saldo insuficiente en la caja del corresponsal para realizar el desembolso". |

---

### 5.11 CU-011: Gestión de Cuentas y Transferencias Financieras

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Gestión de Cuentas y Transferencias Financieras** |
| **Identificador** | **CU-011** |
| **Actores** | Administrador (ACT-01) |
| **Tipo** | Primario / Tesorería |
| **Referencias** | Requerimiento Funcional RF-FIN-01 |
| **Precondición** | Ambas cuentas (`PAPELERIA` y `CORRESPONSAL`) deben estar activas y la cuenta origen debe tener fondos. |
| **Postcondición** | Se registra la transferencia en `transferencia` y se actualizan los saldos de ambas cuentas. |
| **Descripción** | Permite mover dinero entre las cajas de papelería y corresponsal para reabastecer liquidez o consolidar ganancias. |
| **Resumen** | Rebalanceo financiero con doble apunte contable y auditoría de usuario. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Ingresa a "Cuentas y Conciliación". |
| **2** | Sistema | Muestra los saldos actuales de cada cuenta en tiempo real. |
| **3** | Administrador | Selecciona "Nueva Transferencia", eligiendo cuenta de origen, cuenta destino, monto y justificación. |
| **4** | Sistema | Valida que origen != destino y que la cuenta de origen tenga saldo disponible suficiente. |
| **5** | Sistema | Ejecuta transacción: <br>1. Registra egreso en cuenta origen. <br>2. Registra ingreso en cuenta destino. <br>3. Inserta registro en `transferencia`. <br>4. Actualiza saldos en `cuenta`. |
| **6** | Sistema | Notifica "Transferencia de fondos ejecutada con éxito". |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **4.1** | **Fondos insuficientes:** El sistema notifica "La cuenta origen no cuenta con la liquidez solicitada". |

---

### 5.12 CU-012: Visualización de Dashboard y Reportes Gerenciales

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Visualización de Dashboard y Reportes Gerenciales** |
| **Identificador** | **CU-012** |
| **Actores** | Administrador (ACT-01) |
| **Tipo** | Soporte / Analítica |
| **Referencias** | Requerimiento Funcional RF-REP-01 |
| **Precondición** | Sesión de Administrador activa. |
| **Postcondición** | El sistema renderiza estadísticas, gráficos y consolidados de ventas. |
| **Descripción** | Centro de control ejecutivo que sintetiza ventas brutas, utilidades, productos estrella y alertas de inventario. |
| **Resumen** | Presentación visual interactiva de métricas e indicadores de rendimiento del negocio. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Accede al "Dashboard" o módulo de "Reportes". |
| **2** | Sistema | Consulta sumatorias de ventas, total de transacciones y saldos en cuentas. |
| **3** | Sistema | Dibuja gráficos dinámicos con Chart.js (Ventas semanales/mensuales, top productos). |
| **4** | Administrador | Aplica filtros por fechas o categorías para análisis detallado y exporta a formato imprimible. |

---

### 5.13 CU-013: Diagnóstico Predictivo y Recomendaciones con IA

| FICHA DE ESPECIFICACIÓN | DETALLE |
| :--- | :--- |
| **Caso de Uso** | **Diagnóstico Predictivo y Recomendaciones con IA** |
| **Identificador** | **CU-013** |
| **Actores** | Administrador (ACT-01), Motor de IA (ACT-04) |
| **Tipo** | Primario / Inteligencia de Negocio |
| **Referencias** | Requerimiento Funcional RF-IA-01 |
| **Precondición** | Existencia de datos transaccionales históricos de ventas e inventario. |
| **Postcondición** | Se guarda el dictamen en `analisis_ia` y las acciones sugeridas en `recomendacion_ia`. |
| **Descripción** | Procesa estadísticas para predecir qué artículos sufrirán quiebre de stock, cuáles no tienen rotación y qué compras priorizar. |
| **Resumen** | Ejecución de modelos inteligentes (heurístico local o API externa) con panel de recomendaciones priorizadas. |

#### Curso Normal de Eventos

| Paso | Ejecutor | Acción / Actividad Realizada |
| :---: | :--- | :--- |
| **1** | Administrador | Ingresa a la sección "Diagnóstico Inteligente con IA". |
| **2** | Administrador | Elige el tipo de análisis (e.g., "Rotación y Predicción de Demanda") y hace clic en "Procesar Análisis". |
| **3** | Sistema | Compila la tasa de rotación por producto, días de inventario restante y ventas promedio. |
| **4** | Sistema | Despacha la solicitud al proveedor configurado (`LocalAIProvider` o servicio remoto). |
| **5** | Motor IA | Analiza las métricas y formula el informe con nivel de prioridad (ALTA, MEDIA, BAJA). |
| **6** | Sistema | Persiste el análisis en `analisis_ia` y las recomendaciones individuales en `recomendacion_ia`. |
| **7** | Sistema | Muestra las recomendaciones con acciones directas para el Administrador. |

#### Cursos Alternos y Excepciones

| Código | Condición y Respuesta del Sistema |
| :---: | :--- |
| **4.1** | **Indisponibilidad de API Remota:** El sistema cambia automáticamente al motor local heurístico garantizando la continuidad del análisis. |
| **5.1** | **Historial insuficiente:** Si el negocio apenas inicia y hay menos de 5 ventas, el sistema advierte que los pronósticos aumentarán su precisión conforme se registren más operaciones. |

---

<br><br>

### Fin del Documento de Propuesta Técnica
*Propuesta desarrollada para la implementación y sustentación del proyecto **Paper**.*
