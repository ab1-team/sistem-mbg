<?php

namespace App\Livewire\Finance;

use App\Models\Revenue;
use App\Traits\WithSmartTable;
use Livewire\Component;

class RevenueTable extends Component
{
    use WithSmartTable;

    public $dapurId = '';

    public function deleteRevenue($id)
    {
        $user = auth()->user();
        $revenue = Revenue::findOrFail($id);
        
        // Cek akses sebelum hapus
        if ($user->dapur_id && $revenue->dapur_id !== $user->dapur_id) {
            $this->dispatch('notify', message: 'Anda tidak memiliki akses untuk menghapus data ini.', variant: 'error');
            return;
        }

        $revenue->delete();
        $this->dispatch('notify', message: 'Pendapatan berhasil dihapus', variant: 'success');
    }

    public function render()
    {
        $user = auth()->user();

        $query = Revenue::query()
            ->with(['dapur', 'period']);

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        } elseif ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        $revenues = $query->when($this->search, function ($query) {
                $query->where('notes', 'like', '%'.$this->search.'%')
                    ->orWhereHas('dapur', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%'); // Fix column name to 'name'
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $dapurs = $user->dapur_id 
            ? \App\Models\Dapur::where('id', $user->dapur_id)->get() 
            : \App\Models\Dapur::orderBy('name')->get();

        return view('livewire.finance.revenue-table', [
            'revenues' => $revenues,
            'dapurs' => $dapurs,
        ]);
    }
}
