<?php

namespace App\Livewire;

use App\Enums\PoStatus;
use App\Models\Dapur;
use App\Models\PurchaseOrder;
use App\Traits\WithSmartTable;
use Livewire\Component;

class KitchenInvoiceTable extends Component
{
    use WithSmartTable;

    public $dapurId = '';

    public function render()
    {
        $user = auth()->user();

        $query = PurchaseOrder::query()
            ->with(['dapur', 'menuPeriod'])
            ->whereIn('status', [
                PoStatus::DITERUSKAN_KE_SUPPLIER->value,
                PoStatus::DIPROSES_SUPPLIER->value,
                PoStatus::DALAM_PENGIRIMAN->value,
                PoStatus::DITERIMA_SEBAGIAN->value,
                PoStatus::DITERIMA_LENGKAP->value,
                PoStatus::MENUNGGU_VERIFIKASI_DAPUR->value,
                PoStatus::SELESAI->value,
            ]);

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        } elseif ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        $invoices = $query->when($this->search, function ($query) {
            $query->where('po_number', 'like', '%'.$this->search.'%')
                ->orWhereHas('dapur', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                })
                ->orWhereHas('menuPeriod', function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%');
                });
        })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $dapurs = $user->dapur_id
            ? Dapur::where('id', $user->dapur_id)->get()
            : Dapur::orderBy('name')->get();

        return view('livewire.kitchen-invoice-table', [
            'invoices' => $invoices,
            'dapurs' => $dapurs,
        ]);
    }
}
