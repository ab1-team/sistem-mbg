<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; }
        .container { padding: 40px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #6366f1; padding-bottom: 20px; }
        .header table { width: 100%; border-collapse: collapse; }
        .brand { font-size: 24px; font-weight: bold; color: #6366f1; letter-spacing: -1px; }
        .invoice-title { text-align: right; font-size: 18px; font-weight: bold; color: #1e293b; text-transform: uppercase; }
        
        .info-section { margin-bottom: 40px; }
        .info-section table { width: 100%; }
        .info-box { vertical-align: top; width: 50%; }
        .label { font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase; margin-bottom: 5px; }
        .value { font-size: 13px; font-weight: bold; color: #0f172a; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #f8fafc; padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; }
        .items-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .item-name { font-weight: bold; color: #0f172a; }
        .item-meta { font-size: 10px; color: #94a3b8; }
        
        .totals { margin-left: auto; width: 250px; }
        .totals table { width: 100%; }
        .total-row { padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .total-label { text-align: left; color: #64748b; font-weight: bold; }
        .total-value { text-align: right; font-weight: bold; color: #0f172a; }
        .grand-total { padding: 15px 0; color: #6366f1; font-size: 16px; }

        .footer { position: fixed; bottom: 40px; left: 40px; right: 40px; font-size: 10px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td class="brand">Yayasan MBG</td>
                    <td class="invoice-title">TAGIHAN (INVOICE)</td>
                </tr>
            </table>
        </div>

        <div class="info-section">
            <table>
                <tr>
                    <td class="info-box">
                        <div class="label">Dari (Supplier)</div>
                        <div class="value">{{ $invoice->supplier->name }}</div>
                        <div style="font-size: 11px; color: #475569;">{{ $invoice->supplier->address ?? '-' }}</div>
                    </td>
                    <td class="info-box" style="text-align: right;">
                        <div class="label">Untuk (Dapur)</div>
                        <div class="value">{{ $invoice->dapur->name }}</div>
                        <div style="font-size: 11px; color: #475569;">{{ $invoice->invoice_number }}</div>
                        <div style="font-size: 11px; color: #475569;">Jatuh Tempo: {{ $invoice->due_date->format('d M Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Bahan Baku</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->material->name }}</div>
                            <div class="item-meta">Ref PO: {{ $invoice->purchaseOrder->po_number }}</div>
                        </td>
                        <td style="text-align: center;">
                            <div class="value">{{ number_format($item->quantity, 2) }}</div>
                            <div class="item-meta">{{ $item->poItem->unit }}</div>
                        </td>
                        <td style="text-align: right;">
                            <div class="value">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        </td>
                        <td style="text-align: right;">
                            <div class="value">Rp {{ number_format($item->total_price, 0, ',', '.') }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td class="total-label">Subtotal</td>
                    <td class="total-value">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="total-label">Pajak (0%)</td>
                    <td class="total-value">Rp 0</td>
                </tr>
                <tr class="grand-total">
                    <td class="total-label" style="color: #6366f1;">Total Bayar</td>
                    <td class="total-value" style="color: #6366f1;">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Dokumen ini diterbitkan secara otomatis oleh Sistem ERP Yayasan MBG dan sah sebagai bukti penagihan.</p>
            <p>&copy; {{ date('Y') }} Yayasan MBG. Semua hak cipta dilindungi.</p>
        </div>
    </div>
</body>
</html>
