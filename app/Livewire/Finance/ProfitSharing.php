<?php

namespace App\Livewire\Finance;

use App\Models\ProfitCalculation;
use App\Traits\WithSmartTable;
use Livewire\Component;

class ProfitSharing extends Component
{
    use WithSmartTable;

    public $selectedCalculation = null;

    public function showDetail($id)
    {
        $this->selectedCalculation = ProfitCalculation::with(['distributions.investor', 'dapur', 'period'])
            ->findOrFail($id);

        $this->dispatch('open-modal', 'calculation-detail');
    }

    public function render()
    {
        $calculations = ProfitCalculation::with(['dapur', 'period', 'calculatedBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('period', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                })->orWhereHas('dapur', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.finance.profit-sharing', [
            'calculations' => $calculations,
        ])->layout('layouts.app', ['title' => 'Manajemen Bagi Hasil']);
    }
}
