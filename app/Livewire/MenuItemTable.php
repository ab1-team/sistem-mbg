<?php

namespace App\Livewire;

use App\Models\MenuItem;
use App\Traits\WithSmartTable;
use Livewire\Component;

class MenuItemTable extends Component
{
    use WithSmartTable;

    public $mealType = '';

    public function render()
    {
        $menuItems = MenuItem::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->mealType, function ($query) {
                $query->where('meal_type', $this->mealType);
            })
            ->withCount('boms')
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.menu-item-table', [
            'menuItems' => $menuItems,
            'mealTypes' => ['pagi', 'siang', 'sore', 'snack', 'lainnya'],
        ]);
    }
}
