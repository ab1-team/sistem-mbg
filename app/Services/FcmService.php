<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected string $projectId;

    protected string $credentialsPath;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id', env('FIREBASE_PROJECT_ID'));
        $path = config('services.fcm.credentials', env('FIREBASE_CREDENTIALS'));
        $this->credentialsPath = base_path($path);
    }

    /**
     * Send a push notification via FCM v1 API.
     */
    public function sendNotification(string $token, string $title, string $body, ?string $url = null, array $data = [])
    {
        if (empty($token)) {
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (! $accessToken) {
            Log::error('FCM: Failed to get access token.');

            return false;
        }

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'webpush' => [
                    'fcm_options' => [
                        'link' => $url ?? config('app.url'),
                    ],
                ],
                'data' => array_merge($data, [
                    'click_action' => $url ?? config('app.url'),
                ]),
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($endpoint, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('FCM Error: '.$response->body());

        return false;
    }

    /**
     * Get OAuth2 Access Token using Service Account.
     */
    protected function getAccessToken(): ?string
    {
        try {
            if (! file_exists($this->credentialsPath)) {
                // If it's not a file path, check if it's the JSON content directly
                $tempPath = tempnam(sys_get_temp_dir(), 'fcm_cred');
                file_put_contents($tempPath, $this->credentialsPath);
                $this->credentialsPath = $tempPath;
            }

            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/cloud-platform'],
                $this->credentialsPath
            );

            $token = $credentials->fetchAuthToken(HttpHandlerFactory::build());

            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('FCM Auth Error: '.$e->getMessage());

            return null;
        }
    }
}
