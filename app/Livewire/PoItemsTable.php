<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Livewire\Attributes\On;
use Livewire\Component;

class PoItemsTable extends Component
{
    public PurchaseOrder $purchaseOrder;

    public $isOpen = false;

    public $selectedMaterialId = '';

    public $quantity = 1;

    public $unit_price = null;

    public $unit = '';

    public array $materialOptions = [];

    protected $rules = [
        'selectedMaterialId' => 'required',
        'quantity' => 'required|numeric|min:0.001',
        'unit_price' => 'required|numeric|min:0',
    ];

    public function mount(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder->load(['items.material', 'items.assignments.supplier', 'items.assignments.subSupplier']);
        $this->loadMaterials();
    }

    protected function loadMaterials()
    {
        // Gunakan mapping paling sederhana untuk menghindari error JSON di browser
        $this->materialOptions = Material::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($mat) {
                return [
                    'value' => (string) $mat->id,
                    'label' => $mat->name, // Hanya nama agar 100% aman
                    'unit' => (string) $mat->unit,
                ];
            })
            ->toArray();
    }

    public function openAddItem()
    {
        $this->reset(['selectedMaterialId', 'quantity', 'unit_price', 'unit']);
        $this->loadMaterials();
        $this->isOpen = true;
        $this->dispatch('open-modal', name: 'po-add-item-manual');
    }

    public function updatedSelectedMaterialId($value)
    {
        if ($value) {
            $material = Material::find($value);
            if ($material) {
                $this->unit_price = $material->price_estimate > 0 ? $material->price_estimate : null;
                $this->unit = $material->unit;
            }
        }
    }

    public function addItem()
    {
        $this->validate();

        $existingItem = PurchaseOrderItem::where('purchase_order_id', $this->purchaseOrder->id)
            ->where('material_id', $this->selectedMaterialId)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity_to_order' => $existingItem->quantity_to_order + $this->quantity,
                'quantity_needed' => $existingItem->quantity_needed + $this->quantity,
            ]);
        } else {
            PurchaseOrderItem::create([
                'purchase_order_id' => $this->purchaseOrder->id,
                'material_id' => $this->selectedMaterialId,
                'quantity_needed' => $this->quantity,
                'quantity_to_order' => $this->quantity,
                'unit' => $this->unit ?: 'unit',
                'estimated_unit_price' => $this->unit_price,
                'item_status' => 'pending',
            ]);
        }

        $this->purchaseOrder->recalculateTotal();
        $this->isOpen = false;
        $this->refreshTable();
        $this->dispatch('close-modal', name: 'po-add-item-manual');
        session()->flash('success', 'Barang berhasil ditambahkan.');
    }

    public function removeItem($itemId)
    {
        $item = PurchaseOrderItem::where('purchase_order_id', $this->purchaseOrder->id)
            ->where('id', $itemId)
            ->first();

        if ($item) {
            $item->delete();
            $this->purchaseOrder->recalculateTotal();
            $this->refreshTable();
            session()->flash('success', 'Barang berhasil dihapus.');
        } else {
            session()->flash('error', 'Barang tidak ditemukan.');
        }
    }

    #[On('assignment-updated')]
    #[On('item-added')]
    public function refreshTable()
    {
        $this->purchaseOrder->load(['items.material', 'items.assignments.supplier', 'items.assignments.subSupplier']);
        $this->loadMaterials();
    }

    public function render()
    {
        return view('livewire.po-items-table');
    }
}
