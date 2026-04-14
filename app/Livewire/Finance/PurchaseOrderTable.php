<?php

namespace App\Livewire\Finance;

use App\Models\Dapur;
use App\Models\PurchaseOrder;
use App\Traits\WithSmartTable;
use Livewire\Component;

class PurchaseOrderTable extends Component
{
    use WithSmartTable;

    public $dapurId = '';

    public $status = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user->dapur_id) {
            $this->dapurId = $user->dapur_id;
        }
    }

    public function render()
    {
        $user = auth()->user();
        $query = PurchaseOrder::with(['dapur', 'creator']);

        // Dapur filter
        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        } elseif ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        // Status filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Search
        if ($this->search) {
            $query->where('po_number', 'like', '%'.$this->search.'%');
        }

        $purchaseOrders = $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $dapurs = $user->dapur_id ? [] : Dapur::orderBy('name')->get();

        return view('livewire.finance.purchase-order-table', [
            'purchaseOrders' => $purchaseOrders,
            'dapurs' => $dapurs,
        ]);
    }
}
