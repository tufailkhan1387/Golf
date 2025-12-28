
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::get('logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        
        // Plans Management
        Route::get('/plans', [AdminController::class, 'plans'])->name('admin.plans');
        Route::get('/plans/{id}/edit', [AdminController::class, 'editPlan'])->name('admin.plans.edit');
        Route::put('/plans/{id}', [AdminController::class, 'updatePlan'])->name('admin.plans.update');
        
        // Email Notifications
        Route::get('/email-notifications', [AdminController::class, 'emailNotifications'])->name('admin.email-notifications');
        Route::post('/email-notifications/send', [AdminController::class, 'sendEmailNotification'])->name('admin.email-notifications.send');
        Route::get('/email-notifications/history', [AdminController::class, 'emailNotificationsHistory'])->name('admin.email-notifications.history');
        
        // Push Notifications (Firebase)
        Route::get('/push-notifications', [AdminController::class, 'pushNotifications'])->name('admin.push-notifications');
        Route::post('/push-notifications/send', [AdminController::class, 'sendPushNotification'])->name('admin.push-notifications.send');
    });
});

// Checkout success and cancel pages
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
