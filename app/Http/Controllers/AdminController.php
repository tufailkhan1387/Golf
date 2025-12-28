<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\EmailNotification;
use App\Mail\AdminNotification;
use App\Services\FirebaseNotificationService;

class AdminController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Invalid credentials');
        }

        if ($user->role !== 'admin') {
            return back()->with('error', 'Unauthorized access. Admin privileges required.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid credentials');
        }

        // Login the user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->name);
    }

    public function dashboard()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        // Calculate statistics
        $totalUsers = User::count();
        $totalPlans = SubscriptionPlan::count();
        $subscribedUsers = Subscription::where('isSubscribe', true)->count();
        
        // Calculate total revenue from subscription items and plans
        $totalRevenue = \DB::table('subscription_items')
            ->join('subscription_plans', 'subscription_items.stripe_price', '=', 'subscription_plans.stripe_price_id')
            ->sum('subscription_plans.amount');

        // If no data, set to 0
        $totalRevenue = $totalRevenue ?? 0;

        // Get users registered this month
        $monthlyUsers = User::where('role', 'user')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->limit(5) // Limit to 5 for the dashboard widget
            ->get();

        // Prepare chart data (Users registered per month for current year)
        $userRegistrations = User::where('role', 'user')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $userRegistrations[$i] ?? 0;
        }

        return view('admin.dashboard', compact('totalUsers', 'totalPlans', 'subscribedUsers', 'totalRevenue', 'monthlyUsers', 'chartData'));
    }

    /**
     * Display all users
     */
    public function users()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        // Get all users with pagination
        // $users = User::orderBy('id', 'desc')->where('role','user')->paginate(10);
        $users = User::orderBy('id', 'desc')->where('role','user')->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        // Get user with subscription details
        $user = User::findOrFail($id);
        
        // Get user's subscription
        $subscription = Subscription::where('user_id', $id)->first();
        
        // Get subscription plan if user has a subscription with stripe price
        $subscriptionPlan = null;
        if ($subscription) {
            // Try to find the plan through subscription_items
            $subscriptionItem = \DB::table('subscription_items')
                ->where('subscription_id', $subscription->id)
                ->first();
            
            if ($subscriptionItem) {
                $subscriptionPlan = SubscriptionPlan::where('stripe_price_id', $subscriptionItem->stripe_price)->first();
            }
        }

        return view('admin.user-details', compact('user', 'subscription', 'subscriptionPlan'));
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        $user = User::findOrFail($id);
        
        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Delete user and related data (cascading should handle relations if set up, otherwise manual cleanup might be needed)
        // For now assuming standard deletion
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    /**
     * Revenue Reports
     */
    public function reports(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = \DB::table('subscription_items')
            ->join('subscription_plans', 'subscription_items.stripe_price', '=', 'subscription_plans.stripe_price_id')
            ->join('subscription', 'subscription_items.subscription_id', '=', 'subscription.id')
            ->join('users', 'subscription.user_id', '=', 'users.id')
            ->select(
                'subscription_items.*', 
                'subscription_plans.name as plan_name', 
                'subscription_plans.amount', 
                'subscription_plans.currency',
                'users.name as user_name',
                'users.email as user_email'
            );

        if ($startDate) {
            $query->whereDate('subscription_items.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('subscription_items.created_at', '<=', $endDate);
        }

        // Clone query for total calculation
        $totalRevenue = $query->sum('subscription_plans.amount');
        
        // Get paginated results
        $transactions = $query->orderBy('subscription_items.created_at', 'desc')->paginate(15);

        return view('admin.reports', compact('transactions', 'totalRevenue', 'startDate', 'endDate'));
    }

    /**
     * List Subscription Plans
     */
    public function plans()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $products = \Stripe\Product::all(['active' => true, 'type' => 'service']);
        $plans = [];

        foreach ($products->data as $product) {
            // Get prices for each product
            $prices = \Stripe\Price::all(['product' => $product->id, 'active' => true]);

            foreach ($prices->data as $price) {
                if (isset($price->recurring)) {
                    // Check if local plan exists to get custom description/features
                    $localPlan = SubscriptionPlan::where('stripe_price_id', $price->id)->first();

                    $plans[] = (object) [
                        'id' => $price->id, // Stripe Price ID
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'description' => $localPlan ? $localPlan->description : $product->description,
                        'amount' => $price->unit_amount / 100,
                        'currency' => $price->currency,
                        'interval' => $price->recurring->interval,
                        'trial_days' => 7,
                        'is_active' => $price->active, // Stripe active status
                        'features' => $localPlan ? $localPlan->features : [],
                        'local_id' => $localPlan ? $localPlan->id : null
                    ];
                }
            }
        }

        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Edit Subscription Plan
     */
    public function editPlan($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        // $id is expected to be stripe_price_id now
        $localPlan = SubscriptionPlan::where('stripe_price_id', $id)->first();
        
        if ($localPlan) {
            $plan = $localPlan;
        } else {
            // If no local plan, fetch from Stripe to populate form
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            try {
                $price = \Stripe\Price::retrieve($id);
                $product = \Stripe\Product::retrieve($price->product);
                
                $plan = new SubscriptionPlan();
                $plan->stripe_price_id = $price->id;
                $plan->stripe_product_id = $product->id;
                $plan->name = $product->name;
                $plan->amount = $price->unit_amount / 100;
                $plan->currency = $price->currency;
                $plan->interval = $price->recurring->interval;
                $plan->description = $product->description;
                $plan->is_active = $price->active;
                $plan->features = []; // Default empty
            } catch (\Exception $e) {
                return back()->with('error', 'Plan not found in Stripe.');
            }
        }

        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update Subscription Plan
     */
    public function updatePlan(Request $request, $id)
    {
        
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'interval' => 'required|in:day,week,month,year',
            'trial_days' => 'required|integer',
            'features' => 'sometimes|string', // Changed to string as it comes from textarea
            'ispopular' => 'sometimes|boolean'
        ]);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve the existing price
            $price = \Stripe\Price::retrieve($id);

            // Retrieve the product associated with this price
            $product = \Stripe\Product::retrieve($price->product);
           

            // Update the product (name, description)
            $productUpdateData = [
                'name' => $request->name,
                'description' => $request->description,
            ];

            // Add popular flag to product metadata
            $metadata = [];
            if ($request->has('ispopular')) {
                $metadata['popular'] = $request->ispopular ? 'true' : 'false';
            }

            // Update features in metadata if provided
            if ($request->has('features')) {
                // Convert newline separated string to array for JSON storage if needed, 
                // or store as is. The user code suggested json_encode array.
                // Let's convert the textarea string to array first.
                $featuresArray = array_filter(array_map('trim', explode("\n", $request->features)));
                $metadata['features'] = json_encode($featuresArray);
            }
            
            if (!empty($metadata)) {
                $productUpdateData['metadata'] = $metadata;
            }

            \Stripe\Product::update($product->id, $productUpdateData);

            // Since Stripe prices are immutable, we need to create a new price
            // and deactivate the old one if any pricing details changed
            $priceChanged = (
                abs(($price->unit_amount / 100) - $request->amount) > 0.01 || // Float comparison
                $price->currency != $request->currency ||
                $price->recurring->interval != $request->interval
            );

            if ($priceChanged) {
                // Create a new price with updated details first
                $newPrice = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => (int)($request->amount * 100), // Convert to cents
                    'currency' => $request->currency,
                    'recurring' => [
                        'interval' => $request->interval,
                    ],
                    'active' => true,
                ]);

                // Update the product's default price to the new price FIRST
                // This must be done before deactivating the old price
                \Stripe\Product::update($product->id, [
                    'default_price' => $newPrice->id
                ]);

                // Now we can safely deactivate the old price
                // (it's no longer the default, so Stripe will allow it)
                try {
                    \Stripe\Price::update($price->id, ['active' => false]);
                } catch (\Exception $e) {
                    // If deactivation fails, log but don't fail the update
                    // The new price is already set as default, which is what matters
                    Log::warning('Could not deactivate old price: ' . $e->getMessage());
                }
                
                // Update local plan if exists to point to new price ID
                $localPlan = SubscriptionPlan::where('stripe_price_id', $id)->first();
                if ($localPlan) {
                    $localPlan->stripe_price_id = $newPrice->id;
                    $localPlan->save();
                }
            }

            // Update local plan details as well to keep in sync
            // We use the NEW price ID if changed, or the OLD one if not
            $targetPriceId = $priceChanged ? $newPrice->id : $id;
            
            $localPlan = SubscriptionPlan::firstOrNew(['stripe_price_id' => $targetPriceId]);
            $localPlan->stripe_product_id = $product->id;
            $localPlan->name = $request->name;
            $localPlan->description = $request->description;
            $localPlan->amount = $request->amount;
            $localPlan->currency = $request->currency;
            $localPlan->interval = $request->interval;
            $localPlan->trial_days = $request->trial_days;
            $localPlan->features = isset($featuresArray) ? $featuresArray : [];
            $localPlan->is_active = true; // Assuming active if we just updated it
            $localPlan->save();

            return redirect()->route('admin.plans')->with('success', 'Plan updated successfully on Stripe and locally.');

        } catch (\Exception $e) {
            
            return back()->with('error', 'Error updating plan: ' . $e->getMessage());
        }
    }

    /**
     * Show Email Notifications page
     */
    public function emailNotifications()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        // Get all users for selection
        $users = User::where('role', 'user')->orderBy('name', 'asc')->get();

        return view('admin.email-notifications', compact('users'));
    }

    /**
     * Send email notification to selected user(s)
     */
    public function sendEmailNotification(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            
            // Create email notification record
            $emailNotification = EmailNotification::create([
                'user_id' => $user->id,
                'recipient_email' => $user->email,
                'recipient_name' => $user->name,
                'subject' => $request->subject,
                'message' => $request->message,
                'sent_by' => Auth::id(),
                'status' => 'sent',
            ]);

            // Send email
            Mail::to($user->email)->send(new AdminNotification(
                $request->subject,
                $request->message,
                $user->name
            ));

            return redirect()->route('admin.email-notifications')
                ->with('success', 'Email notification sent successfully to ' . $user->email);
        } catch (\Exception $e) {
            // Save failed email record if user was found
            if (isset($user)) {
                EmailNotification::create([
                    'user_id' => $user->id,
                    'recipient_email' => $user->email,
                    'recipient_name' => $user->name,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'sent_by' => Auth::id(),
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return redirect()->route('admin.email-notifications')
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Show Email Notifications History
     */
    public function emailNotificationsHistory(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        // Get email notifications with pagination
        $emailNotifications = EmailNotification::with(['user', 'sender'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.email-notifications-history', compact('emailNotifications'));
    }

    /**
     * Show Push Notifications page
     */
    public function pushNotifications()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        // Get all users with device tokens for selection
        $users = User::where('role', 'user')
            ->whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->orderBy('name', 'asc')
            ->get();

        // Also get all users for manual device token entry option
        $allUsers = User::where('role', 'user')->orderBy('name', 'asc')->get();

        return view('admin.push-notifications', compact('users', 'allUsers'));
    }

    /**
     * Send push notification to selected user or device token
     */
    public function sendPushNotification(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'device_token' => 'required_without:user_id|string',
            'user_id' => 'required_without:device_token|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'device_token.required_without' => 'Either device token or user must be selected.',
            'user_id.required_without' => 'Either device token or user must be selected.',
        ]);

        try {
            $deviceToken = null;
            $user = null;
            $userName = 'User';

            // If user_id is provided, get device token from user
            if ($request->user_id) {
                $user = User::findOrFail($request->user_id);
                if (empty($user->device_token)) {
                    return redirect()->route('admin.push-notifications')
                        ->with('error', 'Selected user does not have a device token. Please use manual device token entry instead.');
                }
                $deviceToken = $user->device_token;
                $userName = $user->name;
            } else {
                // Use provided device token directly
                $deviceToken = $request->device_token;
            }

            // Send push notification
            $firebaseService = new FirebaseNotificationService();
            $success = $firebaseService->sendNotification(
                $deviceToken,
                $request->title,
                $request->message,
                [
                    'type' => 'admin_notification',
                    'action' => 'admin_sent',
                    'sent_by' => Auth::id(),
                ]
            );

            if ($success) {
                $recipientInfo = $user ? "{$user->name} ({$user->email})" : "Device Token: " . substr($deviceToken, 0, 20) . '...';
                return redirect()->route('admin.push-notifications')
                    ->with('success', 'Push notification sent successfully to ' . $recipientInfo);
            } else {
                return redirect()->route('admin.push-notifications')
                    ->with('error', 'Failed to send push notification. Please check the device token and try again.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send push notification: ' . $e->getMessage());
            return redirect()->route('admin.push-notifications')
                ->with('error', 'Failed to send push notification: ' . $e->getMessage());
        }
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}
