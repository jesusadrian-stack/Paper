/**
 * JavaScript Principal de la Aplicación
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // 2. Confirmation dialogs
    document.querySelectorAll('.btn-confirm').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || '¿Está seguro de realizar esta acción?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // 3. Filtro en vivo para tablas
    const searchInputs = document.querySelectorAll('.table-search-input');
    searchInputs.forEach(function(input) {
        const tableId = input.getAttribute('data-table');
        const targetTable = document.getElementById(tableId);
        if (!targetTable) return;

        input.addEventListener('keyup', function() {
            const filterValue = this.value.toLowerCase().trim();
            const rows = targetTable.querySelectorAll('tbody tr');

            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});

function formatMoney(amount) {
    return '$ ' + new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}
