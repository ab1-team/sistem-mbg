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
        $user = auth()->user();

        if ($revenueId) {
            $this->revenueId = $revenueId;
            $revenue = Revenue::findOrFail($revenueId);

            // Cek akses edit jika user terikat dapur tertentu
            if ($user->dapur_id && $revenue->dapur_id !== $user->dapur_id) {
                return redirect()->route('finance.revenues.index')->with('error', 'Anda tidak memiliki akses ke data ini.');
            }

            $this->dapur_id = $revenue->dapur_id;
            $this->period_id = $revenue->period_id;
            $this->amount = $revenue->amount;
            $this->notes = $revenue->notes;
        } else {
            $this->period_id = Period::getActive()?->id;
            // Auto-assign dapur jika user terikat dapur tertentu
            if ($user->dapur_id) {
                $this->dapur_id = $user->dapur_id;
            }
        }
    }

    public function save(FinancialRecordService $service)
    {
        $user = auth()->user();

        // Paksa dapur_id jika user terikat dapur tertentu
        if ($user->dapur_id) {
            $this->dapur_id = $user->dapur_id;
        }

        $this->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'period_id' => 'required|exists:periods,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($this->revenueId) {
            $revenue = Revenue::findOrFail($this->revenueId);

            // Cek akses simpan (Double check)
            if ($user->dapur_id && $revenue->dapur_id !== $user->dapur_id) {
                return redirect()->route('finance.revenues.index')->with('error', 'Akses ditolak.');
            }

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
        $user = auth()->user();
        $dapurs = $user->dapur_id
            ? Dapur::where('id', $user->dapur_id)->get()
            : Dapur::orderBy('name')->get();

        return view('livewire.finance.revenue-form', [
            'dapurs' => $dapurs,
            'periods' => Period::where('status', '!=', 'locked')->orderBy('start_date', 'desc')->get(),
        ]);
    }
}
