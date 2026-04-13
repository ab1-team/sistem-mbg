<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class InvoicePaid extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
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
        $amount = number_format($this->invoice->total_amount, 0, ',', '.');

        return [
            'invoice_id' => $this->invoice->id,
            'title' => 'Pembayaran Invoice Selesai',
            'message' => "Tagihan Anda (Inv: {$this->invoice->invoice_number}) sebesar Rp{$amount} telah dibayar oleh Yayasan.",
            'url' => route('supplier.purchase-orders.show', $this->invoice->purchase_order_id),
        ];
    }
}
