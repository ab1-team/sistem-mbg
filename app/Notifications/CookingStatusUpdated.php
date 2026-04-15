<?php

namespace App\Notifications;

use App\Models\CookingSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CookingStatusUpdated extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public CookingSchedule $schedule,
        public string $statusLabel
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
        $menuName = $this->schedule->menuSchedule->menuItem->name;

        return [
            'cooking_schedule_id' => $this->schedule->id,
            'title' => 'Update Operasional Dapur',
            'message' => "Proses masak [{$menuName}] di {$this->schedule->dapur->name} kini berstatus: {$this->statusLabel}.",
            'url' => route('kitchen.index', [], false),
        ];
    }
}
