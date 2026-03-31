<?php

namespace App\Livewire;

use App\Models\Period;
use App\Traits\WithSmartTable;
use Livewire\Component;

class PeriodTable extends Component
{
    use WithSmartTable;

    public function render()
    {
        $periods = Period::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('status', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.period-table', [
            'periods' => $periods,
        ]);
    }
}
