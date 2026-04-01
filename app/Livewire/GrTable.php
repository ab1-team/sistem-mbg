<?php

namespace App\Livewire;

use App\Models\PurchaseOrder;
use App\Traits\WithSmartTable;
use Livewire\Component;

class GrTable extends Component
{
    use WithSmartTable;

    public function render()
    {
        $purchaseOrders = PurchaseOrder::query()
            ->with(['dapur', 'menuPeriod'])
            ->whereIn('status', [
                \App\Enums\PoStatus::DITERUSKAN_KE_SUPPLIER,
                \App\Enums\PoStatus::DIPROSES_SUPPLIER,
                \App\Enums\PoStatus::DALAM_PENGIRIMAN,
                \App\Enums\PoStatus::DITERIMA_SEBAGIAN,
            ])
            ->when($this->search, function ($query) {
                $query->where('po_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('dapur', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.gr-table', [
            'purchaseOrders' => $purchaseOrders,
        ]);
    }
}
