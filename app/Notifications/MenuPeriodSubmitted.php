<?php

namespace App\Notifications;

use App\Models\MenuPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MenuPeriodSubmitted extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public MenuPeriod $menuPeriod,
        public string $submitterName
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
            'menu_period_id' => $this->menuPeriod->id,
            'title' => 'Pengajuan Menu Baru',
            'message' => "{$this->submitterName} telah mengajukan rencana menu baru untuk periode: ".($this->menuPeriod->period?->name ?? 'N/A'),
            'url' => route('menu-periods.show', $this->menuPeriod),
        ];
    }
}
