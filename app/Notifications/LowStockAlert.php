<?php

namespace App\Notifications;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public Stock $stock
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
            'stock_id' => $this->stock->id,
            'material_name' => $this->stock->material->name,
            'title' => 'Peringatan Stok Rendah!',
            'message' => "Stok {$this->stock->material->name} di {$this->stock->dapur->name} mencapai batas kritis: ".number_format($this->stock->current_stock, 1)." {$this->stock->material->unit}.",
            'url' => route('kitchen.inventory'), // Fixed route name
        ];
    }
}
