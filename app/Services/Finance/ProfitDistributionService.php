<?php

namespace App\Services\Finance;

use App\Models\DividendDistribution;
use App\Models\Expense;
use App\Models\Investor;
use App\Models\Period;
use App\Models\ProfitCalculation;
use App\Models\Revenue;
use App\Models\Wallet;
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
            $dapurIds = Revenue::where('period_id', $period->id)->pluck('dapur_id')
                ->merge(Expense::where('period_id', $period->id)->pluck('dapur_id'))
                ->unique();

            $results = [];

            foreach ($dapurIds as $dapurId) {
                // 1. Calculate Totals
                $totalRevenue = Revenue::where('period_id', $period->id)->where('dapur_id', $dapurId)->sum('amount');

                // COGS (HPP) - In our case, expenses with category 'bahan_baku'
                $totalCogs = Expense::where('period_id', $period->id)
                    ->where('dapur_id', $dapurId)
                    ->where('category', 'bahan_baku')
                    ->sum('amount');

                // Other Expenses
                $totalOtherExpenses = Expense::where('period_id', $period->id)
                    ->where('dapur_id', $dapurId)
                    ->where('category', '!=', 'bahan_baku')
                    ->sum('amount');

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
                    'yayasan_share' => $netProfit > 0 ? ($netProfit * 0.20) : 0,
                    'investor_total_share' => $netProfit > 0 ? ($netProfit * 0.80) : 0,
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
                        DividendDistribution::create([
                            'profit_calculation_id' => $calculation->id,
                            'investor_id' => $investor->id,
                            'share_percentage' => $investor->share_percentage,
                            'amount' => $investorShare,
                            'status' => 'paid', // Mark as paid immediately because we credit the wallet
                            'paid_at' => now(),
                        ]);

                        // Credit Investor's Wallet
                        $this->creditWallet($investor, $investorShare, "Dividen Periode: {$period->name} (Dapur: {$calculation->dapur->nama})");
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
