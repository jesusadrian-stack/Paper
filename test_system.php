<?php
/**
 * Script de Pruebas Automatizadas del Sistema
 */

require_once __DIR__ . '/Config/config.php';
require_once __DIR__ . '/Config/database.php';
require_once __DIR__ . '/Services/AuthService.php';
require_once __DIR__ . '/Services/VentaService.php';
require_once __DIR__ . '/Services/InventarioService.php';
require_once __DIR__ . '/Services/CuentaService.php';
require_once __DIR__ . '/Services/CorresponsalService.php';
require_once __DIR__ . '/Services/TransferenciaService.php';
require_once __DIR__ . '/Services/AIService.php';
require_once __DIR__ . '/Services/ReporteService.php';

echo "====================================================\n";
echo "INICIANDO BATERÍA DE PRUEBAS DEL SISTEMA PAPELERÍA\n";
echo "====================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest($condition, $testName) {
    global $passed, $failed;
    if ($condition) {
        echo " [OK] " . $testName . "\n";
        $passed++;
    } else {
        echo " [ERROR] " . $testName . "\n";
        $failed++;
    }
}

// 1. Conexión a Base de Datos
try {
    $db = Database::getConnection();
    assertTest($db instanceof PDO, "Conexión PDO a MySQL (Laragon)");
} catch (Exception $e) {
    assertTest(false, "Conexión PDO: " . $e->getMessage());
}

// 2. Autenticación
$auth = new AuthService();
$loginAdminEmail = $auth->login('admin@papeleria.com', '12345');
assertTest($loginAdminEmail['success'] && $loginAdminEmail['user']['rol_nombre'] === 'ADMINISTRADOR', "Autenticación Admin por correo (admin@papeleria.com / 12345)");

$loginAdminUser = $auth->login('admin', '12345');
assertTest($loginAdminUser['success'] && $loginAdminUser['user']['rol_nombre'] === 'ADMINISTRADOR', "Autenticación Admin por usuario (admin / 12345)");

$loginTrabajador = $auth->login('trabajador', 'trabajador123');
assertTest($loginTrabajador['success'] && $loginTrabajador['user']['rol_nombre'] === 'TRABAJADOR', "Autenticación de Trabajador (trabajador/trabajador123)");

// Prueba de Registro de Nuevo Operador
$userModel = new Usuario();
$existingReg = $userModel->getByUsername('usuario_registro_test');
if (!$existingReg) {
    $newUserId = $userModel->create([
        'id_rol'         => 2,
        'nombre'         => 'Test',
        'apellido'       => 'Registro',
        'documento'      => '999888777',
        'telefono'       => '3119998888',
        'correo'         => 'test.registro@papeleria.com',
        'nombre_usuario' => 'usuario_registro_test',
        'contrasena'     => 'clave123',
        'estado'         => 'ACTIVO'
    ]);
    $loginNewReg = $auth->login('usuario_registro_test', 'clave123');
    assertTest($loginNewReg['success'], "Registro y posterior Login de nuevo operador (usuario_registro_test)");
} else {
    $loginNewReg = $auth->login('usuario_registro_test', 'clave123');
    assertTest($loginNewReg['success'], "Login de usuario registrado previamente");
}

$loginInvalido = $auth->login('admin', 'password_falsa');
assertTest(!$loginInvalido['success'], "Rechazo de credenciales incorrectas");

// 3. Inventario y Productos
$invService = new InventarioService();
$prodModel = new Producto();
$productoTest = $prodModel->getByCodigo('ESC001');
assertTest($productoTest !== null, "Búsqueda de producto por código 'ESC001'");

$stockInicial = (int)$productoTest['stock_actual'];
$resEntrada = $invService->registrarEntrada($productoTest['id_producto'], 10, 1, 'Prueba unitaria de entrada');
assertTest($resEntrada['success'], "Registro de entrada de inventario (+10)");

$productoActualizado = $prodModel->getById($productoTest['id_producto']);
assertTest((int)$productoActualizado['stock_actual'] === $stockInicial + 10, "Verificación de nuevo stock físico en base de datos");

// 4. Venta Transaccional (POS)
$ventaService = new VentaService();
$cuentaModel = new Cuenta();
$ctaPapeleriaPre = $cuentaModel->getByTipo('PAPELERIA');
$saldoPapeleriaPre = (float)$ctaPapeleriaPre['saldo'];

$itemsVenta = [
    [
        'id_producto' => $productoTest['id_producto'],
        'cantidad'    => 2,
        'precio'      => (float)$productoTest['precio']
    ]
];

