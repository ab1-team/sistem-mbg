<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderStatusChanged extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public PurchaseOrder $purchaseOrder,
        public string $statusLabel,
        public ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function toArray($notifiable): array
    {
        return [
            'purchase_order_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->po_number,
            'title' => "Update Status PO: {$this->purchaseOrder->po_number}",
            'message' => "Pesanan {$this->purchaseOrder->po_number} kini berstatus: {$this->statusLabel}.".($this->reason ? " Alasan: {$this->reason}" : ''),
            'url' => route('purchase-orders.show', $this->purchaseOrder, false),
        ];
    }
}
