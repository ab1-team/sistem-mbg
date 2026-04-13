<?php

namespace App\Livewire\Investor;

use App\Models\Investor;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Notifications\WithdrawalRequested;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WithdrawalRequestForm extends Component
{
    public $amount;

    public $notes;

    public function save()
    {
        $user = Auth::user();
        $investor = Investor::where('user_id', $user->id)->first();

        if (! $investor) {
            session()->flash('error', 'Data investor tidak ditemukan.');

            return;
        }

        $wallet = $investor->wallet;
        $balance = $wallet ? $wallet->balance : 0;

        $this->validate([
            'amount' => 'required|numeric|min:10000|max:'.$balance,
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.max' => 'Saldo tidak mencukupi untuk penarikan ini.',
            'amount.min' => 'Minimal penarikan adalah Rp 10.000.',
        ]);

        $request = WithdrawalRequest::create([
            'investor_id' => $investor->id,
            'amount' => $this->amount,
            'status' => 'pending',
            'notes' => $this->notes,
        ]);

        // Notify Finance
        $finances = User::role('finance_yayasan')->get();
        foreach ($finances as $finance) {
            $finance->notify(new WithdrawalRequested($request, $user->name));
        }

        session()->flash('success', 'Permintaan penarikan berhasil diajukan. Mohon tunggu verifikasi admin.');

        return redirect()->route('investor.dashboard');
    }

    public function render()
    {
        $user = Auth::user();
        $investor = Investor::where('user_id', $user->id)->first();
        $balance = $investor && $investor->wallet ? $investor->wallet->balance : 0;

        return view('livewire.investor.withdrawal-request-form', [
            'balance' => $balance,
        ]);
    }
}
