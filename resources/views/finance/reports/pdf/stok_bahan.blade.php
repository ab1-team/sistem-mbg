@extends('finance.reports.pdf.layout')

@section('content')
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11px;">KODE</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11px;">NAMA BAHAN</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11px;">KATEGORI</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center; font-size: 11px;">STOK</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center; font-size: 11px;">SATUAN</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right; font-size: 11px;">HARGA ESTIMASI</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right; font-size: 11px;">TOTAL NILAI</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($stocks as $stock)
                @php 
                    $totalValue = $stock->current_stock * $stock->material->price_estimate; 
                    $grandTotal += $totalValue;
                @endphp
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px;">{{ $stock->material->code }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px; font-weight: bold;">{{ $stock->material->name }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px; text-transform: uppercase;">{{ str_replace('_', ' ', $stock->material->category) }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-size: 11px;">{{ number_format($stock->current_stock, 2) }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-size: 10px;">{{ $stock->material->unit }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-size: 11px;">Rp{{ number_format($stock->material->price_estimate, 0, ',', '.') }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-size: 11px; font-weight: bold;">Rp{{ number_format($totalValue, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9;">
                <td colspan="6" style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 12px;">TOTAL ESTIMASI ASET INVENTARIS:</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 12px; color: #111827;">Rp{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 30px; font-size: 10px; color: #666; font-style: italic;">
        * Nilai dihitung berdasarkan stok terakhir saat laporan ditarik dan harga estimasi bahan baku.
    </div>
@endsection
