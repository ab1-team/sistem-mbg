<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewPOAssigned extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public PurchaseOrder $purchaseOrder
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
            'title' => 'Pesanan Baru Diterima',
            'message' => "Anda telah menerima pesanan baru (PO: {$this->purchaseOrder->po_number}). Mohon segera dikonfirmasi.",
            'url' => route('supplier.purchase-orders.show', $this->purchaseOrder->id), // Fixed context: supplier portal
        ];
    }
}
