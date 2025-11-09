<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '0',
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

        // ✅ Determine if profile is completed
        $profileCompleted = !(
            empty($user->focus) ||
            empty($user->skill) ||
            empty($user->challenge)
        );

        return response()->json([
            'status' => '1',
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_tour' => $user->is_tour ?? false,
                    'profile_completed' => $profileCompleted,
                ],
                'token' => $token
            ]
        ]);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // 'phone' => 'required|string|unique:users,phone',
            'password' => 'required|min:6'
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
            // 'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => '1',
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    // 'phone' => $user->phone
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
        $user->is_tour = false;
        $user->save();
        return response()->json([
            'status' => '1',
            'message' => 'Tour skipped',
            'data' => [
                'user' => $user,
            ],
        ]);
    }
}
