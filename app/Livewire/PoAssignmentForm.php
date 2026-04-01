<?php

namespace App\Livewire;

use App\Models\PoSupplierAssignment;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

class PoAssignmentForm extends Component
{
    public ?PurchaseOrderItem $item = null;

    public $isOpen = false;

    public $suppliers;

    // Form fields
    public $supplier_id = '';

    public $quantity = 0;

    public $unit_price = 0;

    public function mount(?PurchaseOrderItem $item = null)
    {
        $this->item = $item;
        $this->suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        if ($this->item) {
            $this->initForm();
        }
    }

    #[On('open-assignment')]
    public function loadItem($itemId)
    {
        $this->item = PurchaseOrderItem::with(['material', 'assignments.supplier'])->find($itemId);
        $this->initForm();
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->item = null;
    }

    private function initForm()
    {
        $this->quantity = $this->remainingQuantity;
        $this->unit_price = $this->item->estimated_unit_price;
    }

    public function getRemainingQuantityProperty()
    {
        if (! $this->item) {
            return 0;
        }
        $assigned = $this->item->assignments()->sum('quantity_assigned');

        return $this->item->quantity_to_order - $assigned;
    }

    public function addAssignment()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ], [
            'quantity.min' => 'Kuantitas minimal adalah 0.',
        ]);

        PoSupplierAssignment::create([
            'po_item_id' => $this->item->id,
            'supplier_id' => $this->supplier_id,
            'assigned_by' => auth()->id(),
            'quantity_assigned' => $this->quantity,
            'unit_price_agreed' => $this->unit_price,
            'status' => 'diteruskan',
        ]);

        $this->reset(['supplier_id', 'quantity']);
        $this->quantity = $this->remainingQuantity;

        $this->dispatch('assignment-updated');
        $this->close();
        session()->flash('success', 'Supplier berhasil ditugaskan.');
    }

    public function removeAssignment(PoSupplierAssignment $assignment)
    {
        $assignment->delete();
        $this->quantity = $this->remainingQuantity;
        $this->dispatch('assignment-updated');
    }

    public function render()
    {
        return view('livewire.po-assignment-form');
    }
}
