<?php

namespace App\Livewire;

use App\Models\User;
use App\Traits\WithSmartTable;
use Livewire\Component;

class UserTable extends Component
{
    use WithSmartTable;

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.user-table', [
            'users' => $users,
        ]);
    }
}
