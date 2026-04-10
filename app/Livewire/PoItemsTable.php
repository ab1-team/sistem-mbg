<?php

namespace App\Livewire;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Livewire\Attributes\On;
use Livewire\Component;

class PoItemsTable extends Component
{
    public PurchaseOrder $purchaseOrder;

    public function mount(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder->load(['items.material', 'items.assignments.supplier']);
    }

    #[On('assignment-updated')]
    #[On('item-added')]
    public function refreshTable()
    {
        $this->purchaseOrder->load(['items.material', 'items.assignments.supplier']);
    }

    public function removeItem($itemId)
    {
        $item = PurchaseOrderItem::find($itemId);
        if ($item) {
            $item->delete();
            $this->purchaseOrder->recalculateTotal();
            $this->refreshTable();
            session()->flash('success', 'Barang berhasil dihapus.');
        }
    }

    public function render()
    {
        return view('livewire.po-items-table');
    }
}
