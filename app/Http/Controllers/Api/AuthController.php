<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Subscription;
use App\Services\FirebaseNotificationService;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_token' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' =>'0',
                'message' => 'Validation error',
                'data' => (object)[]
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => '0',
                'message' => 'Invalid credentials',
                'data' => (object)[]
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Profile completion check
        $profileCompleted = !(
            empty($user->focus) ||
            empty($user->skill) ||
            empty($user->challenge)
        );

        // Fetch subscription
        $subscription = Subscription::where('user_id', $user->id)
            ->where('email', $user->email)
            ->first();

        // If no subscription
        if (!$subscription) {
            return response()->json([
                'status' => '1',
                'message' => 'No subscription found',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'is_tour' => $user->is_tour ?? false,
                        'profile_completed' => $profileCompleted,
                        'isFreeTrial' => false,
                        'isSubscribe' => false,
                        'trial_started_at' => null,
                        'trial_ends_at' => null,
                        'expired' => false,
                    ],
                    'token' => $token
                ]
            ]);
        }

        // Subscription found → check expiry
        $now = Carbon::now();
        $expired = $subscription->trial_ends_at && $now->greaterThan($subscription->trial_ends_at);

        // Disable flags if expired
        if ($expired && ($subscription->isFreeTrial || $subscription->isSubscribe)) {
            $subscription->isFreeTrial = false;
            $subscription->isSubscribe = false;
            $subscription->save();
        }

        return response()->json([
            'status' => '1',
            'message' => $expired ? 'Free trial expired' : 'Session status fetched',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_tour' => $user->is_tour ?? false,
                    'profile_completed' => $profileCompleted,
                    'isFreeTrial' => $subscription->isFreeTrial,
                    'isSubscribe' => $subscription->isSubscribe,
                    'trial_started_at' => $subscription->trial_started_at,
                    'trial_ends_at' => $subscription->trial_ends_at,
                    'expired' => $expired,
                ],
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke current access token
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => '1',
            'message' => 'Logout successful',
            'data'    => (object)[]
        ]);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // 'phone' => 'required|string|unique:users,phone',
            'password' => 'required|min:6',
            'device_token' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '0',
                'message' => 'Validation error',
                'data' => (object)[],
                'errors' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'is_tour' => false,
            // 'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'device_token' => $request->device_token
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Send welcome notification if device_token is provided
        if (!empty($request->device_token)) {
            try {
                $firebaseService = new FirebaseNotificationService();
                $firebaseService->sendWelcomeNotification($request->device_token, $user->name);
            } catch (\Exception $e) {
                // Log error but don't fail registration
                Log::error('Failed to send welcome notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => '1',
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_tour' => $user->is_tour
                ],
                'token' => $token
            ]
        ]);
    }

    public function SavePersonalizeInformationApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'focus' => 'required|string',
            'skill' => 'required|string',
            'fName' => 'required|string',
            'challenge' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '0',
                'message' => 'Validation error',
                'data' => (object)[],
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::find($request->user_id); // ✅ Corrected from findOne() to find()

        $user->focus = $request->focus;
        $user->skill = $request->skill;
        $user->fName = $request->fName;
        $user->challenge = $request->challenge;
        $user->save();

        return response()->json([
            'status' => '1',
            'message' => 'Information added successfully',
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    public function cancel_tour(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '0',
                'message' => 'Validation error',
                'data' => (object)[],
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::find($request->user_id);
        $user->is_tour = true;
        $user->save();
        return response()->json([
            'status' => '1',
            'message' => 'Tour skipped',
            'data' => [
                'user' => $user,
            ],
        ]);
    }
    public function get_profile(Request $request)
    {

        $user = Auth::user();

        return response()->json([
            'status' => '1',
            'message' => 'User Data',
            'data' => [
                'user' => $user,
            ],
        ]);
    }
}
