<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmController extends Controller
{
    /**
     * Save the FCM token for the authenticated user.
     */
    public function saveToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();
        if ($user) {
            FcmToken::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'token' => $request->token,
                ],
                [
                    'last_used_at' => now(),
                ]
            );

            return response()->json(['message' => 'Token saved successfully.']);
        }

        return response()->json(['message' => 'User not authenticated.'], 401);
    }

    /**
     * Serve the Firebase Service Worker JS dynamically.
     */
    public function serviceWorker()
    {
        $config = config('services.fcm');

        $js = <<<JS
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "{$config['api_key']}",
    authDomain: "{$config['auth_domain']}",
    projectId: "{$config['project_id']}",
    storageBucket: "{$config['storage_bucket']}",
    messagingSenderId: "{$config['messaging_sender_id']}",
    appId: "{$config['app_id']}"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/favicon.ico',
        data: {
            url: payload.data.click_action || payload.data.url || '/'
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
JS;

        return response($js)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
