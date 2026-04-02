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
    public $dapurId = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function render()
    {
        $user = auth()->user();
        
        $query = MenuPeriod::with(['dapur', 'period', 'creator'])
            ->where('title', 'like', '%'.$this->search.'%');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        } elseif ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        $menuPeriods = $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(10);

        $dapurs = $user->dapur_id 
            ? \App\Models\Dapur::where('id', $user->dapur_id)->get() 
            : \App\Models\Dapur::orderBy('name')->get();

        return view('livewire.menu-period-table', [
            'menuPeriods' => $menuPeriods,
            'dapurs' => $dapurs,
        ]);
    }
}
