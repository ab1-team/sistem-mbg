<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Traits\WithSmartTable;
use Livewire\Component;

class InvoiceTable extends Component
{
    use WithSmartTable;

    public $dapurId = '';

    public function render()
    {
        $user = auth()->user();

        $query = Invoice::query()
            ->with(['purchaseOrder', 'supplier', 'dapur']);

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        } elseif ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        $invoices = $query->when($this->search, function ($query) {
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

        $dapurs = $user->dapur_id 
            ? \App\Models\Dapur::where('id', $user->dapur_id)->get() 
            : \App\Models\Dapur::orderBy('name')->get();

        return view('livewire.invoice-table', [
            'invoices' => $invoices,
            'dapurs' => $dapurs,
        ]);
    }
}
