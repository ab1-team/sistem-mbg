<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Dapur;
use Livewire\Component;

class Reporting extends Component
{
    public $dapur_id = 'all';

    public $tahun;

    public $bulan;

    public $periode = '-';

    public $jenis_laporan = '';

    public $sub_laporan = '';

    // UI State
    public $daftarAkun = [];

    public $opsiTahun = [];

    public $opsiBulan = [];

    public $opsiDapur = [];

    public $showDapurSelector = true;

    public $opsiHari = [];

    public $opsiLaporan = [];

    public function mount()
    {
        $this->tahun = date('Y');
        $this->bulan = date('m');
        $this->loadDaftarAkun();

        $allDapurs = Dapur::all();
        $this->opsiDapur = $allDapurs->map(fn ($d) => ['value' => (string) $d->id, 'label' => $d->name])->toArray();
        array_unshift($this->opsiDapur, ['value' => 'all', 'label' => 'Semua Dapur (Konsolidasi Yayasan)']);

        if (auth()->user()->dapur_id) {
            $this->dapur_id = auth()->user()->dapur_id;
            $this->showDapurSelector = false;
        } elseif ($allDapurs->count() === 1) {
            $this->dapur_id = (string) $allDapurs->first()->id;
            $this->showDapurSelector = false;
        } else {
            $this->showDapurSelector = true;
        }

        // Format options for searchable select
        $this->opsiTahun = collect(range(date('Y') - 5, date('Y') + 1))
            ->map(fn ($y) => ['value' => $y, 'label' => (string) $y])->toArray();

        $this->opsiBulan = collect(range(1, 12))->map(fn ($m) => [
            'value' => str_pad($m, 2, '0', STR_PAD_LEFT),
            'label' => date('F', mktime(0, 0, 0, $m, 1)),
        ])->toArray();

        $this->opsiHari = collect(range(1, 31))->map(fn ($d) => [
            'value' => str_pad($d, 2, '0', STR_PAD_LEFT),
            'label' => (string) $d,
        ])->prepend(['value' => '-', 'label' => 'Semua Tanggal'])->toArray();

        $this->opsiLaporan = [
            ['value' => 'labaRugi', 'label' => 'Laporan Laba Rugi (Income Statement)'],
            ['value' => 'neraca', 'label' => 'Laporan Neraca (Balance Sheet)'],
            ['value' => 'arusKas', 'label' => 'Laporan Arus Kas (Cash Flow)'],
            ['value' => 'bukuBesar', 'label' => 'Laporan Buku Besar (General Ledger)'],
        ];
    }

    public function updatedDapurId()
    {
        $this->loadDaftarAkun();
        $this->sub_laporan = '';
    }

    public function loadDaftarAkun()
    {
        if ($this->dapur_id !== 'all') {
            $this->daftarAkun = Account::with('dapur')->where('dapur_id', $this->dapur_id)->orderBy('kode')->get();
        } else {
            $this->daftarAkun = Account::with('dapur')->orderBy('kode')->get();
        }
    }

    public function updatedJenisLaporan()
    {
        // Reset sub_laporan setiap kali jenis laporan diganti
        $this->sub_laporan = '';

        // Jika Laba Rugi atau Neraca, reset periode ke bulanan (-)
        if (in_array($this->jenis_laporan, ['labaRugi', 'neraca'])) {
            $this->periode = '-';
        }
    }

    public function openReport()
    {
        if (! $this->jenis_laporan) {
            return;
        }

        $params = http_build_query([
            'laporan' => $this->jenis_laporan,
            'dapur_id' => $this->dapur_id,
            'tahun' => $this->tahun,
            'bulan' => $this->bulan,
            'periode' => $this->periode,
            'sub_laporan' => $this->sub_laporan,
        ]);

        $this->dispatch('open-new-tab', url: route('finance.reports.preview').'?'.$params);
    }

    public function render()
    {
        return view('livewire.finance.reporting')->layout('layouts.app');
    }
}
