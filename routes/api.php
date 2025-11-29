<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/SavePersonalizeInformationApi', [AuthController::class, 'SavePersonalizeInformationApi']);
Route::post('/cancel_tour', [AuthController::class, 'cancel_tour']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    // Logout route
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/get_profile', [AuthController::class, 'get_profile']);

    // Session routes
    Route::prefix('session')->group(function () {
        Route::post('/free-trial', [SessionController::class, 'startFreeTrial']);
        Route::post('/status', [SessionController::class, 'status']);
    });
});

// Subscription Plans API Routes
Route::prefix('subscription-plans')->group(function () {
    Route::get('/', [SubscriptionPlanController::class, 'index']);
    Route::post('/', [SubscriptionPlanController::class, 'store']);
    Route::get('/{id}', [SubscriptionPlanController::class, 'show']);
    Route::put('/{id}', [SubscriptionPlanController::class, 'update']);
    Route::delete('/{id}', [SubscriptionPlanController::class, 'destroy']);
    Route::get('/interval/{interval}', [SubscriptionPlanController::class, 'getByInterval']);
});

// Subscription Management API Routes
Route::prefix('subscriptions')->group(function () {
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/create', [SubscriptionController::class, 'createSubscription']);
    Route::post('/user-subscriptions', [SubscriptionController::class, 'getUserSubscriptions']);
    Route::post('/cancel', [SubscriptionController::class, 'cancelSubscription']);
    Route::post('/resume', [SubscriptionController::class, 'resumeSubscription']);
});

// Session API for free trial
