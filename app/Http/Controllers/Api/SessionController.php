<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseNotificationService;

class SessionController extends Controller
{
    /**
     * Start a 7-day free trial for a user.
     */

    public function startFreeTrial(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Ensure the user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
                'data' => (object)[]
            ], 401); // Unauthorized
        }

        // Current time and the trial end time (7 days from now)
        $now = Carbon::now();
        $endsAt = $now->copy()->addDays(7);

        // Retrieve or create the subscription record for the user
        $subscription = Subscription::firstOrNew([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Check if trial is expired and reset if necessary
        if ($subscription->exists && $subscription->trial_ends_at && $now->greaterThan($subscription->trial_ends_at)) {
            $subscription->isFreeTrial = false;
            $subscription->isSubscribe = false;
        }

        // If already on active trial (not expired), return early
        if ($subscription->exists && $subscription->isFreeTrial && $subscription->trial_ends_at && $now->lessThanOrEqualTo($subscription->trial_ends_at)) {
            return response()->json([
                'success' => true,
                'message' => 'Free trial already active',
                'data' => [
                    'user_id' => $subscription->user_id,
                    'email' => $subscription->email,
                    'isFreeTrial' => $subscription->isFreeTrial ?? false,
                    'isSubscribe' => $subscription->isSubscribe ?? false,
                    'trial_started_at' => $subscription->trial_started_at,
                    'trial_ends_at' => $subscription->trial_ends_at,
                ],
            ], 200); // OK status
        }

        // Set new trial values
        $subscription->user_id = $user->id;
        $subscription->email = $user->email;
        $subscription->isFreeTrial = true;
        $subscription->isSubscribe = true;
        $subscription->trial_started_at = $now;
        $subscription->trial_ends_at = $endsAt;
        $subscription->save();

        // Send free trial started notification to user
        if (!empty($user->device_token)) {
            try {
                $firebaseService = new FirebaseNotificationService();
                $firebaseService->sendNotification(
                    $user->device_token,
                    'Free Trial Started! 🎉',
                    "Hi {$user->name}! Your 7-day free trial has started. Enjoy full access to all premium features until " . $endsAt->format('M d, Y') . "!",
                    [
                        'type' => 'free_trial_started',
                        'action' => 'trial_started',
                        'trial_ends_at' => $endsAt->toIso8601String(),
                    ]
                );
            } catch (\Exception $e) {
                // Log error but don't fail trial creation
                Log::error('Failed to send free trial notification: ' . $e->getMessage());
            }
        }

        // Return the response
        return response()->json([
            'success' => true,
            'message' => 'Free trial started for 7 days',
            'data' => [
                'user_id' => $subscription->user_id,
                'email' => $subscription->email,
                'isFreeTrial' => $subscription->isFreeTrial ?? false,
                'isSubscribe' => $subscription->isSubscribe ?? false,
                'trial_started_at' => $subscription->trial_started_at,
                'trial_ends_at' => $subscription->trial_ends_at,
            ],
        ], 201); // Created status
    }



    /**
     * Get session status; if free trial expired, deactivate flags.
     */
    public function status(Request $request)
    {
        $user = auth()->user();

        // Fetch subscription using authenticated user's ID & email
        $subscription = Subscription::where('user_id', $user->id)
            ->where('email', $user->email)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'message' => 'No subscription found',
                'data' => [
                    'user_id' => (int) $user->id,
                    'email' => $user->email,
                    'is_tour' => $user->is_tour ?? false,
                    'isFreeTrial' => false,
                    'isSubscribe' => false,
                    'trial_started_at' => null,
                    'trial_ends_at' => null,
                    'expired' => false,
                ],
            ]);
        }

        $now = Carbon::now();
        $expired = $subscription->trial_ends_at && $now->greaterThan($subscription->trial_ends_at);

        // If expired, remove trial/subscribe flags
        if ($expired && ($subscription->isFreeTrial || $subscription->isSubscribe)) {
            $subscription->isFreeTrial = false;
            $subscription->isSubscribe = false;
            $subscription->save();
        }

        return response()->json([
            'success' => true,
            'message' => $expired ? 'Free trial expired' : 'Session status fetched',
            'data' => [
                'user_id' => $subscription->user_id,
                'email' => $subscription->email,
                'isFreeTrial' => $subscription->isFreeTrial,
                'is_tour' => $user->is_tour ?? false,
                'isSubscribe' => $subscription->isSubscribe,
                'trial_started_at' => $subscription->trial_started_at,
                'trial_ends_at' => $subscription->trial_ends_at,
                'expired' => $expired,
            ],
        ]);
    }
}
