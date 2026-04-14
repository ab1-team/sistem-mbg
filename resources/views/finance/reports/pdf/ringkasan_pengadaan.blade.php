@extends('finance.reports.pdf.layout')

@section('content')
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11px;">NO PO</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11px;">TANGGAL</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11px;">DAPUR</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right; font-size: 11px;">ESTIMASI BIAYA</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center; font-size: 11px;">STATUS</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 11px;">OLEH</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse ($pos as $po)
                @php $grandTotal += $po->total_estimated_cost; @endphp
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px; font-family: monospace; font-weight: bold;">{{ $po->po_number }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px;">{{ $po->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px;">{{ $po->dapur->name }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-size: 11px; font-weight: bold;">Rp{{ number_format($po->total_estimated_cost, 0, ',', '.') }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-size: 10px;">
                        <span style="text-transform: uppercase; font-weight: bold; color: {{ $po->status->color() }};">
                            {{ $po->status->label() }}
                        </span>
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px;">{{ $po->creator->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 20px; border: 1px solid #ddd; text-align: center; font-size: 12px; color: #666;">
                        Tidak ada aktivitas pengadaan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($pos->isNotEmpty())
            <tfoot>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="3" style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 12px;">TOTAL RINGKASAN PENGADAAN:</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 12px; color: #111827;">Rp{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td colspan="2" style="border: 1px solid #ddd;"></td>
                </tr>
            </tfoot>
        @endif
    </table>
@endsection
