<?php

namespace App\Livewire\Finance;

use App\Models\Dapur;
use App\Models\Expense;
use App\Models\Period;
use App\Services\Finance\FinancialRecordService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExpenseForm extends Component
{
    use WithFileUploads;

    public $expenseId;
    public $dapur_id;
    public $period_id;
    public $category;
    public $amount;
    public $notes;
    public $attachment;
    public $existingAttachment;

    public function mount($expenseId = null)
    {
        if ($expenseId) {
            $this->expenseId = $expenseId;
            $expense = Expense::findOrFail($expenseId);
            $this->dapur_id = $expense->dapur_id;
            $this->period_id = $expense->period_id;
            $this->category = $expense->category;
            $this->amount = $expense->amount;
            $this->notes = $expense->notes;
            $this->existingAttachment = $expense->attachment;
        } else {
            $this->period_id = Period::getActive()?->id;
        }
    }

    public function save(FinancialRecordService $service)
    {
        $this->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'period_id' => 'required|exists:periods,id',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'attachment' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $attachmentPath = $this->existingAttachment;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('expenses', 'public');
        }

        if ($this->expenseId) {
            $expense = Expense::findOrFail($this->expenseId);
            $expense->update([
                'dapur_id' => $this->dapur_id,
                'period_id' => $this->period_id,
                'category' => $this->category,
                'amount' => $this->amount,
                'notes' => $this->notes,
                'attachment' => $attachmentPath,
            ]);
            session()->flash('success', 'Data pengeluaran berhasil diperbarui.');
        } else {
            $service->createExpense([
                'dapur_id' => $this->dapur_id,
                'period_id' => $this->period_id,
                'category' => $this->category,
                'amount' => $this->amount,
                'notes' => $this->notes,
                'attachment' => $attachmentPath,
            ]);
            session()->flash('success', 'Data pengeluaran berhasil disimpan.');
        }

        return redirect()->route('finance.expenses.index');
    }

    public function render()
    {
        return view('livewire.finance.expense-form', [
            'dapurs' => Dapur::orderBy('nama')->get(),
            'periods' => Period::where('status', '!=', 'locked')->orderBy('start_date', 'desc')->get(),
            'categories' => [
                'gaji' => 'Gaji & Upah',
                'listrik_air' => 'Listrik, Air & Internet',
                'sewa' => 'Sewa Bangunan',
                'transportasi' => 'Transportasi & BBM',
                'peralatan' => 'Peralatan & Perlengkapan',
                'bahan_baku' => 'Bahan Baku (Manual)',
                'lain_lain' => 'Lain-lain',
            ],
        ]);
    }
}
