<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class WithdrawalStatusChanged extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public WithdrawalRequest $withdrawalRequest,
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
        $amount = number_format($this->withdrawalRequest->amount, 0, ',', '.');
        
        return [
            'withdrawal_request_id' => $this->withdrawalRequest->id,
            'title' => 'Update Penarikan Saldo',
            'message' => "Permintaan penarikan Rp{$amount} Anda telah {$this->statusLabel}.",
            'url' => route('investor.dashboard'), // Fixed route name
        ];
    }
}
