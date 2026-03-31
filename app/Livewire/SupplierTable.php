<?php

namespace App\Livewire;

use App\Models\Supplier;
use App\Traits\WithSmartTable;
use Livewire\Component;

class SupplierTable extends Component
{
    use WithSmartTable;

    public function render()
    {
        $suppliers = Supplier::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('contact_name', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.supplier-table', [
            'suppliers' => $suppliers,
        ]);
    }
}
