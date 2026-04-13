<?php

namespace App\Notifications;

use App\Models\Period;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PeriodStatusChanged extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public Period $period,
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
        return [
            'period_id' => $this->period->id,
            'title' => 'Pengumuman Sistem: Periode Keuangan',
            'message' => "Periode {$this->period->name} telah {$this->statusLabel}. Mohon perhatikan batasan input data.",
            'url' => route('dashboard'),
        ];
    }
}
