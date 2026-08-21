<?php
require_once __DIR__ . '/../Config/database.php';

class ReporteService {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getReporteVentas(?string $fechaInicio = null, ?string $fechaFin = null, ?int $userId = null): array {
        $sql = "SELECT v.*, u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.numero_identificacion,
                       (SELECT COUNT(*) FROM detalle_venta dv WHERE dv.id_venta = v.id_venta) AS items_count 
                FROM venta v 
                JOIN usuario u ON v.id_usuario = u.id_usuario 
                LEFT JOIN cliente c ON v.id_cliente = c.id_cliente 
                WHERE 1=1 ";
        
        $params = [];
        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(v.fecha_venta) >= :fechaInicio ";
            $params['fechaInicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND DATE(v.fecha_venta) <= :fechaFin ";
            $params['fechaFin'] = $fechaFin;
        }
        if ($userId) {
            $sql .= " AND v.id_usuario = :userId ";
            $params['userId'] = $userId;
        }

        $sql .= " ORDER BY v.fecha_venta DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $ventas = $stmt->fetchAll();

        // Totales de resumen
        $totalRecaudado = 0;
        $totalVentas = count($ventas);
        foreach ($ventas as $v) {
            if ($v['estado'] === 'COMPLETADA') {
                $totalRecaudado += (float)$v['total'];
            }
        }

        return [
            'ventas'          => $ventas,
            'total_ventas'    => $totalVentas,
            'total_recaudado' => $totalRecaudado
        ];
    }

    public function getReporteInventario(?int $categoriaId = null, bool $onlyLowStock = false): array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre, 
                       (p.stock_actual * p.precio) AS valor_total_stock 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE 1=1 ";
        
        $params = [];
        if ($categoriaId) {
            $sql .= " AND p.id_categoria = :categoriaId ";
            $params['categoriaId'] = $categoriaId;
        }
        if ($onlyLowStock) {
            $sql .= " AND p.stock_actual <= p.stock_minimo ";
        }

        $sql .= " ORDER BY p.stock_actual ASC, p.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $productos = $stmt->fetchAll();

        $valorInventarioTotal = 0;
        $unidadesTotales = 0;
        $articulosBajoStock = 0;

        foreach ($productos as $p) {
            $valorInventarioTotal += (float)$p['valor_total_stock'];
            $unidadesTotales += (int)$p['stock_actual'];
            if ((int)$p['stock_actual'] <= (int)$p['stock_minimo']) {
                $articulosBajoStock++;
            }
        }

        return [
            'productos'              => $productos,
            'total_articulos'        => count($productos),
            'unidades_totales'       => $unidadesTotales,
            'valor_inventario_total' => $valorInventarioTotal,
            'articulos_bajo_stock'   => $articulosBajoStock
        ];
    }

    public function getReporteFinanzas(?string $fechaInicio = null, ?string $fechaFin = null): array {
        $sql = "SELECT mc.*, c.nombre AS cuenta_nombre, c.tipo AS cuenta_tipo, 
                       u.nombre_usuario, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido 
                FROM movimiento_cuenta mc 
                JOIN cuenta c ON mc.id_cuenta = c.id_cuenta 
                JOIN usuario u ON mc.id_usuario = u.id_usuario 
                WHERE 1=1 ";
        
        $params = [];
        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(mc.fecha_movimiento) >= :fechaInicio ";
            $params['fechaInicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND DATE(mc.fecha_movimiento) <= :fechaFin ";
            $params['fechaFin'] = $fechaFin;
        }

        $sql .= " ORDER BY mc.fecha_movimiento DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $movimientos = $stmt->fetchAll();

        $totalIngresos = 0;
        $totalEgresos = 0;
        $totalDepositos = 0;
        $totalRetiros = 0;

        foreach ($movimientos as $m) {
            $val = (float)$m['valor'];
            switch ($m['tipo']) {
                case 'INGRESO':  $totalIngresos += $val; break;
                case 'EGRESO':   $totalEgresos += $val; break;
                case 'DEPOSITO': $totalDepositos += $val; break;
                case 'RETIRO':   $totalRetiros += $val; break;
            }
        }

        return [
            'movimientos'     => $movimientos,
            'total_ingresos'  => $totalIngresos,
            'total_egresos'   => $totalEgresos,
            'total_depositos' => $totalDepositos,
            'total_retiros'   => $totalRetiros
        ];
    }
}
