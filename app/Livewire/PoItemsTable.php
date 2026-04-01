<?php

namespace App\Livewire;

use App\Models\PurchaseOrder;
use Livewire\Attributes\On;
use Livewire\Component;

class PoItemsTable extends Component
{
    public PurchaseOrder $purchaseOrder;

    public function mount(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    #[On('assignment-updated')]
    public function refreshTable()
    {
        $this->purchaseOrder->load('items.assignments.supplier');
    }

    public function render()
    {
        return view('livewire.po-items-table');
    }
}
