<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Traits\WithSmartTable;
use Livewire\Component;

class InvoiceTable extends Component
{
    use WithSmartTable;

    public function render()
    {
        $invoices = Invoice::query()
            ->with(['purchaseOrder', 'supplier', 'dapur'])
            ->when($this->search, function ($query) {
                $query->where('invoice_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('purchaseOrder', function ($q) {
                        $q->where('po_number', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.invoice-table', [
            'invoices' => $invoices,
        ]);
    }
}
