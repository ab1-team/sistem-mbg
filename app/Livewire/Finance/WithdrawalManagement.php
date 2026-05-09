<?php

namespace App\Livewire\Finance;

use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use App\Notifications\WithdrawalStatusChanged;
use App\Traits\WithSmartTable;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WithdrawalManagement extends Component
{
    use WithSmartTable;

    public function approveRequest($id)
    {
        $request = WithdrawalRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            $this->dispatch('notify', message: 'Hanya permintaan Pending yang bisa diproses.', variant: 'danger');

            return;
        }

        DB::transaction(function () use ($request) {
            $investor = $request->investor;
            $wallet = $investor->wallet;

            if (! $wallet || $wallet->balance < $request->amount) {
                throw new \Exception('Saldo investor tidak cukup untuk penarikan ini.');
            }

            // Deduct Wallet
            $wallet->decrement('balance', $request->amount);
            $wallet->update([
                'last_transaction_at' => now(),
            ]);

            // Mark request as processed
            $request->update([
                'status' => 'processed',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);
        });

        // Notify Investor
        $request->investor?->user?->notify(new WithdrawalStatusChanged($request, 'disetujui'));

        $this->dispatch('notify', message: 'Penarikan berhasil disetujui dan saldo investor telah dikurangi.', variant: 'success');
    }

    public function rejectRequest($id)
    {
        $request = WithdrawalRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            $this->dispatch('notify', message: 'Hanya permintaan Pending yang bisa ditolak.', variant: 'danger');

            return;
        }

        $request->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Notify Investor
        $request->investor?->user?->notify(new WithdrawalStatusChanged($request, 'ditolak'));

        $this->dispatch('notify', message: 'Permintaan penarikan telah ditolak.', variant: 'success');
    }

    public function render()
    {
        $requests = WithdrawalRequest::query()
            ->with(['investor', 'processedBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('investor', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.finance.withdrawal-management', [
            'withdrawals' => $requests,
        ])->layout('layouts.app', ['title' => 'Manajemen Penarikan Dana']);
    }
}
