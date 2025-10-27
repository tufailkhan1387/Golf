<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\SubscriptionController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
    Route::get('/user-subscriptions', [SubscriptionController::class, 'getUserSubscriptions']);
    Route::post('/cancel', [SubscriptionController::class, 'cancelSubscription']);
    Route::post('/resume', [SubscriptionController::class, 'resumeSubscription']);
});
