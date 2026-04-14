<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Livewire\Attributes\On;
use Livewire\Component;

class PoAddItemForm extends Component
{
    public ?PurchaseOrder $purchaseOrder = null;

    public $isOpen = false;

    public $selectedMaterialId = '';

    public $quantity = 1;

    public $unit_price = 0;

    public $unit = '';

    protected $rules = [
        'selectedMaterialId' => 'required|exists:materials,id',
        'quantity' => 'required|numeric|min:0.001',
        'unit_price' => 'required|numeric|min:0',
    ];

    #[On('open-add-item')]
    public function openModal($poId)
    {
        $this->purchaseOrder = PurchaseOrder::find($poId);
        $this->reset(['selectedMaterialId', 'quantity', 'unit_price', 'unit']);
        $this->isOpen = true;
    }

    public function updatedSelectedMaterialId($value)
    {
        if ($value) {
            $material = Material::find($value);
            if ($material) {
                $this->unit_price = $material->price_estimate ?? 0;
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
        $this->dispatch('item-added');
        $this->isOpen = false;
        session()->flash('success', 'Barang berhasil ditambahkan.');
    }

    public function render()
    {
        $materials = Material::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($m) {
                return [
                    'value' => $m->id,
                    'label' => $m->name." ({$m->unit})",
                ];
            });

        return view('livewire.po-add-item-form', [
            'materialOptions' => $materials,
        ]);
    }
}
