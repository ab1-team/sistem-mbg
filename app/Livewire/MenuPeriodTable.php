<?php

namespace App\Livewire;

use App\Models\MenuPeriod;
use Livewire\Component;
use Livewire\WithPagination;

class MenuPeriodTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortAsc = false;
    public $status = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function render()
    {
        $query = MenuPeriod::with(['dapur', 'period', 'creator'])
            ->where('title', 'like', '%' . $this->search . '%');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.menu-period-table', [
            'menuPeriods' => $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate(10)
        ]);
    }
}
