<?php

namespace App\Livewire;

use App\Enums\PoStatus;
use App\Models\PurchaseOrder;
use Livewire\Attributes\On;
use Livewire\Component;

class PoVerification extends Component
{
    public ?PurchaseOrder $purchaseOrder = null;

    public $isOpen = false;

    public $notes = '';

    #[On('open-verification')]
    public function loadPo($poId)
    {
        $this->purchaseOrder = PurchaseOrder::with(['items.material', 'items.assignments.supplier', 'items.assignments.subSupplier'])->find($poId);
    }

    public function verify()
    {
        $this->authorizeVerification();

        $this->purchaseOrder->changeStatus(PoStatus::SELESAI, $this->notes ?: 'Diverifikasi oleh Kepala Dapur.');
        $this->purchaseOrder->update([
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        session()->flash('success', 'PO berhasil diverifikasi dan Invoice telah diterbitkan.');

        return redirect()->route('purchase-orders.show', $this->purchaseOrder);
    }

    public function markDeficit()
    {
        $this->authorizeVerification();

        foreach ($this->purchaseOrder->items as $item) {
            foreach ($item->assignments as $assignment) {
                $assignment->update(['is_fulfillment_closed' => true]);
            }
        }

        $this->purchaseOrder->changeStatus(PoStatus::DITERIMA_SEBAGIAN, 'Terdeteksi defisit oleh Kepala Dapur. Menunggu alokasi susulan.');

        session()->flash('warning', 'Defisit dilaporkan. Anda sekarang dapat mengalokasikan kekurangan barang ke supplier lain.');

        return redirect()->route('purchase-orders.show', $this->purchaseOrder);
    }

    private function authorizeVerification()
    {
        if (! auth()->user()->hasRole(['kepala_dapur', 'superadmin', 'admin_yayasan'])) {
            abort(403, 'Hanya Kepala Dapur yang dapat melakukan verifikasi ini.');
        }
    }

    public function render()
    {
        return view('livewire.po-verification');
    }
}
