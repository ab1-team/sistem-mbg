<?php

namespace App\Livewire;

use App\Enums\PoStatus;
use App\Models\Dapur;
use App\Models\PurchaseOrder;
use App\Traits\WithSmartTable;
use Livewire\Component;

class GrTable extends Component
{
    use WithSmartTable;

    public $dapurId = '';

    public function render()
    {
        $user = auth()->user();

        $query = PurchaseOrder::query()
            ->with(['dapur', 'menuPeriod'])
            ->whereIn('status', [
                PoStatus::DITERUSKAN_KE_SUPPLIER,
                PoStatus::DIPROSES_SUPPLIER,
                PoStatus::DALAM_PENGIRIMAN,
                PoStatus::DITERIMA_SEBAGIAN,
            ]);

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        } elseif ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        $purchaseOrders = $query->when($this->search, function ($query) {
            $query->where('po_number', 'like', '%'.$this->search.'%')
                ->orWhereHas('dapur', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                });
        })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $dapurs = $user->dapur_id
            ? Dapur::where('id', $user->dapur_id)->get()
            : Dapur::orderBy('name')->get();

        return view('livewire.gr-table', [
            'purchaseOrders' => $purchaseOrders,
            'dapurs' => $dapurs,
        ]);
    }
}
