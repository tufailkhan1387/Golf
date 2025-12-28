<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $serviceAccountPath = base_path('serviceAccount.json');
        
        if (!file_exists($serviceAccountPath)) {
            throw new \Exception('Firebase service account file not found at: ' . $serviceAccountPath);
        }

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send a notification to a single device token
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data Additional data payload (optional)
     * @return bool
     */
    public function sendNotification(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        try {
            if (empty($deviceToken)) {
                Log::warning('Firebase notification: Device token is empty');
                return false;
            }

            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification);

            // Add custom data if provided
            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $this->messaging->send($message);
            
            Log::info('Firebase notification sent successfully', [
                'device_token' => substr($deviceToken, 0, 20) . '...',
                'title' => $title
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase notification failed', [
                'error' => $e->getMessage(),
                'device_token' => substr($deviceToken, 0, 20) . '...',
                'title' => $title
            ]);

            return false;
        }
    }

    /**
     * Send a notification to multiple device tokens
     *
     * @param array $deviceTokens
     * @param string $title
     * @param string $body
     * @param array $data Additional data payload (optional)
     * @return array Results for each token
     */
    public function sendNotificationToMultiple(array $deviceTokens, string $title, string $body, array $data = []): array
    {
        $results = [];
        
        foreach ($deviceTokens as $token) {
            $results[$token] = $this->sendNotification($token, $title, $body, $data);
        }

        return $results;
    }

    /**
     * Send welcome notification to a new user
     *
     * @param string $deviceToken
     * @param string $userName
     * @return bool
     */
    public function sendWelcomeNotification(string $deviceToken, string $userName): bool
    {
        return $this->sendNotification(
            $deviceToken,
            'Welcome to Golf App!',
            "Hi {$userName}, welcome to our golf community! We're excited to have you on board.",
            [
                'type' => 'welcome',
                'action' => 'registration'
            ]
        );
    }
}
