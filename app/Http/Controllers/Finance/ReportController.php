<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Utils\AccountingUtil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function preview(Request $request)
    {
        $laporan = $request->get('laporan');
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $periode = $request->get('periode', '-');
        $subLaporan = $request->get('sub_laporan');
        $dapur_id = $request->get('dapur_id', 'all');
        $dapurObj = $dapur_id !== 'all' ? Dapur::find($dapur_id) : null;

        if (! method_exists($this, $laporan)) {
            abort(404, "Report type '{$laporan}' not found.");
        }

        return $this->{$laporan}($tahun, $bulan, $periode, $subLaporan, $dapur_id, $dapurObj);
    }

    protected function labaRugi($tahun, $bulan, $periode, $subLaporan, $dapur_id, $dapurObj)
    {
        $labaRugi = AccountingUtil::labaRugi($tahun, $bulan, $dapur_id);
        $title = 'Laporan Laba Rugi';
        $subtitle = $this->getSubtitle($tahun, $bulan).($dapurObj ? ' - '.$dapurObj->name : ' - Laporan Yayasan (Konsolidasi)');

        $pdf = Pdf::loadView('finance.reports.pdf.laba_rugi', compact('labaRugi', 'title', 'subtitle', 'tahun', 'bulan', 'dapurObj'));

        return $pdf->stream("laporan-laba-rugi-{$tahun}-{$bulan}.pdf");
    }

    protected function neraca($tahun, $bulan, $periode, $subLaporan, $dapur_id, $dapurObj)
    {
        $akunLevel1s = AccountingUtil::neraca($tahun, $bulan, $dapur_id);
        $title = 'Laporan Neraca';
        $subtitle = $this->getSubtitle($tahun, $bulan).($dapurObj ? ' - '.$dapurObj->name : ' - Laporan Yayasan (Konsolidasi)');

        $pdf = Pdf::loadView('finance.reports.pdf.neraca', compact('akunLevel1s', 'title', 'subtitle', 'tahun', 'bulan', 'dapurObj'));

        return $pdf->stream("laporan-neraca-{$tahun}-{$bulan}.pdf");
    }

    protected function arusKas($tahun, $bulan, $periode, $subLaporan, $dapur_id, $dapurObj)
    {
        $tanggalMulai = "{$tahun}-{$bulan}-01";
        $tanggalAkhir = date('Y-m-t', strtotime($tanggalMulai));

        $dataArusKas = AccountingUtil::arusKas($tanggalMulai, $tanggalAkhir, $dapur_id);
        $title = 'Laporan Arus Kas';
        $subtitle = $this->getSubtitle($tahun, $bulan, $periode).($dapurObj ? ' - '.$dapurObj->name : ' - Laporan Yayasan (Konsolidasi)');

        $pdf = Pdf::loadView('finance.reports.pdf.arus_kas', compact('dataArusKas', 'title', 'subtitle', 'tahun', 'bulan', 'periode', 'dapurObj'));

        return $pdf->stream("laporan-arus-kas-{$tahun}-{$bulan}-{$periode}.pdf");
    }

    protected function bukuBesar($tahun, $bulan, $periode, $subLaporan, $dapur_id, $dapurObj)
    {
        if (! $subLaporan) {
            abort(400, 'Silakan pilih rekening untuk laporan Buku Besar.');
        }

        // Technically bukuBesar fetches a specific account which is uniquely tied to a dapur anyway,
        // but passing the logic down is safe.
        $data = AccountingUtil::bukuBesar($subLaporan, $tahun, $bulan, $periode);
        $title = 'Laporan Buku Besar';
        $subtitle = $this->getSubtitle($tahun, $bulan, $periode).($dapurObj ? ' - '.$dapurObj->name : ' - Laporan Yayasan (Konsolidasi)');

        $pdf = Pdf::loadView('finance.reports.pdf.buku_besar', array_merge($data, [
            'title' => $title,
            'subtitle' => $subtitle,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'periode' => $periode,
            'dapurObj' => $dapurObj,
        ]));

        return $pdf->stream("laporan-buku-besar-{$data['akun']->kode}.pdf");
    }

    protected function stokBahan($tahun, $bulan, $periode, $subLaporan, $dapur_id, $dapurObj)
    {
        $query = Stock::with(['material', 'dapur']);

        if ($dapur_id !== 'all') {
            $query->where('dapur_id', $dapur_id);
        }

        $stocks = $query->get();
        $title = 'Laporan Stok Bahan (Inventory)';
        $subtitle = 'Per Tanggal: '.date('d M Y').($dapurObj ? ' - '.$dapurObj->name : ' - Konsolidasi Yayasan');

        $pdf = Pdf::loadView('finance.reports.pdf.stok_bahan', compact('stocks', 'title', 'subtitle', 'dapurObj'));

        return $pdf->stream('laporan-stok-bahan-'.date('Ymd').'.pdf');
    }

    protected function ringkasanPengadaan($tahun, $bulan, $periode, $subLaporan, $dapur_id, $dapurObj)
    {
        $query = PurchaseOrder::with(['dapur', 'items', 'creator'])
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan);

        if ($dapur_id !== 'all') {
            $query->where('dapur_id', $dapur_id);
        }

        $pos = $query->latest()->get();
        $title = 'Laporan Ringkasan Pengadaan (Procurement Summary)';
        $subtitle = $this->getSubtitle($tahun, $bulan).($dapurObj ? ' - '.$dapurObj->name : ' - Konsolidasi Yayasan');

        $pdf = Pdf::loadView('finance.reports.pdf.ringkasan_pengadaan', compact('pos', 'title', 'subtitle', 'dapurObj'));

        return $pdf->stream("laporan-pengadaan-{$tahun}-{$bulan}.pdf");
    }

    private function getSubtitle($tahun, $bulan, $periode = '-')
    {
        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM');

        if ($periode !== '-') {
            return "Periode: Tanggal {$periode} {$namaBulan} {$tahun}";
        }

        return "Periode: {$namaBulan} {$tahun}";
    }
}
