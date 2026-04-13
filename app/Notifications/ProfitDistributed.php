<?php

namespace App\Notifications;

use App\Models\DividendDistribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ProfitDistributed extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public DividendDistribution $distribution,
        public string $periodName
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
        $amount = number_format($this->distribution->amount, 0, ',', '.');

        return [
            'dividend_distribution_id' => $this->distribution->id,
            'title' => 'Bagi Hasil Tersedia',
            'message' => "Bagi hasil untuk periode {$this->periodName} telah dikreditkan ke dompet Anda sebesar Rp{$amount}.",
            'url' => route('investor.dashboard'),
        ];
    }
}
