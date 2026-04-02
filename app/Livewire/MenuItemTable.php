<?php

namespace App\Livewire;

use App\Models\Dapur;
use App\Models\MenuItem;
use App\Traits\WithSmartTable;
use Livewire\Component;

class MenuItemTable extends Component
{
    use WithSmartTable;

    public $mealType = '';

    public $dapurId = '';

    public function render()
    {
        $user = auth()->user();

        $menuItems = MenuItem::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->mealType, function ($query) {
                $query->where('meal_type', $this->mealType);
            })
            ->when($user->dapur_id, function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->whereNull('dapur_id')->orWhere('dapur_id', $user->dapur_id);
                });
            })
            ->when(! $user->dapur_id && $this->dapurId, function ($query) {
                $query->where('dapur_id', $this->dapurId);
            })
            ->withCount('boms')
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $dapurs = $user->dapur_id
            ? Dapur::where('id', $user->dapur_id)->get()
            : Dapur::all();

        return view('livewire.menu-item-table', [
            'menuItems' => $menuItems,
            'mealTypes' => ['pagi', 'siang', 'sore', 'snack', 'lainnya'],
            'dapurs' => $dapurs,
        ]);
    }
}
