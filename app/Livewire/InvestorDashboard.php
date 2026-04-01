<?php

namespace App\Livewire;

use App\Models\DividendDistribution;
use App\Models\Investor;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class InvestorDashboard extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();
        $investor = Investor::where('user_id', $user->id)->first();

        if (!$investor) {
            return view('livewire.investor-dashboard', [
                'error' => 'Data investor tidak ditemukan untuk akun ini.'
            ]);
        }

        $wallet = $investor->wallet;
        $totalEarned = DividendDistribution::where('investor_id', $investor->id)
            ->where('status', 'paid')
            ->sum('amount');

        $recentDistributions = DividendDistribution::with(['profitCalculation.period', 'profitCalculation.dapur'])
            ->where('investor_id', $investor->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentWithdrawals = WithdrawalRequest::where('investor_id', $investor->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.investor-dashboard', [
            'investor' => $investor,
            'wallet' => $wallet,
            'totalEarned' => $totalEarned,
            'recentDistributions' => $recentDistributions,
            'recentWithdrawals' => $recentWithdrawals,
        ]);
    }
}
