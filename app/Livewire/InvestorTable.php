<?php

namespace App\Livewire;

use App\Models\Investor;
use App\Traits\WithSmartTable;
use Livewire\Component;

class InvestorTable extends Component
{
    use WithSmartTable;

    public function render()
    {
        $investors = Investor::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('identity_number', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.investor-table', [
            'investors' => $investors,
        ]);
    }
}
