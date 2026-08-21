/**
 * Módulo POS (Punto de Venta) Interactivo
 */

class PosApp {
    constructor() {
        this.cart = [];
        this.clientSelect = document.getElementById('pos-cliente-select');
        this.cartItemsContainer = document.getElementById('pos-cart-items');
        this.subtotalEl = document.getElementById('pos-subtotal');
        this.totalEl = document.getElementById('pos-total');
        this.cashInput = document.getElementById('pos-cash-received');
        this.changeEl = document.getElementById('pos-change');
        this.btnFinish = document.getElementById('pos-btn-finish');
        this.searchProductInput = document.getElementById('pos-search-product');
        this.emptyCartMessage = document.getElementById('pos-empty-cart');
        
        this.initEvents();
    }

    initEvents() {
        const self = this;

        // Búsqueda de productos en el grid
        if (this.searchProductInput) {
            this.searchProductInput.addEventListener('keyup', function(e) {
                const query = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.pos-product-item');
                
                cards.forEach(card => {
                    const name = card.getAttribute('data-nombre').toLowerCase();
                    const code = card.getAttribute('data-codigo').toLowerCase();
                    if (name.includes(query) || code.includes(query)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Si presiona ENTER y coincide exactamente con un código, agregarlo de inmediato
                if (e.key === 'Enter' && query) {
                    const exactMatch = Array.from(cards).find(c => c.getAttribute('data-codigo').toLowerCase() === query);
                    if (exactMatch) {
                        exactMatch.click();
                        self.searchProductInput.value = '';
                    }
                }
            });
        }

        // Clic en tarjetas de productos
        document.querySelectorAll('.pos-product-item').forEach(card => {
            card.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                const code = this.getAttribute('data-codigo');
                const name = this.getAttribute('data-nombre');
                const price = parseFloat(this.getAttribute('data-precio'));
                const stock = parseInt(this.getAttribute('data-stock'));

                self.addToCart({ id, code, name, price, stock });
            });
        });

        // Cálculo de cambio / vuelto
        if (this.cashInput) {
            this.cashInput.addEventListener('input', function() {
                self.calculateChange();
            });
        }

        // Botón Finalizar Venta
        if (this.btnFinish) {
            this.btnFinish.addEventListener('click', function() {
                self.submitSale();
            });
        }
    }

    addToCart(product) {
        if (product.stock <= 0) {
            alert(`El producto '${product.name}' no tiene stock disponible.`);
            return;
        }

        const existing = this.cart.find(item => item.id === product.id);

        if (existing) {
            if (existing.cantidad + 1 > product.stock) {
                alert(`No hay más existencias disponibles de '${product.name}'. Stock máximo: ${product.stock}`);
                return;
            }
            existing.cantidad += 1;
            existing.subtotal = existing.cantidad * existing.precio;
        } else {
            this.cart.push({
                id: product.id,
                codigo: product.codigo,
                nombre: product.name,
                precio: product.price,
                stock: product.stock,
                cantidad: 1,
                subtotal: product.price
            });
        }

        this.renderCart();
    }

    updateQuantity(productId, newQty) {
        const item = this.cart.find(i => i.id === productId);
        if (!item) return;

        newQty = parseInt(newQty);
        if (isNaN(newQty) || newQty <= 0) {
            this.removeFromCart(productId);
            return;
        }

        if (newQty > item.stock) {
            alert(`Stock máximo disponible para '${item.nombre}': ${item.stock}`);
            newQty = item.stock;
        }

        item.cantidad = newQty;
        item.subtotal = item.cantidad * item.precio;
        this.renderCart();
    }

    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.renderCart();
    }

    clearCart() {
        this.cart = [];
        this.renderCart();
    }

    renderCart() {
        if (!this.cartItemsContainer) return;

        this.cartItemsContainer.innerHTML = '';

        if (this.cart.length === 0) {
            if (this.emptyCartMessage) this.emptyCartMessage.style.display = 'block';
            if (this.btnFinish) this.btnFinish.disabled = true;
            this.subtotalEl.innerText = formatMoney(0);
            this.totalEl.innerText = formatMoney(0);
            this.calculateChange();
            return;
        }

        if (this.emptyCartMessage) this.emptyCartMessage.style.display = 'none';
        if (this.btnFinish) this.btnFinish.disabled = false;

        let total = 0;

        this.cart.forEach(item => {
            total += item.subtotal;

            const tr = document.createElement('div');
            tr.className = 'd-flex align-items-center justify-content-between p-2 mb-2 bg-light rounded border';
            tr.innerHTML = `
                <div style="flex: 1; min-width: 0;" class="pe-2">
                    <div class="fw-bold text-truncate" title="${item.nombre}">${item.nombre}</div>
                    <small class="text-muted">${item.codigo} &bull; ${formatMoney(item.precio)} c/u</small>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="window.posApp.updateQuantity(${item.id}, ${item.cantidad - 1})">-</button>
                    <input type="number" class="form-control form-control-sm text-center px-1" style="width: 50px;" value="${item.cantidad}" min="1" max="${item.stock}" onchange="window.posApp.updateQuantity(${item.id}, this.value)">
                    <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="window.posApp.updateQuantity(${item.id}, ${item.cantidad + 1})">+</button>
                </div>
                <div class="text-end ps-3" style="width: 90px;">
                    <div class="fw-bold">${formatMoney(item.subtotal)}</div>
                    <button class="btn btn-link text-danger p-0 text-decoration-none" style="font-size: 0.75rem;" onclick="window.posApp.removeFromCart(${item.id})">Quitar</button>
                </div>
            `;
            this.cartItemsContainer.appendChild(tr);
        });

        this.subtotalEl.innerText = formatMoney(total);
        this.totalEl.innerText = formatMoney(total);
        this.calculateChange();
    }

    calculateChange() {
        if (!this.cashInput || !this.changeEl) return;
        const total = this.cart.reduce((sum, i) => sum + i.subtotal, 0);
        const cash = parseFloat(this.cashInput.value) || 0;
        const change = cash - total;

        if (change >= 0) {
            this.changeEl.innerText = formatMoney(change);
            this.changeEl.className = 'fw-bold text-success';
        } else {
            this.changeEl.innerText = '-' + formatMoney(Math.abs(change));
            this.changeEl.className = 'fw-bold text-danger';
        }
    }

    async submitSale() {
        if (this.cart.length === 0) {
            alert('El carrito está vacío.');
            return;
        }

        const clientId = this.clientSelect ? this.clientSelect.value : '';
        const payload = {
            id_cliente: clientId || null,
            items: this.cart.map(i => ({
                id_producto: i.id,
                cantidad: i.cantidad,
                precio: i.precio
            }))
        };

        this.btnFinish.disabled = true;
        this.btnFinish.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

        try {
            const response = await fetch(window.APP_URL_VENTAS_STORE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                alert(`¡Venta #${data.id_venta} registrada con éxito!`);
                window.location.href = `${window.APP_URL_VENTAS_SHOW}?id=${data.id_venta}`;
            } else {
                alert('Error al registrar venta: ' + data.message);
                this.btnFinish.disabled = false;
                this.btnFinish.innerHTML = '<i class="bi bi-check2-circle"></i> Confirmar y Cobrar';
            }
        } catch (error) {
            alert('Error de conexión con el servidor.');
            this.btnFinish.disabled = false;
            this.btnFinish.innerHTML = '<i class="bi bi-check2-circle"></i> Confirmar y Cobrar';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('pos-container')) {
        window.posApp = new PosApp();
    }
});
