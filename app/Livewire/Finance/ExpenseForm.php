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
        $user = auth()->user();

        if ($expenseId) {
            $this->expenseId = $expenseId;
            $expense = Expense::findOrFail($expenseId);

            // Cek akses edit jika user terikat dapur tertentu
            if ($user->dapur_id && $expense->dapur_id !== $user->dapur_id) {
                return redirect()->route('finance.expenses.index')->with('error', 'Anda tidak memiliki akses ke data ini.');
            }

            $this->dapur_id = $expense->dapur_id;
            $this->period_id = $expense->period_id;
            $this->category = $expense->category;
            $this->amount = $expense->amount;
            $this->notes = $expense->notes;
            $this->existingAttachment = $expense->attachment;
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

            // Cek akses simpan (Double check)
            if ($user->dapur_id && $expense->dapur_id !== $user->dapur_id) {
                return redirect()->route('finance.expenses.index')->with('error', 'Akses ditolak.');
            }

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
        $user = auth()->user();
        $dapurs = $user->dapur_id 
            ? Dapur::where('id', $user->dapur_id)->get() 
            : Dapur::orderBy('name')->get();

        return view('livewire.finance.expense-form', [
            'dapurs' => $dapurs,
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
