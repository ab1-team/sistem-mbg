<?php

namespace App\Livewire;

use App\Models\Material;
use App\Traits\WithSmartTable;
use Livewire\Component;

class MaterialTable extends Component
{
    use WithSmartTable;

    public $category = '';

    public function render()
    {
        $materials = Material::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.material-table', [
            'materials' => $materials,
            'categories' => ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'],
        ]);
    }
}
