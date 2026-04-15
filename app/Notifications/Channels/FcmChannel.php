<?php

namespace App\Notifications\Channels;

use App\Services\FcmService;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function __construct(
        protected FcmService $fcmService
    ) {}

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        $token = $notifiable->routeNotificationFor('fcm', $notification);

        if (! $token) {
            return;
        }

        if (! method_exists($notification, 'toFcm')) {
            return;
        }

        $payload = $notification->toFcm($notifiable);

        if (! $payload) {
            return;
        }

        $this->fcmService->sendNotification(
            $token,
            $payload['title'],
            $payload['body'],
            $payload['url'] ?? null,
            $payload['data'] ?? []
        );
    }
}
