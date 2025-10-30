<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Subscription;

class SessionController extends Controller
{
    /**
     * Start a 7-day free trial for a user.
     */
    public function startFreeTrial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $now = Carbon::now();
        $endsAt = $now->copy()->addDays(7);

        $subscription = Subscription::firstOrNew([
            'user_id' => $request->user_id,
            'email' => $request->email,
        ]);

        // If existing and trial active but expired, reset
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
                    'isFreeTrial' => $subscription->isFreeTrial,
                    'isSubscribe' => $subscription->isSubscribe,
                    'trial_started_at' => $subscription->trial_started_at,
                    'trial_ends_at' => $subscription->trial_ends_at,
                ],
            ]);
        }

        $subscription->user_id = $request->user_id;
        $subscription->email = $request->email;
        $subscription->isFreeTrial = true;
        $subscription->isSubscribe = true;
        $subscription->trial_started_at = $now;
        $subscription->trial_ends_at = $endsAt;
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Free trial started for 7 days',
            'data' => [
                'user_id' => $subscription->user_id,
                'email' => $subscription->email,
                'isFreeTrial' => $subscription->isFreeTrial,
                'isSubscribe' => $subscription->isSubscribe,
                'trial_started_at' => $subscription->trial_started_at,
                'trial_ends_at' => $subscription->trial_ends_at,
            ],
        ], 201);
    }

    /**
     * Get session status; if free trial expired, deactivate flags.
     */
    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subscription = Subscription::where('user_id', $request->user_id)
            ->where('email', $request->email)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'message' => 'No subscription found',
                'data' => [
                    'user_id' => (int) $request->user_id,
                    'email' => $request->email,
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
                'isSubscribe' => $subscription->isSubscribe,
                'trial_started_at' => $subscription->trial_started_at,
                'trial_ends_at' => $subscription->trial_ends_at,
                'expired' => $expired,
            ],
        ]);
    }
}


