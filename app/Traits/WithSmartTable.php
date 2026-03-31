<?php

namespace App\Traits;

use Livewire\WithPagination;

trait WithSmartTable
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortAsc = false;

    /**
     * Boot the trait to add common logic
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Handle sorting
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    /**
     * Common pagination view customization for Tailwind
     */
    public function paginationView()
    {
        return 'livewire.pagination-ui';
    }
}
