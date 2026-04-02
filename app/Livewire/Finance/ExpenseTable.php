<?php

namespace App\Livewire\Finance;

use App\Models\Expense;
use App\Traits\WithSmartTable;
use Livewire\Component;

class ExpenseTable extends Component
{
    use WithSmartTable;

    public $dapurId = '';

    public function deleteExpense($id)
    {
        $user = auth()->user();
        $expense = Expense::findOrFail($id);

        // Cek akses sebelum hapus
        if ($user->dapur_id && $expense->dapur_id !== $user->dapur_id) {
            $this->dispatch('notify', message: 'Anda tidak memiliki akses untuk menghapus data ini.', variant: 'error');
            return;
        }

        $expense->delete();
        $this->dispatch('notify', message: 'Beban berhasil dihapus', variant: 'success');
    }

    public function render()
    {
        $user = auth()->user();

        $query = Expense::query()
            ->with(['dapur', 'period', 'creator']);

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        } elseif ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        $expenses = $query->when($this->search, function ($query) {
                $query->where('notes', 'like', '%'.$this->search.'%')
                    ->orWhere('category', 'like', '%'.$this->search.'%')
                    ->orWhereHas('dapur', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%'); // Fixed 'nama' to 'name'
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $dapurs = $user->dapur_id 
            ? \App\Models\Dapur::where('id', $user->dapur_id)->get() 
            : \App\Models\Dapur::orderBy('name')->get();

        return view('livewire.finance.expense-table', [
            'expenses' => $expenses,
            'dapurs' => $dapurs,
        ]);
    }
}
