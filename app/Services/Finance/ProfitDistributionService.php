<?php

namespace App\Services\Finance;

use App\Models\DividendDistribution;
use App\Models\Investor;
use App\Models\Payment;
use App\Models\Period;
use App\Models\ProfitCalculation;
use App\Models\Setting;
use App\Models\Wallet;
use App\Notifications\ProfitDistributed;
use Illuminate\Support\Facades\DB;

class ProfitDistributionService
{
    /**
     * Calculate and distribute profit for a period and dapur.
     */
    public function distributeProfit(Period $period)
    {
        return DB::transaction(function () use ($period) {
            // Get all dapurs that have activity in this period
            $dapurIds = Payment::whereBetween('tanggal_pembayaran', [$period->start_date, $period->end_date])
                ->pluck('dapur_id')
                ->unique();

            $results = [];

            foreach ($dapurIds as $dapurId) {
                // 1. Calculate Totals based on Payment types
                $totalRevenue = Payment::where('dapur_id', $dapurId)
                    ->whereBetween('tanggal_pembayaran', [$period->start_date, $period->end_date])
                    ->where('jenis_transaksi', 'pendapatan_dana')
                    ->sum('total_harga');

                // COGS (HPP) - pembelian_bahan
                $totalCogs = Payment::where('dapur_id', $dapurId)
                    ->whereBetween('tanggal_pembayaran', [$period->start_date, $period->end_date])
                    ->where('jenis_transaksi', 'pembelian_bahan')
                    ->sum('total_harga');

                // Other Expenses - operasional_dapur, gaji_staf, lainnya
                $totalOtherExpenses = Payment::where('dapur_id', $dapurId)
                    ->whereBetween('tanggal_pembayaran', [$period->start_date, $period->end_date])
                    ->whereIn('jenis_transaksi', ['operasional_dapur', 'gaji_staf', 'lainnya'])
                    ->sum('total_harga');

                $grossProfit = $totalRevenue - $totalCogs;
                $netProfit = $grossProfit - $totalOtherExpenses;

                // 2. Create Profit Calculation Record
                $calculation = ProfitCalculation::create([
                    'dapur_id' => $dapurId,
                    'period_id' => $period->id,
                    'total_revenue' => $totalRevenue,
                    'total_cogs' => $totalCogs,
                    'total_expenses' => $totalOtherExpenses,
                    'gross_profit' => $grossProfit,
                    'net_profit' => $netProfit,
                    'yayasan_share' => $netProfit > 0 ? ($netProfit * (Setting::get('profit_share_yayasan', 20)) / 100) : 0,
                    'investor_total_share' => $netProfit > 0 ? ($netProfit * (Setting::get('profit_share_investor', 80)) / 100) : 0,
                    'status' => 'final',
                    'calculated_by' => auth()->id() ?? 1,
                ]);

                // 3. Distribute to Investors if there's profit
                if ($netProfit > 0) {
                    $investorPool = $calculation->investor_total_share;
                    $investors = Investor::where('is_active', true)->get();

                    foreach ($investors as $investor) {
                        $investorShare = $investorPool * ($investor->share_percentage / 100);

                        // Create distribution record
                        $distribution = DividendDistribution::create([
                            'profit_calculation_id' => $calculation->id,
                            'investor_id' => $investor->id,
                            'share_percentage' => $investor->share_percentage,
                            'amount' => $investorShare,
                            'status' => 'paid', // Mark as paid immediately because we credit the wallet
                            'paid_at' => now(),
                        ]);

                        // Credit Investor's Wallet
                        $this->creditWallet($investor, $investorShare, "Dividen Periode: {$period->name} (Dapur: {$calculation->dapur->nama})");

                        // Notify Investor
                        $investor->user?->notify(new ProfitDistributed($distribution, $period->name));
                    }

                    // Credit Yayasan's Wallet (mapped to Dapur wallet for now or a general Yayasan account)
                    // For now, let's just record it. If Dapur has a wallet, credit it.
                    $dapur = $calculation->dapur;
                    $this->creditWallet($dapur, $calculation->yayasan_share, "Bagi Hasil Yayasan Periode: {$period->name}");
                }

                $results[] = $calculation;
            }

            return $results;
        });
    }

    /**
     * Credit a wallet for an owner (Investor or Dapur).
     */
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
}
