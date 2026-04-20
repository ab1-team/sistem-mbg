<?php

namespace App\Livewire;

use App\Models\PoSupplierAssignment;
use App\Models\PurchaseOrderItem;
use App\Models\SubSupplier;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

class PoAssignmentForm extends Component
{
    public ?PurchaseOrderItem $item = null;

    public $isOpen = false;

    public $suppliers;

    public $sub_suppliers;

    // Form fields
    public $sub_supplier_id = '';

    public $quantity = 0;

    public $unit_price = 0;

    public function mount(?PurchaseOrderItem $item = null)
    {
        $this->item = $item;
        $this->suppliers = Supplier::where('is_active', true)
            ->with(['subSuppliers' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        if ($this->item) {
            $this->initForm();
        }
    }

    #[On('open-assignment')]
    public function loadItem($itemId)
    {
        $this->item = PurchaseOrderItem::with(['material', 'assignments.supplier', 'assignments.subSupplier'])->find($itemId);
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

        // Menghitung alokasi yang sudah 'terpakai':
        // 1. Jika penugasan sudah ditutup (is_fulfillment_closed), yang dihitung adalah jml yang BENAR-BENAR DITERIMA.
        // 2. Jika penugasan masih aktif, yang dihitung adalah jml yang DITUGASKAN (reservasi).
        $used = $this->item->assignments->sum(function ($a) {
            return $a->is_fulfillment_closed ? $a->quantity_received : $a->quantity_assigned;
        });

        return max(0, $this->item->quantity_to_order - $used);
    }

    public function addAssignment()
    {
        $this->validate([
            'sub_supplier_id' => 'required|exists:sub_suppliers,id',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ], [
            'quantity.min' => 'Kuantitas minimal adalah 0.',
        ]);

        $subSupplier = SubSupplier::find($this->sub_supplier_id);

        PoSupplierAssignment::create([
            'po_item_id' => $this->item->id,
            'supplier_id' => $subSupplier->supplier_id,
            'sub_supplier_id' => $this->sub_supplier_id,
            'assigned_by' => auth()->id(),
            'quantity_assigned' => $this->quantity,
            'unit_price_agreed' => $this->unit_price,
            'status' => 'diteruskan',
            'is_fulfillment_closed' => false,
        ]);

        $this->reset(['sub_supplier_id', 'quantity']);
        $this->quantity = $this->remainingQuantity;

        $this->dispatch('assignment-updated');
        $this->close();
        session()->flash('success', 'Sub-Supplier berhasil ditugaskan.');
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
