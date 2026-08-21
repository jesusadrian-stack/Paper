<?php
$pageTitle = 'Punto de Venta (POS) - Nueva Venta';
require_once VIEWS_PATH . '/Layouts/header.php';
?>

<div id="pos-container">
    <div class="row g-3">
        <!-- Columna Izquierda: Catálogo y Búsqueda de Productos -->
        <div class="col-12 col-lg-7 col-xl-8">
            <div class="card shadow-sm border-0 rounded-4 p-3 h-100">
                <!-- Barra de búsqueda e información -->
                <div class="row g-2 mb-3">
                    <div class="col-12 col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-primary"></i></span>
                            <input type="text" id="pos-search-product" class="form-control form-control-lg" placeholder="Buscar por nombre o escanear código de barras [ENTER]..." autofocus>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <select id="pos-cliente-select" class="form-select form-select-lg">
                            <option value="">-- Cliente Mostrador --</option>
                            <?php foreach ($clientes as $cl): ?>
                                <option value="<?= $cl['id_cliente'] ?>">
                                    <?= sanitize($cl['nombre'] . ' ' . ($cl['apellido'] ?? '')) ?> (<?= sanitize($cl['numero_identificacion']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Grid de productos seleccionables -->
                <div class="overflow-y-auto pe-1" style="max-height: calc(100vh - 240px);">
                    <div class="row g-3" id="pos-product-grid">
                        <?php foreach ($productos as $pr): ?>
                            <div class="col-6 col-md-4 col-xl-3 pos-product-item"
                                 data-id="<?= $pr['id_producto'] ?>"
                                 data-codigo="<?= sanitize($pr['codigo']) ?>"
                                 data-nombre="<?= sanitize($pr['nombre']) ?>"
                                 data-precio="<?= $pr['precio'] ?>"
                                 data-stock="<?= $pr['stock_actual'] ?>">
                                <div class="pos-product-card card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative p-0" style="cursor: pointer; transition: transform 0.15s ease, box-shadow 0.15s ease;">
                                    <!-- Imagen del producto -->
                                    <div class="position-relative bg-light text-center p-2" style="height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <img src="<?= getProductImage($pr) ?>" alt="<?= sanitize($pr['nombre']) ?>" style="max-height: 105px; max-width: 100%; object-fit: contain; border-radius: 8px;">
                                        <span class="position-absolute top-0 end-0 m-2 badge <?= $pr['stock_actual'] <= $pr['stock_minimo'] ? 'bg-danger' : 'bg-dark' ?> rounded-pill shadow-sm" style="font-size: 0.7rem;">
                                            <?= $pr['stock_actual'] ?> uds
                                        </span>
                                    </div>
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <code class="small text-muted d-block mb-1"><?= sanitize($pr['codigo']) ?></code>
                                            <h6 class="fw-bold text-dark mb-2" style="font-size: 0.85rem; line-height: 1.3; min-height: 34px;">
                                                <?= sanitize($pr['nombre']) ?>
                                            </h6>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                            <span class="fw-bold text-primary fs-6"><?= formatMoney($pr['precio']) ?></span>
                                            <button class="btn btn-sm btn-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Agregar al carrito">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Carrito de Compras y Cobro -->
        <div class="col-12 col-lg-5 col-xl-4">
            <div class="card shadow-sm border-0 rounded-4 p-3 pos-cart">
                <div class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cart3 text-primary me-2"></i>Carrito de Compra</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="window.posApp.clearCart()">
                        <i class="bi bi-trash"></i> Vaciar
                    </button>
                </div>

                <!-- Lista de ítems en el carrito -->
                <div class="pos-cart-items" id="pos-cart-items">
                    <div class="text-center text-muted py-5" id="pos-empty-cart">
                        <i class="bi bi-cart-x fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                        <p class="mb-0 small">Haga clic en los productos para agregarlos al pedido.</p>
                    </div>
                </div>

                <!-- Panel de Liquidación y Cobro -->
                <div class="p-3 bg-light rounded-3 border mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Subtotal:</span>
                        <span class="fw-semibold text-dark" id="pos-subtotal">$ 0,00</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-5 fw-bold text-dark">TOTAL A PAGAR:</span>
                        <span class="fs-4 fw-bold text-primary" id="pos-total">$ 0,00</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="pos-cash-received" class="form-label small text-muted mb-1">Efectivo Recibido:</label>
                            <input type="number" step="100" class="form-control form-control-sm text-end fw-bold" id="pos-cash-received" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Cambio / Vuelto:</label>
                            <div class="form-control form-control-sm text-end fw-bold bg-white" id="pos-change">$ 0,00</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-success w-100 py-2 fw-bold shadow-sm" id="pos-btn-finish" disabled>
                        <i class="bi bi-check2-circle me-1"></i> Confirmar y Cobrar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= url('js/pos.js') ?>"></script>

<?php require_once VIEWS_PATH . '/Layouts/footer.php'; ?>
