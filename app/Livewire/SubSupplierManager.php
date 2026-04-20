<?php

namespace App\Livewire;

use App\Models\SubSupplier;
use App\Models\Supplier;
use Livewire\Component;

class SubSupplierManager extends Component
{
    public Supplier $supplier;

    public $name = '';

    public $phone = '';

    public $address = '';

    public $isEditing = false;

    public $subSupplierId = null;

    protected $rules = [
        'name' => 'required|string|max:150',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
    ];

    public function mount(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $subSupplier = SubSupplier::find($this->subSupplierId);
            $subSupplier->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);
            session()->flash('success', 'Sub-Supplier berhasil diperbarui.');
        } else {
            $this->supplier->subSuppliers()->create([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);
            session()->flash('success', 'Sub-Supplier berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $subSupplier = SubSupplier::find($id);
        $this->subSupplierId = $id;
        $this->name = $subSupplier->name;
        $this->phone = $subSupplier->phone;
        $this->address = $subSupplier->address;
        $this->isEditing = true;
    }

    public function toggleStatus($id)
    {
        $subSupplier = SubSupplier::find($id);
        $subSupplier->update(['is_active' => ! $subSupplier->is_active]);
    }

    public function delete($id)
    {
        SubSupplier::find($id)->delete();
        session()->flash('success', 'Sub-Supplier berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->phone = '';
        $this->address = '';
        $this->isEditing = false;
        $this->subSupplierId = null;
    }

    public function render()
    {
        return view('livewire.sub-supplier-manager', [
            'subSuppliers' => $this->supplier->subSuppliers()->orderBy('name')->get(),
        ]);
    }
}
