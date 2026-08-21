<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Venta.php';
require_once __DIR__ . '/../Models/DetalleVenta.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/MovimientoInventario.php';
require_once __DIR__ . '/../Models/AlertaInventario.php';
require_once __DIR__ . '/../Models/Cuenta.php';
require_once __DIR__ . '/../Models/MovimientoCuenta.php';

class VentaService {
    private PDO $db;
    private Venta $ventaModel;
    private DetalleVenta $detalleVentaModel;
    private Producto $productoModel;
    private MovimientoInventario $movimientoInventarioModel;
    private AlertaInventario $alertaModel;
    private Cuenta $cuentaModel;
    private MovimientoCuenta $movimientoCuentaModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->ventaModel = new Venta();
        $this->detalleVentaModel = new DetalleVenta();
        $this->productoModel = new Producto();
        $this->movimientoInventarioModel = new MovimientoInventario();
        $this->alertaModel = new AlertaInventario();
        $this->cuentaModel = new Cuenta();
        $this->movimientoCuentaModel = new MovimientoCuenta();
    }

    /**
     * Procesa una venta completa con validación atómica de inventario y actualización de cuenta
     * @param int $userId
     * @param int|null $clientId
     * @param array $items Array de ítems con [id_producto, cantidad, precio]
     * @return array
     */
    public function procesarVenta(int $userId, ?int $clientId, array $items): array {
        if (empty($items)) {
            return ['success' => false, 'message' => 'El carrito de ventas no contiene ningún producto.'];
        }

        try {
            $this->db->beginTransaction();

            $subtotalGeneral = 0.0;
            $itemsValidados = [];

            // 1. Validar productos y existencias
            foreach ($items as $item) {
                $productId = (int)($item['id_producto'] ?? 0);
                $cantidad = (int)($item['cantidad'] ?? 0);

                if ($productId <= 0 || $cantidad <= 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Datos de producto inválidos en el carrito.'];
                }

                $producto = $this->productoModel->getById($productId);
                if (!$producto) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "El producto con ID {$productId} no fue encontrado."];
                }

                if ($producto['estado'] !== 'ACTIVO') {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "El producto '{$producto['nombre']}' se encuentra inactivo."];
                }

                $stockActual = (int)$producto['stock_actual'];
                if ($stockActual < $cantidad) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "Stock insuficiente para '{$producto['nombre']}'. Disponible: {$stockActual}, solicitado: {$cantidad}."];
                }

                $precioUnitario = (float)$producto['precio'];
                $subtotalItem = $precioUnitario * $cantidad;
                $subtotalGeneral += $subtotalItem;

                $itemsValidados[] = [
                    'producto'        => $producto,
                    'id_producto'     => $productId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal'        => $subtotalItem,
                    'stock_actual'    => $stockActual,
                    'stock_minimo'    => (int)$producto['stock_minimo']
                ];
            }

            $totalGeneral = $subtotalGeneral; // Si hay impuestos o descuentos futuros se calculan aquí

            // 2. Registrar cabecera de la venta
            $ventaId = $this->ventaModel->create($userId, $clientId, $subtotalGeneral, $totalGeneral);

            // 3. Registrar detalles y descontar inventario
            foreach ($itemsValidados as $item) {
                // Detalle de venta
                $this->detalleVentaModel->create(
                    $ventaId,
                    $item['id_producto'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal']
                );

                // Descontar inventario
                $stockNuevo = $item['stock_actual'] - $item['cantidad'];
                $this->productoModel->updateStock($item['id_producto'], $stockNuevo);

                // Registrar movimiento de inventario
                $this->movimientoInventarioModel->create([
                    'id_producto'    => $item['id_producto'],
                    'id_usuario'     => $userId,
                    'tipo'           => 'SALIDA',
                    'cantidad'       => $item['cantidad'],
                    'stock_anterior' => $item['stock_actual'],
                    'stock_nuevo'    => $stockNuevo,
                    'motivo'         => "Venta #{$ventaId}"
                ]);

                // Generar alerta de bajo inventario si aplica
                if ($stockNuevo <= $item['stock_minimo']) {
                    $this->alertaModel->create(
                        $item['id_producto'],
                        $stockNuevo,
                        $item['stock_minimo'],
                        "Stock bajo tras venta #{$ventaId}: '{$item['producto']['nombre']}' tiene {$stockNuevo} unidades (mínimo {$item['stock_minimo']})"
                    );
                }
            }

            // 4. Registrar ingreso en Cuenta PAPELERIA
            $cuentaPapeleria = $this->cuentaModel->getByTipo('PAPELERIA');
            if ($cuentaPapeleria) {
                $cuentaId = (int)$cuentaPapeleria['id_cuenta'];
                $saldoAnterior = (float)$cuentaPapeleria['saldo'];
                $saldoNuevo = $saldoAnterior + $totalGeneral;

                $this->cuentaModel->updateSaldo($cuentaId, $saldoNuevo);

                $this->movimientoCuentaModel->create([
                    'id_cuenta'      => $cuentaId,
                    'id_usuario'     => $userId,
                    'id_venta'       => $ventaId,
                    'tipo'           => 'INGRESO',
                    'concepto'       => "Ingreso por Venta #{$ventaId}",
                    'valor'          => $totalGeneral,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_nuevo'    => $saldoNuevo
                ]);
            }

            $this->db->commit();
            return [
                'success'  => true,
                'message'  => "Venta #{$ventaId} completada exitosamente.",
                'id_venta' => $ventaId,
                'total'    => $totalGeneral
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al procesar la venta: ' . $e->getMessage()];
        }
    }
}
