@php
    use App\Utils\AccountingUtil;
    $totalArusKas = 0;
    // Assuming $saldoKas is passed from controller or calculated here
    $tahun = $tahun ?? date('Y');
    $bulanLalu = intval($bulan) - 1;
    $saldoKas = AccountingUtil::saldoKas($tahun, $bulanLalu);
@endphp

@extends('finance.reports.pdf.layout')

@section('content')
    <table style="width: 100%; border: 0;">
        <tr style="background-color: #b0b0b0; font-weight: bold;">
            <td style="border: 0; width: 70%;" colspan="2">Nama Akun</td>
            <td style="border: 0; width: 30%; text-align: right;">Saldo</td>
        </tr>
        <tr>
            <td colspan="3" style="height: 5px; border: 0;"></td>
        </tr>

        @foreach ($dataArusKas as $index => $ak)
            @if ($ak['header'])
                <tr style="background-color: #d0d0d0; font-weight: bold;">
                    <td style="border: 0; width: 5%;">{{ $index + 1 }}</td>
                    <td style="border: 0;">{{ $ak['header']->nama_akun }}</td>
                    <td style="border: 0; text-align: right;">
                        @if ($index == 0)
                            {{ number_format($saldoKas, 2) }}
                        @endif
                    </td>
                </tr>
            @endif

            @php
                $grandTotal = [];
            @endphp
            @foreach ($ak['groups'] as $indexGroup => $group)
                <tr>
                    <td colspan="3" style="height: 3px; border: 0;"></td>
                </tr>

                @if ($group['subheader'])
                    <tr style="background-color: #f0f0f0; font-style: italic;">
                        <td style="border: 0;"></td>
                        <td style="border: 0;">{{ $group['subheader']->nama_akun }}</td>
                        <td style="border: 0;"></td>
                    </tr>
                @endif

                @php
                    $total = 0;
                @endphp
                @foreach ($group['items'] as $item)
                    <tr>
                        <td style="border: 0;"></td>
                        <td style="border: 0;">{{ $item->nama_akun }}</td>
                        <td style="border: 0; text-align: right;">{{ number_format($item->total, 2) }}</td>
                    </tr>

                    @php
                        $total += $item->total;
                    @endphp
                @endforeach

                @php
                    $titleJumlah = $ak['header']->nama_akun;
                    if ($group['subheader']) {
                        $titleJumlah = $group['subheader']->nama_akun;
                    }
                @endphp

                @if (strtolower($titleJumlah) != 'pengeluaran')
                    @php
                        $grandTotal[$indexGroup] = $total;
                    @endphp
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td style="border: 0;"></td>
                        <td style="border: 0;">Jumlah {{ $titleJumlah }}</td>
                        <td style="border: 0; text-align: right;">{{ number_format($total, 2) }}</td>
                    </tr>
                @endif
            @endforeach

            @if ($index > 0)
                @php
                    $totalBawah = 0;
                    foreach ($grandTotal as $indexGrandTotal => $jumlahBawah) {
                        if ($indexGrandTotal == 0) {
                            $totalBawah += $jumlahBawah;
                        } else {
                            $totalBawah -= $jumlahBawah;
                        }
                    }

                    $totalArusKas += $totalBawah;
                @endphp

                <tr style="background-color: #d0d0d0; font-weight: bold;">
                    <td style="border: 0;"></td>
                    <td style="border: 0;">
                        @if ($index == 1)
                            Kas Bersih yang diperoleh dari aktivitas Operasi (A-B-C)
                        @elseif ($index == 2)
                            Kas Bersih yang diperoleh dari aktivitas Investasi (A-B)
                        @elseif ($index == 3)
                            Kas Bersih yang diperoleh dari aktivitas Pendanaan (A-B)
                        @endif
                    </td>
                    <td style="border: 0; text-align: right;">{{ number_format($totalBawah, 2) }}</td>
                </tr>
            @endif

            <tr>
                <td colspan="3" style="height: 10px; border: 0;"></td>
            </tr>
        @endforeach

        <tr style="background-color: #b0b0b0; font-weight: bold;">
            <td style="border: 0;"></td>
            <td style="border: 0;">Kenaikan (Penurunan) Kas</td>
            <td style="border: 0; text-align: right;">{{ number_format($totalArusKas, 2) }}</td>
        </tr>
        <tr style="background-color: #b0b0b0; font-weight: bold;">
            <td style="border: 0;"></td>
            <td style="border: 0;">SALDO AKHIR KAS SETARA KAS</td>
            <td style="border: 0; text-align: right;">{{ number_format($totalArusKas + $saldoKas, 2) }}</td>
        </tr>
    </table>
@endsection
