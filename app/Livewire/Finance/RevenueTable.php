<?php

namespace App\Livewire\Finance;

use App\Models\Revenue;
use App\Traits\WithSmartTable;
use Livewire\Component;

class RevenueTable extends Component
{
    use WithSmartTable;

    public function deleteRevenue($id)
    {
        Revenue::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Pendapatan berhasil dihapus', variant: 'success');
    }

    public function render()
    {
        $revenues = Revenue::query()
            ->with(['dapur', 'period'])
            ->when($this->search, function ($query) {
                $query->where('notes', 'like', '%'.$this->search.'%')
                    ->orWhereHas('dapur', function ($q) {
                        $q->where('nama', 'like', '%'.$this->search.'%');
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.finance.revenue-table', [
            'revenues' => $revenues,
        ]);
    }
}
