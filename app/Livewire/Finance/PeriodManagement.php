<?php

namespace App\Livewire\Finance;

use App\Models\Period;
use App\Services\Finance\ProfitDistributionService;
use App\Traits\WithSmartTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PeriodManagement extends Component
{
    use WithSmartTable;

    public function closePeriod($id)
    {
        $period = Period::findOrFail($id);

        if (! $period->isOpen()) {
            $this->dispatch('notify', message: 'Hanya periode Open yang bisa ditutup.', variant: 'danger');

            return;
        }

        $period->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);

        $this->dispatch('notify', message: "Periode {$period->name} berhasil ditutup.", variant: 'success');
    }

    public function reopenPeriod($id)
    {
        $period = Period::findOrFail($id);

        if (! $period->isClosed()) {
            $this->dispatch('notify', message: 'Hanya periode Closed yang bisa dibuka kembali.', variant: 'danger');

            return;
        }

        $period->update([
            'status' => 'open',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        $this->dispatch('notify', message: "Periode {$period->name} dibuka kembali.", variant: 'success');
    }

    public function lockPeriod($id, ProfitDistributionService $service)
    {
        $period = Period::findOrFail($id);

        if (! $period->isClosed()) {
            $this->dispatch('notify', message: 'Periode harus ditutup (Closed) sebelum bisa dikunci (Locked).', variant: 'danger');

            return;
        }

        try {
            // Trigger profit distribution calculation
            $service->distributeProfit($period);

            // Update status to locked
            $period->update([
                'status' => 'locked',
            ]);

            $this->dispatch('notify', message: "Periode {$period->name} telah dikunci dan bagi hasil berhasil didistribusikan.", variant: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal mengunci periode: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function render()
    {
        $periods = Period::query()
            ->with('closedBy')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate($this->perPage);

        return view('livewire.finance.period-management', [
            'periods' => $periods,
        ]);
    }
}
