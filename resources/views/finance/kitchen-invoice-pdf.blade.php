<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Form Permintaan Bahan Baku</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            text-transform: uppercase;
            font-size: 16px;
            margin: 0;
            letter-spacing: 1px;
        }

        .header h2 {
            text-transform: uppercase;
            font-size: 14px;
            margin: 5px 0;
        }

        .header p {
            font-size: 10px;
            margin: 2px 0;
            font-style: italic;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }

        .po-number {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #e2efda;
            border: 1px solid #000;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
        }

        td {
            border: 1px solid #000;
            padding: 6px 5px;
            vertical-align: middle;
        }

        .col-no {
            width: 30px;
            text-align: center;
        }

        .col-desc {
            width: auto;
        }

        .col-qty {
            width: 60px;
            text-align: center;
        }

        .col-price {
            width: 90px;
            text-align: right;
        }

        .col-total {
            width: 100px;
            text-align: right;
        }

        .col-note {
            width: 150px;
            font-size: 10px;
        }

        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .currency {
            float: left;
        }

        .amount {
            float: right;
        }

        .footer {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $dapur->name }}</h1>
        <h2>YAYASAN MBG</h2>
        <p>{{ $dapur->address }}, {{ $dapur->city }}, {{ $dapur->province }}</p>
    </div>

    <div class="po-number">
        {{ $po->po_number }}
    </div>

    <div class="title">
        INVOICE DAPUR (KONSOLIDASI)
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Dari</span>
            <span>: {{ $dapur->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kepada</span>
            <span>: Yayasan MBG</span>
        </div>
        <div class="info-row">
            <span class="info-label">Waktu</span>
            <span>: {{ optional($po->submitted_at)->format('d F Y') ?? now()->format('d F Y') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Uraian Jenis Bahan Makanan</th>
                <th>Kuantitas</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($items as $index => $item)
                @php
                    $price = $item->estimated_unit_price;
                    $lineTotal = $item->quantity_to_order * $price;
                    $grandTotal += $lineTotal;
                @endphp
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-desc">{{ $item->material->name }}</td>
                    <td class="col-qty">{{ number_format($item->quantity_to_order, 0, ',', '.') }} {{ $item->unit }}
                    </td>
                    <td class="col-price">
                        <span class="currency">Rp</span>
                        <span class="amount">{{ number_format($price, 0, ',', '.') }}</span>
                    </td>
                    <td class="col-total">
                        <span class="currency">Rp</span>
                        <span class="amount">{{ number_format($lineTotal, 0, ',', '.') }}</span>
                    </td>
                    <td class="col-note">{{ $item->material->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: center;">TOTAL</td>
                <td class="col-total">
                    <span class="currency">Rp</span>
                    <span class="amount">{{ number_format($grandTotal, 0, ',', '.') }}</span>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