$resVenta = $ventaService->procesarVenta(1, 1, $itemsVenta);
assertTest($resVenta['success'], "Procesamiento de Venta transaccional #" . ($resVenta['id_venta'] ?? 'N/A'));

$ctaPapeleriaPost = $cuentaModel->getByTipo('PAPELERIA');
$saldoPapeleriaPost = (float)$ctaPapeleriaPost['saldo'];
$totalEsperado = 2 * (float)$productoTest['precio'];
assertTest(abs($saldoPapeleriaPost - ($saldoPapeleriaPre + $totalEsperado)) < 0.01, "Impacto contable automático en Caja Papelería (+$totalEsperado)");

// 5. Corresponsal Bancario (Depósito y Retiro)
$corresponsalService = new CorresponsalService();
$ctaCorresponsalPre = $cuentaModel->getByTipo('CORRESPONSAL');
$saldoCorresponsalPre = (float)$ctaCorresponsalPre['saldo'];

// Depósito
$resDep = $corresponsalService->registrarOperacion([
    'id_usuario'  => 1,
    'id_cliente'  => 1,
    'tipo'        => 'DEPOSITO',
    'valor'       => 50000.00,
    'referencia'  => 'DEP-TEST-001',
    'descripcion' => 'Prueba depósito Nequi'
]);
assertTest($resDep['success'], "Operación de Depósito Corresponsal ($50.000)");

// Retiro con saldo
$resRet = $corresponsalService->registrarOperacion([
    'id_usuario'  => 1,
    'id_cliente'  => 1,
    'tipo'        => 'RETIRO',
    'valor'       => 20000.00,
    'referencia'  => 'RET-TEST-001',
    'descripcion' => 'Prueba retiro Bancolombia'
]);
assertTest($resRet['success'], "Operación de Retiro Corresponsal ($20.000)");

// Retiro superior a saldo (debe fallar)
$resRetExcesivo = $corresponsalService->registrarOperacion([
    'id_usuario'  => 1,
    'id_cliente'  => 1,
    'tipo'        => 'RETIRO',
    'valor'       => 999999999.00,
    'referencia'  => 'RET-EXCESO',
    'descripcion' => 'Prueba de saldo insuficiente'
]);
assertTest(!$resRetExcesivo['success'], "Validación estricta de saldo insuficiente en Corresponsal");

// 6. Transferencias entre Cuentas
$transfService = new TransferenciaService();
$ctaOrigen = $cuentaModel->getByTipo('PAPELERIA');
$ctaDestino = $cuentaModel->getByTipo('CORRESPONSAL');

$resTransf = $transfService->realizarTransferencia(1, $ctaOrigen['id_cuenta'], $ctaDestino['id_cuenta'], 25000.00, 'Traspaso de prueba');
assertTest($resTransf['success'], "Transferencia exitosa Papelería -> Corresponsal ($25.000)");

$resTransfMisma = $transfService->realizarTransferencia(1, $ctaOrigen['id_cuenta'], $ctaOrigen['id_cuenta'], 1000.00, 'Prueba misma cuenta');
assertTest(!$resTransfMisma['success'], "Validación: No permitir transferencia a la misma cuenta");

// 7. Inteligencia Artificial
$aiService = new AIService();
$resAI = $aiService->generarAnalisisReabastecimiento(1);
assertTest($resAI['success'] && !empty($resAI['analisis']), "Generación de Diagnóstico Predictivo con IA");

$resAITendencias = $aiService->generarAnalisisTendenciasVentas(1);
assertTest($resAITendencias['success'], "Generación de Análisis de Tendencias de Ventas con IA");

// 8. Reportes
$repService = new ReporteService();
$repVentas = $repService->getReporteVentas();
assertTest($repVentas['total_ventas'] > 0, "Generación de Reporte de Ventas Consolidadas");

$repInventario = $repService->getReporteInventario();
assertTest($repInventario['total_articulos'] > 0 && $repInventario['valor_inventario_total'] > 0, "Generación de Reporte de Valoración de Inventario");

echo "\n====================================================\n";
echo "RESULTADOS DE PRUEBAS: {$passed} PASADAS / {$failed} FALLIDAS\n";
echo "====================================================\n";

if ($failed === 0) {
    echo "¡TODAS LAS PRUEBAS COMPLETADAS CON ÉXITO AL 100%!\n\n";
    exit(0);
} else {
    echo "ALERTA: Se detectaron fallos en las pruebas.\n\n";
    exit(1);
}
