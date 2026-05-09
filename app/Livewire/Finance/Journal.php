<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Dapur;
use App\Models\Payment;
use App\Utils\AccountingUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class Journal extends Component
{
    use WithFileUploads;

    // Form fields
    public $tanggal_transaksi;

    public $jenis_transaksi = 'aset_keluar'; // default

    public $sumber_dana; // Account ID

    public $disimpan_ke; // Account ID

    public $keterangan;

    public $nominal;

    public $relasi; // Free text reference

    public $dapur_id;

    public $saldo = 0;

    public $showDapurSelector = true;

    public function mount()
    {
        $this->tanggal_transaksi = now()->format('Y-m-d');

        $userDapurId = auth()->user()->dapur_id;
        $allDapurs = Dapur::all();

        if ($userDapurId) {
            // Priority 1: User is linked to a specific Dapur
            $this->dapur_id = $userDapurId;
            $this->showDapurSelector = false;
        } elseif ($allDapurs->count() === 1) {
            // Priority 2: Only one Dapur exists in the system
            $this->dapur_id = $allDapurs->first()->id;
            $this->showDapurSelector = false;
        } else {
            // Priority 3: Multiple Dapurs exist, admin must select
            $this->dapur_id = $allDapurs->first()?->id;
            $this->showDapurSelector = true;
        }
    }

    public function updatedDapurId()
    {
        $this->sumber_dana = null;
        $this->disimpan_ke = null;
        $this->relasi = null;
    }

    public function updatedJenisTransaksi()
    {
        $this->relasi = null;
        $this->sumber_dana = null;
        $this->disimpan_ke = null;
        $this->saldo = 0;
    }

    public function updatedSumberDana()
    {
        $this->calculateSaldo();
    }

    public function updatedDisimpanKe()
    {
        $this->calculateSaldo();
    }

    public function updatedTanggalTransaksi()
    {
        $this->calculateSaldo();
    }

    public function calculateSaldo()
    {
        $this->saldo = 0;
        $accountId = null;

        // Determine which account to track
        if ($this->jenis_transaksi === 'aset_masuk') {
            $accountId = $this->disimpan_ke;
        } else {
            // aset_keluar or pemindahan_saldo
            $accountId = $this->sumber_dana;
        }

        if (! $accountId || ! $this->tanggal_transaksi) {
            return;
        }

        $account = Account::with(['balance' => function ($q) {
            $tahun = date('Y', strtotime($this->tanggal_transaksi));
            $q->where('tahun', $tahun);
        }])->find($accountId);

        if (! $account || ! $account->balance) {
            return;
        }

        $bulan = (int) date('m', strtotime($this->tanggal_transaksi));
        $this->saldo = AccountingUtil::sumSaldo($account, $bulan);
    }

    public function save()
    {
        $this->validate([
            'tanggal_transaksi' => 'required|date',
            'jenis_transaksi' => 'required|in:aset_masuk,aset_keluar,pemindahan_saldo',
            'sumber_dana' => 'required|exists:accounts,id',
            'disimpan_ke' => 'required|exists:accounts,id',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $sourceAccount = Account::findOrFail($this->sumber_dana);
            $destAccount = Account::findOrFail($this->disimpan_ke);

            // Determine Debit/Kredit codes based on Type
            $rekening_debit = '';
            $rekening_kredit = '';
            $mappedType = 'lainnya';

            if ($this->jenis_transaksi === 'aset_masuk') {
                // Asset In: Debit Cash/Bank (Dest), Credit Revenue (Source)
                $rekening_debit = $destAccount->kode;
                $rekening_kredit = $sourceAccount->kode;
                $mappedType = 'pendapatan_dana';
            } elseif ($this->jenis_transaksi === 'aset_keluar') {
                // Asset Out: Debit Expense (Dest), Credit Cash/Bank (Source)
                $rekening_debit = $destAccount->kode;
                $rekening_kredit = $sourceAccount->kode;
                $mappedType = 'operasional_dapur';
            } else {
                // Transfer: Debit Target (Dest), Credit Source (Source)
                $rekening_debit = $destAccount->kode;
                $rekening_kredit = $sourceAccount->kode;
                $mappedType = 'lainnya';
            }

            $payment = Payment::create([
                'dapur_id' => $this->dapur_id,
                'user_id' => auth()->id(),
                'no_pembayaran' => 'PYM-'.now()->format('YmdHis').'-'.rand(100, 999),
                'tanggal_pembayaran' => $this->tanggal_transaksi,
                'jenis_transaksi' => $mappedType,
                'no_referensi' => $this->relasi,
                'total_harga' => $this->nominal,
                'catatan' => $this->keterangan,
                'payment_proof' => null,
                'rekening_debit' => $rekening_debit,
                'rekening_kredit' => $rekening_kredit,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            session()->flash('success', 'Jurnal berhasil disimpan.');

            return redirect()->route('finance.journal.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan jurnal: '.$e->getMessage());
        }
    }

    public function render()
    {
        $baseAccounts = Account::where('dapur_id', $this->dapur_id)
            ->orderBy('kode')
            ->get();

        $sourceOptions = [];
        $targetOptions = [];

        // Debugging state
        $debug = [
            'jenis' => $this->jenis_transaksi,
            'dapur' => $this->dapur_id,
            'total_base' => $baseAccounts->count(),
        ];

        foreach ($baseAccounts as $acc) {
            $kode = trim($acc->kode);
            $option = [
                'value' => (string) $acc->id,
                'label' => $acc->kode.' - '.$acc->nama,
                'kode' => $kode,
            ];

            $canBeSource = true;
            $canBeTarget = true;

            switch ($this->jenis_transaksi) {
                case 'aset_masuk':
                    // Source: NOT Asset
                    if (str_starts_with($kode, '1.1') || str_starts_with($kode, '1.2') || str_starts_with($kode, '1.3')) {
                        $canBeSource = false;
                    }
                    break;

                case 'aset_keluar':
                    // Target: NOT Asset
                    if (str_starts_with($kode, '1.1') || str_starts_with($kode, '1.2') || str_starts_with($kode, '1.3')) {
                        $canBeTarget = false;
                    }
                    break;

                case 'pemindahan_saldo':
                    // Target: NOT Inventory (1.1.03)
                    if (str_starts_with($kode, '1.1.03')) {
                        $canBeTarget = false;
                    }
                    break;
            }

            if ($canBeSource) {
                $sourceOptions[] = $option;
            }
            if ($canBeTarget) {
                $targetOptions[] = $option;
            }
        }

        // Logging for verification
        Log::info('Journal Render Logic', [
            'jenis' => $this->jenis_transaksi,
            'source_count' => count($sourceOptions),
            'target_count' => count($targetOptions),
        ]);

        // Determine showRelasi
        $showRelasi = false;
        $sourceAcc = $baseAccounts->firstWhere('id', $this->sumber_dana);
        $targetAcc = $baseAccounts->firstWhere('id', $this->disimpan_ke);

        if (in_array($this->jenis_transaksi, ['aset_masuk', 'pemindahan_saldo'])) {
            if ($targetAcc && (str_starts_with($targetAcc->kode, '1.1.01') || str_starts_with($targetAcc->kode, '1.1.02'))) {
                $showRelasi = true;
            }
        } elseif ($this->jenis_transaksi === 'aset_keluar') {
            if ($sourceAcc && str_starts_with($sourceAcc->kode, '1.1.01')) {
                $showRelasi = true;
            }
        }

        // Determine showInventaris
        $showInventaris = false;
        if ($targetAcc && (str_starts_with($targetAcc->kode, '1.2.01') || str_starts_with($targetAcc->kode, '1.2.02') || str_starts_with($targetAcc->kode, '1.2.03'))) {
            $showInventaris = true;
        }

        $dapurOptions = Dapur::all()->map(fn ($d) => ['value' => (string) $d->id, 'label' => $d->name])->values()->toArray();

        $jenisTransaksiOptions = [
            ['value' => 'aset_masuk', 'label' => 'Aset Masuk'],
            ['value' => 'aset_keluar', 'label' => 'Aset Keluar'],
            ['value' => 'pemindahan_saldo', 'label' => 'Pemindahan Saldo'],
        ];

        return view('livewire.finance.journal', [
            'sourceOptions' => collect($sourceOptions)->values()->toArray(),
            'targetOptions' => collect($targetOptions)->values()->toArray(),
            'dapurOptions' => $dapurOptions,
            'jenisTransaksiOptions' => $jenisTransaksiOptions,
            'showRelasi' => $showRelasi,
            'showInventaris' => $showInventaris,
        ])->layout('layouts.app', ['title' => 'Input Jurnal Umum']);
    }
}
