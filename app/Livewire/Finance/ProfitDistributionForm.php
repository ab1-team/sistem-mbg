<?php

namespace App\Livewire\Finance;

use App\Models\Dapur;
use App\Models\DividendDistribution;
use App\Models\Investor;
use App\Models\Payment;
use App\Models\Period;
use App\Models\ProfitCalculation;
use App\Models\Setting;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProfitDistributionForm extends Component
{
    // Form Fields
    public $tanggal_distribusi;

    public $period_id;

    public $dapur_id;

    public $net_profit = 0;

    public $yayasan_share = 0;

    public $investor_pool = 0;

    public $investor_distributions = []; // investor_id => amount

    // UI States
    public $dapurOptions = [];

    public $periodOptions = [];

    public $investors = [];

    public $investor_share_percentage = 80;

    public $yayasan_share_percentage = 20;

    public function mount()
    {
        $this->tanggal_distribusi = date('Y-m-d');
        $this->investor_share_percentage = Setting::get('profit_share_investor', 80);
        $this->yayasan_share_percentage = Setting::get('profit_share_yayasan', 20);

        $this->dapurOptions = Dapur::all()->map(fn ($d) => ['value' => $d->id, 'label' => $d->name])->toArray();
        $this->periodOptions = Period::orderBy('year', 'desc')->orderBy('month', 'desc')
            ->get()
            ->map(fn ($p) => ['value' => $p->id, 'label' => $p->name.' ('.$p->status.')'])
            ->toArray();

        $this->investors = Investor::where('is_active', true)->get();

        foreach ($this->investors as $investor) {
            $this->investor_distributions[$investor->id] = 0;
        }

        if (count($this->dapurOptions) === 1) {
            $this->dapur_id = $this->dapurOptions[0]['value'];
        }
    }

    public function updatedPeriodId()
    {
        if ($this->period_id && $this->dapur_id) {
            $this->calculateFromPeriod();
        }
    }

    public function updatedDapurId()
    {
        if ($this->period_id && $this->dapur_id) {
            $this->calculateFromPeriod();
        }
    }

    public function updatedNetProfit()
    {
        $this->net_profit = (float) str_replace(['.', ','], ['', '.'], $this->net_profit);
        $this->recalculateSplits();
    }

    protected function calculateFromPeriod()
    {
        $period = Period::find($this->period_id);
        if (! $period) {
            return;
        }

        // Using similar logic to ProfitDistributionService but just for UI preview
        $totalRevenue = Payment::where('dapur_id', $this->dapur_id)
            ->whereBetween('tanggal_pembayaran', [$period->start_date, $period->end_date])
            ->where('jenis_transaksi', 'pendapatan_dana')
            ->sum('total_harga');

        $totalCogs = Payment::where('dapur_id', $this->dapur_id)
            ->whereBetween('tanggal_pembayaran', [$period->start_date, $period->end_date])
            ->where('jenis_transaksi', 'pembelian_bahan')
            ->sum('total_harga');

        $totalOtherExpenses = Payment::where('dapur_id', $this->dapur_id)
            ->whereBetween('tanggal_pembayaran', [$period->start_date, $period->end_date])
            ->whereIn('jenis_transaksi', ['operasional_dapur', 'gaji_staf', 'lainnya'])
            ->sum('total_harga');

        $grossProfit = $totalRevenue - $totalCogs;
        $this->net_profit = $grossProfit - $totalOtherExpenses;

        $this->recalculateSplits();
    }

    public function recalculateSplits()
    {
        if ($this->net_profit > 0) {
            $this->yayasan_share = $this->net_profit * ($this->yayasan_share_percentage / 100);
            $this->investor_pool = $this->net_profit * ($this->investor_share_percentage / 100);

            foreach ($this->investors as $investor) {
                $this->investor_distributions[$investor->id] = $this->investor_pool * ($investor->share_percentage / 100);
            }
        } else {
            $this->yayasan_share = 0;
            $this->investor_pool = 0;
            foreach ($this->investors as $investor) {
                $this->investor_distributions[$investor->id] = 0;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'dapur_id' => 'required',
            'tanggal_distribusi' => 'required|date',
            'net_profit' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () {
                // 1. Create Profit Calculation
                $calculation = ProfitCalculation::create([
                    'dapur_id' => $this->dapur_id,
                    'period_id' => $this->period_id ?: null,
                    'net_profit' => $this->net_profit,
                    'yayasan_share' => $this->yayasan_share,
                    'investor_total_share' => $this->investor_pool,
                    'status' => 'final',
                    'calculated_by' => auth()->id(),
                ]);

                // 2. Create Dividend Distributions & Wallet Updates & Journal Entries
                foreach ($this->investors as $investor) {
                    $amount = $this->investor_distributions[$investor->id] ?? 0;
                    if ($amount <= 0) {
                        continue;
                    }

                    DividendDistribution::create([
                        'profit_calculation_id' => $calculation->id,
                        'investor_id' => $investor->id,
                        'share_percentage' => $investor->share_percentage,
                        'amount' => $amount,
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    // Credit Wallet
                    $this->creditWallet($investor, $amount, 'Bagi Hasil '.($this->period_id ? Period::find($this->period_id)->name : date('M Y')));

                    // Accounting Entry (Payment Record)
                    Payment::create([
                        'dapur_id' => $this->dapur_id,
                        'user_id' => auth()->id(),
                        'no_pembayaran' => 'DIV-'.time().'-'.$investor->id,
                        'tanggal_pembayaran' => $this->tanggal_distribusi,
                        'jenis_transaksi' => 'lainnya',
                        'total_harga' => $amount,
                        'rekening_debit' => '3.2.01.02', // Ikhtisar Laba Rugi
                        'rekening_kredit' => '2.1.03.03', // Dividen Penyerta Modal Lainnya
                        'catatan' => "Alokasi Bagi Hasil Investor: {$investor->name}".($this->period_id ? ' (Periode: '.Period::find($this->period_id)->name.')' : ''),
                        'created_by' => auth()->id(),
                    ]);
                }

                // 3. Record Yayasan Share in Accounting
                if ($this->yayasan_share > 0) {
                    Payment::create([
                        'dapur_id' => $this->dapur_id,
                        'user_id' => auth()->id(),
                        'no_pembayaran' => 'YAY-'.time(),
                        'tanggal_pembayaran' => $this->tanggal_distribusi,
                        'jenis_transaksi' => 'lainnya',
                        'total_harga' => $this->yayasan_share,
                        'rekening_debit' => '3.2.01.02',
                        'rekening_kredit' => '2.1.04.01', // Utang Jangka Pendek Lain-lain (Bagian Yayasan)
                        'catatan' => 'Alokasi Bagi Hasil Yayasan'.($this->period_id ? ' (Periode: '.Period::find($this->period_id)->name.')' : ''),
                        'created_by' => auth()->id(),
                    ]);

                    // Also credit Dapur Wallet as Yayasan owner
                    $dapur = Dapur::find($this->dapur_id);
                    $this->creditWallet($dapur, $this->yayasan_share, 'Bagi Hasil Yayasan');
                }
            });

            $this->dispatch('notify', message: 'Distribusi bagi hasil berhasil disimpan dan dicatatkan dalam laporan keuangan.', variant: 'success');

            return redirect()->route('finance.profit-sharing.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal menyimpan: '.$e->getMessage(), variant: 'danger');
        }
    }

    protected function creditWallet($owner, $amount, $notes)
    {
        $wallet = $owner->wallet;

        if (! $wallet) {
            $wallet = Wallet::create([
                'owner_type' => get_class($owner),
                'owner_id' => $owner->id,
                'balance' => 0,
                'is_active' => true,
            ]);
        }

        $wallet->increment('balance', $amount);
        $wallet->update([
            'last_transaction_at' => now(),
            'notes' => $notes,
        ]);

        return $wallet;
    }

    public function render()
    {
        return view('livewire.finance.profit-distribution-form')
            ->layout('layouts.app', ['title' => 'Input Distribusi Bagi Hasil']);
    }
}
