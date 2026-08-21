<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/MovimientoInventario.php';
require_once __DIR__ . '/../Models/AlertaInventario.php';
require_once __DIR__ . '/../Models/HistorialPrecio.php';

class InventarioService {
    private PDO $db;
    private Producto $productoModel;
    private MovimientoInventario $movimientoModel;
    private AlertaInventario $alertaModel;
    private HistorialPrecio $historialPrecioModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->productoModel = new Producto();
        $this->movimientoModel = new MovimientoInventario();
        $this->alertaModel = new AlertaInventario();
        $this->historialPrecioModel = new HistorialPrecio();
    }

    public function registrarEntrada(int $productId, int $cantidad, int $userId, ?string $motivo = null): array {
        if ($cantidad <= 0) {
            return ['success' => false, 'message' => 'La cantidad debe ser mayor a cero.'];
        }

        try {
            $this->db->beginTransaction();

            $producto = $this->productoModel->getById($productId);
            if (!$producto) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Producto no encontrado.'];
            }

            $stockAnterior = (int)$producto['stock_actual'];
            $stockNuevo = $stockAnterior + $cantidad;

            // Actualizar stock
            $this->productoModel->updateStock($productId, $stockNuevo);

            // Registrar movimiento
            $this->movimientoModel->create([
                'id_producto'    => $productId,
                'id_usuario'     => $userId,
                'tipo'           => 'ENTRADA',
                'cantidad'       => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'motivo'         => $motivo ?: 'Entrada de mercancía / Reabastecimiento'
            ]);

            // Si el stock supera el mínimo, resolver alertas pendientes
            $this->alertaModel->resolveByProductIfRestocked($productId, $stockNuevo);

            $this->db->commit();
            return ['success' => true, 'message' => 'Entrada de inventario registrada con éxito.', 'stock_nuevo' => $stockNuevo];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al registrar entrada: ' . $e->getMessage()];
        }
    }

    public function registrarSalida(int $productId, int $cantidad, int $userId, ?string $motivo = null): array {
        if ($cantidad <= 0) {
            return ['success' => false, 'message' => 'La cantidad debe ser mayor a cero.'];
        }

        try {
            $this->db->beginTransaction();

            $producto = $this->productoModel->getById($productId);
            if (!$producto) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Producto no encontrado.'];
            }

            $stockAnterior = (int)$producto['stock_actual'];
            if ($stockAnterior < $cantidad) {
                $this->db->rollBack();
                return ['success' => false, 'message' => "Stock insuficiente. Stock actual: {$stockAnterior}, solicitado: {$cantidad}."];
            }

            $stockNuevo = $stockAnterior - $cantidad;

            // Actualizar stock
            $this->productoModel->updateStock($productId, $stockNuevo);

            // Registrar movimiento
            $this->movimientoModel->create([
                'id_producto'    => $productId,
                'id_usuario'     => $userId,
                'tipo'           => 'SALIDA',
                'cantidad'       => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'motivo'         => $motivo ?: 'Salida de mercancía / Merma o uso interno'
            ]);

            // Si el nuevo stock es menor o igual al mínimo, disparar alerta
            if ($stockNuevo <= (int)$producto['stock_minimo']) {
                $this->alertaModel->create(
                    $productId,
                    $stockNuevo,
                    (int)$producto['stock_minimo'],
                    "Stock crítico: '{$producto['nombre']}' tiene {$stockNuevo} unidades (mínimo {$producto['stock_minimo']})"
                );
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Salida de inventario registrada con éxito.', 'stock_nuevo' => $stockNuevo];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al registrar salida: ' . $e->getMessage()];
        }
    }

    public function registrarAjuste(int $productId, int $stockFisico, int $userId, ?string $motivo = null): array {
        if ($stockFisico < 0) {
            return ['success' => false, 'message' => 'El stock físico no puede ser negativo.'];
        }

        try {
            $this->db->beginTransaction();

            $producto = $this->productoModel->getById($productId);
            if (!$producto) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Producto no encontrado.'];
            }

            $stockAnterior = (int)$producto['stock_actual'];
            $diferencia = $stockFisico - $stockAnterior;

            if ($diferencia === 0) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'El stock físico es igual al stock actual. No hay ajuste necesario.'];
            }

            // Actualizar stock
            $this->productoModel->updateStock($productId, $stockFisico);

            // Registrar movimiento
            $this->movimientoModel->create([
                'id_producto'    => $productId,
                'id_usuario'     => $userId,
                'tipo'           => 'AJUSTE',
                'cantidad'       => abs($diferencia),
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockFisico,
                'motivo'         => $motivo ?: ("Ajuste de inventario: " . ($diferencia > 0 ? "+{$diferencia}" : "{$diferencia}"))
            ]);

            // Gestionar alertas
            if ($stockFisico <= (int)$producto['stock_minimo']) {
                $this->alertaModel->create(
                    $productId,
                    $stockFisico,
                    (int)$producto['stock_minimo'],
                    "Stock crítico tras ajuste: '{$producto['nombre']}' tiene {$stockFisico} unidades (mínimo {$producto['stock_minimo']})"
                );
            } else {
                $this->alertaModel->resolveByProductIfRestocked($productId, $stockFisico);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Ajuste de inventario realizado con éxito.', 'stock_nuevo' => $stockFisico];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al registrar ajuste: ' . $e->getMessage()];
        }
    }

    public function actualizarProductoConPrecio(int $id, array $data, int $userId): array {
        try {
            $this->db->beginTransaction();

            $producto = $this->productoModel->getById($id);
            if (!$producto) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Producto no encontrado.'];
            }

            $precioAnterior = (float)$producto['precio'];
            $nuevoPrecio = (float)$data['precio'];

            // Actualizar producto
            $this->productoModel->update($id, $data);

            // Si el precio cambió, guardar en historial
            if (abs($precioAnterior - $nuevoPrecio) > 0.001) {
                $this->historialPrecioModel->create($id, $userId, $precioAnterior, $nuevoPrecio);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Producto actualizado correctamente.'];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error al actualizar producto: ' . $e->getMessage()];
        }
    }
}
