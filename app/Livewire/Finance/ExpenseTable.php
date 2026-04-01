<?php

namespace App\Livewire\Finance;

use App\Models\Expense;
use App\Traits\WithSmartTable;
use Livewire\Component;

class ExpenseTable extends Component
{
    use WithSmartTable;

    public function deleteExpense($id)
    {
        Expense::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Beban berhasil dihapus', variant: 'success');
    }

    public function render()
    {
        $expenses = Expense::query()
            ->with(['dapur', 'period', 'creator'])
            ->when($this->search, function ($query) {
                $query->where('notes', 'like', '%'.$this->search.'%')
                    ->orWhere('category', 'like', '%'.$this->search.'%')
                    ->orWhereHas('dapur', function ($q) {
                        $q->where('nama', 'like', '%'.$this->search.'%');
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.finance.expense-table', [
            'expenses' => $expenses,
        ]);
    }
}
