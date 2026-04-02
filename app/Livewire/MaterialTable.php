<?php

namespace App\Livewire;

use App\Models\Dapur;
use App\Models\Material;
use App\Traits\WithSmartTable;
use Livewire\Component;

class MaterialTable extends Component
{
    use WithSmartTable;

    public $category = '';
    public $dapurId = '';

    public function render()
    {
        $user = auth()->user();

        $materials = Material::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            })
            ->when($user->dapur_id, function ($query) use ($user) {
                $query->where(function($q) use ($user) {
                    $q->whereNull('dapur_id')->orWhere('dapur_id', $user->dapur_id);
                });
            })
            ->when(!$user->dapur_id && $this->dapurId, function ($query) {
                $query->where('dapur_id', $this->dapurId);
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $dapurs = $user->dapur_id 
            ? Dapur::where('id', $user->dapur_id)->get() 
            : Dapur::all();

        return view('livewire.material-table', [
            'materials' => $materials,
            'categories' => ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya'],
            'dapurs' => $dapurs,
        ]);
    }
}
