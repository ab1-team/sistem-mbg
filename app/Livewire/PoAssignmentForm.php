<?php

namespace App\Livewire;

use App\Models\PoSupplierAssignment;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Livewire\Component;

class PoAssignmentForm extends Component
{
    public PurchaseOrderItem $item;
    public $suppliers;
    
    // Form fields
    public $supplier_id = '';
    public $quantity = 0;
    public $unit_price = 0;

    public function mount(PurchaseOrderItem $item)
    {
        $this->item = $item;
        $this->suppliers = Supplier::where('is_active', true)->get();
        $this->quantity = $this->remainingQuantity;
        $this->unit_price = $item->estimated_unit_price;
    }

    public function getRemainingQuantityProperty()
    {
        $assigned = $this->item->assignments()->sum('quantity_assigned');
        return $this->item->quantity_to_order - $assigned;
    }

    public function addAssignment()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.001|max:' . $this->remainingQuantity,
            'unit_price' => 'required|numeric|min:0',
        ], [
            'quantity.max' => 'Kuantitas melebihi sisa yang belum ditugaskan (' . $this->remainingQuantity . ').'
        ]);

        PoSupplierAssignment::create([
            'po_item_id' => $this->item->id,
            'supplier_id' => $this->supplier_id,
            'assigned_by' => auth()->id(),
            'quantity_assigned' => $this->quantity,
            'unit_price_agreed' => $this->unit_price,
            'status' => 'diteruskan'
        ]);

        $this->reset(['supplier_id', 'quantity']);
        $this->quantity = $this->remainingQuantity;
        
        $this->dispatch('assignment-updated');
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
