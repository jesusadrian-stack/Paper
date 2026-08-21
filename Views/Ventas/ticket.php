<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Venta #<?= $venta['id_venta'] ?> - <?= APP_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #f3f4f6;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        #ticket-print {
            width: 80mm;
            background: #fff;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-size: 13px;
            line-height: 1.3;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 3px 0;
        }
        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 15px;
        }
        @media print {
            body { background: transparent; padding: 0; }
            #ticket-print { box-shadow: none; width: 100%; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div style="width: 80mm;">
        <button class="btn-print no-print" onclick="window.print()">IMPRIMIR TICKET</button>

        <div id="ticket-print">
            <div class="text-center">
                <img src="<?= url('images/logo.jpg') ?>" alt="Logo" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; margin-bottom: 5px;">
                <h2 style="font-size: 16px; font-weight: bold;"><?= strtoupper(APP_NAME) ?></h2>
                <p>PAPELERÍA & CORRESPONSAL</p>
                <p>NIT: 900.123.456-1</p>
                <p>Tel: 300 123 4567</p>
            </div>

            <div class="divider"></div>

            <div>
                <p><strong>TICKET DE VENTA #<?= str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT) ?></strong></p>
                <p>Fecha: <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></p>
                <p>Cajero: <?= sanitize($venta['usuario_nombre']) ?></p>
                <p>Cliente: <?= sanitize($venta['cliente_nombre'] ?? 'Cliente Mostrador') ?></p>
                <?php if (!empty($venta['numero_identificacion'])): ?>
                    <p>Doc: <?= sanitize($venta['numero_identificacion']) ?></p>
                <?php endif; ?>
            </div>

            <div class="divider"></div>

            <table>
                <thead>
                    <tr>
                        <th align="left">Cant. / Art.</th>
                        <th align="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $d): ?>
                        <tr>
                            <td colspan="2"><?= sanitize($d['producto_nombre']) ?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;<?= $d['cantidad'] ?> x <?= number_format($d['precio_unitario'], 2, ',', '.') ?></td>
                            <td class="text-end fw-bold"><?= number_format($d['subtotal'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="divider"></div>

            <table>
                <tr>
                    <td class="fw-bold">SUBTOTAL:</td>
                    <td class="text-end fw-bold">$ <?= number_format($venta['subtotal'], 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="font-size: 15px;">TOTAL:</td>
                    <td class="text-end fw-bold" style="font-size: 15px;">$ <?= number_format($venta['total'], 2, ',', '.') ?></td>
                </tr>
            </table>

            <div class="divider"></div>

            <div class="text-center" style="margin-top: 10px;">
                <p>¡GRACIAS POR SU COMPRA!</p>
                <p style="font-size: 10px; margin-top: 5px;">Sistema Desarrollado con Laragon &bull; MySQL</p>
            </div>
        </div>
    </div>

    <script>
        // Auto print trigger if requested via URL
        if (window.location.search.includes('print=true')) {
            window.print();
        }
    </script>
</body>
</html>
