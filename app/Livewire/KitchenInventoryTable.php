<?php

namespace App\Livewire;

use App\Models\Stock;
use App\Traits\WithSmartTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KitchenInventoryTable extends Component
{
    use WithSmartTable;

    public $category = '';

    public function render()
    {
        $dapurId = Auth::user()->dapur_id ?? \App\Models\Dapur::first()->id;

        $stocks = Stock::query()
            ->with(['material'])
            ->where('dapur_id', $dapurId)
            ->when($this->search, function ($query) {
                $query->whereHas('material', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('code', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->category, function ($query) {
                $query->whereHas('material', function ($q) {
                    $q->where('category', $this->category);
                });
            })
            ->orderBy($this->sortField === 'created_at' ? 'id' : $this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.kitchen-inventory-table', [
            'stocks' => $stocks,
            'categories' => ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'],
        ]);
    }
}
