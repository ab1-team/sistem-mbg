<?php

namespace App\Livewire;

use App\Models\Dapur;
use App\Traits\WithSmartTable;
use Livewire\Component;

class DapurTable extends Component
{
    use WithSmartTable;

    public function render()
    {
        $dapurs = Dapur::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.dapur-table', [
            'dapurs' => $dapurs,
        ]);
    }
}
