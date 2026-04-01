<?php

namespace App\Livewire\Finance;

use App\Models\Dapur;
use App\Models\Period;
use App\Models\Revenue;
use App\Services\Finance\FinancialRecordService;
use Livewire\Component;

class RevenueForm extends Component
{
    public $revenueId;

    public $dapur_id;

    public $period_id;

    public $amount;

    public $notes;

    public function mount($revenueId = null)
    {
        if ($revenueId) {
            $this->revenueId = $revenueId;
            $revenue = Revenue::findOrFail($revenueId);
            $this->dapur_id = $revenue->dapur_id;
            $this->period_id = $revenue->period_id;
            $this->amount = $revenue->amount;
            $this->notes = $revenue->notes;
        } else {
            $this->period_id = Period::getActive()?->id;
        }
    }

    public function save(FinancialRecordService $service)
    {
        $this->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'period_id' => 'required|exists:periods,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($this->revenueId) {
            $revenue = Revenue::findOrFail($this->revenueId);
            $revenue->update([
                'dapur_id' => $this->dapur_id,
                'period_id' => $this->period_id,
                'amount' => $this->amount,
                'notes' => $this->notes,
            ]);
            session()->flash('success', 'Data pendapatan berhasil diperbarui.');
        } else {
            $service->createRevenue([
                'dapur_id' => $this->dapur_id,
                'period_id' => $this->period_id,
                'amount' => $this->amount,
                'notes' => $this->notes,
            ]);
            session()->flash('success', 'Data pendapatan berhasil disimpan.');
        }

        return redirect()->route('finance.revenues.index');
    }

    public function render()
    {
        return view('livewire.finance.revenue-form', [
            'dapurs' => Dapur::orderBy('nama')->get(),
            'periods' => Period::where('status', '!=', 'locked')->orderBy('start_date', 'desc')->get(),
        ]);
    }
}
