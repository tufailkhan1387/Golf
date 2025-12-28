<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription as LocalSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Product;
use Stripe\PaymentMethod;
use Stripe\Exception\ApiErrorException;
use App\Services\FirebaseNotificationService;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create a subscription for a user
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'price_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateStripeCustomer($request->user_id);

            // Get price details to calculate amount
            $price = Price::retrieve($request->price_id);
            $amount = $price->unit_amount; // Amount in cents

            // Create Payment Intent to collect payment method for subscription
            // Using setup_future_usage to save payment method for recurring charges
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $price->currency ?? 'usd',
                'customer' => $customer->id,
                'setup_future_usage' => 'off_session', // Save payment method for subscription
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $request->user_id,
                    'price_id' => $request->price_id,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment intent created successfully',
                'data' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'customer_id' => $customer->id,
                    'user_id' => $request->user_id,
                    'amount' => $amount / 100, // Convert cents to dollars
                    'currency' => $price->currency ?? 'usd',
                ]
            ], 201);
        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create subscription after payment intent is confirmed
     */
    public function createSubscription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'payment_intent_id' => 'required|string',
            'price_id' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Retrieve the payment intent to get the payment method
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded' && $paymentIntent->status !== 'requires_capture') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment intent is not in a valid state. Status: ' . $paymentIntent->status
                ], 400);
            }

            // Get the payment method ID from the payment intent
            $paymentMethodId = $paymentIntent->payment_method;

            if (!$paymentMethodId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payment method found in payment intent'
                ], 400);
            }

            // Get or create Stripe customer
            $customer = $this->getOrCreateStripeCustomer($request->user_id);

            // Attach payment method to customer if not already attached
            try {
                $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
                if ($paymentMethod->customer !== $customer->id) {
                    $paymentMethod->attach(['customer' => $customer->id]);
                }
            } catch (\Exception $e) {
                // Payment method might already be attached
            }

            // Set as default payment method for the customer
            Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            // Create subscription with the payment method
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => $request->price_id,
                ]],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                ],
                'expand' => ['latest_invoice.payment_intent'],
                'trial_period_days' => 7, // 7-day free trial
                'metadata' => [
                    'user_id' => $request->user_id,
                    'payment_intent_id' => $request->payment_intent_id,
                ],
            ]);

            // Get user email from request
            $userEmail = $request->email;
            $dbUserId = null;

            // Try to find user by email in database
            $user = User::where('email', $userEmail)->first();
            if ($user) {
                $dbUserId = $user->id;
            }

            // If user not found by email, try to find by user_id if it's a database ID
            if (!$dbUserId && is_numeric($request->user_id)) {
                $user = User::find($request->user_id);
                if ($user) {
                    $dbUserId = $user->id;
                    // Update email if different
                    if ($user->email !== $userEmail) {
                        $userEmail = $user->email;
                    }
                }
            }

            // Update Stripe customer email if not set
            if (!$customer->email && $userEmail) {
                Customer::update($customer->id, [
                    'email' => $userEmail,
                ]);
            }

            // When user subscribes after payment, set free-trial to false
            // They are now a paying subscriber, not on free trial
            $isFreeTrial = false;

            // Save subscription to local database
            // Only save if we have a valid database user ID
            $localSubscription = null;
            if ($dbUserId) {
                $localSubscription = LocalSubscription::updateOrCreate(
                    [
                        'user_id' => $dbUserId,
                        'email' => $userEmail,
                    ],
                    [
                        'isFreeTrial' => $isFreeTrial,
                        'isSubscribe' => true,
                        'trial_started_at' => $subscription->trial_start ? date('Y-m-d H:i:s', $subscription->trial_start) : null,
                        'trial_ends_at' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                    ]
                );

                // Send subscription success notification to user
                // Refresh user to ensure we have the latest data including device_token
                $user = User::find($dbUserId);
                if ($user && !empty($user->device_token)) {
                    try {
                        $firebaseService = new FirebaseNotificationService();
                        
                        // Get plan name if available
                        $planName = 'your subscription plan';
                        try {
                            $price = Price::retrieve($request->price_id);
                            $product = Product::retrieve($price->product);
                            $planName = $product->name ?? $planName;
                        } catch (\Exception $e) {
                            // If we can't get plan name, use default
                        }

                        $firebaseService->sendNotification(
                            $user->device_token,
                            'Subscription Successful! 🎉',
                            "Congratulations {$user->name}! Your subscription to {$planName} has been activated successfully. Enjoy your premium access!",
                            [
                                'type' => 'subscription_success',
                                'action' => 'subscription_created',
                                'subscription_id' => $subscription->id,
                            ]
                        );
                    } catch (\Exception $e) {
                        // Log error but don't fail subscription creation
                        Log::error('Failed to send subscription notification: ' . $e->getMessage());
                    }
                }
            }

            $responseData = [
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
                'trial_start' => $subscription->trial_start ? date('Y-m-d H:i:s', $subscription->trial_start) : null,
                'trial_end' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
            ];

            // Add local subscription data if saved
            if ($localSubscription) {
                $responseData['local_subscription'] = [
                    'id' => $localSubscription->id,
                    'user_id' => $localSubscription->user_id,
                    'email' => $localSubscription->email,
                    'isFreeTrial' => $localSubscription->isFreeTrial,
                    'isSubscribe' => $localSubscription->isSubscribe,
                    'trial_started_at' => $localSubscription->trial_started_at ? $localSubscription->trial_started_at->format('Y-m-d H:i:s') : null,
                    'trial_ends_at' => $localSubscription->trial_ends_at ? $localSubscription->trial_ends_at->format('Y-m-d H:i:s') : null,
                ];
            } else {
                $responseData['warning'] = 'Subscription created in Stripe but not saved to local database. User not found in database.';
            }

            return response()->json([
                'success' => true,
                'message' => $localSubscription ? 'Subscription created and assigned to user successfully' : 'Subscription created successfully (user not found in local database)',
                'data' => $responseData,
            ], 201);
        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's subscription (single active subscription)
     */
    public function getUserSubscriptions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get Stripe customer for Firebase user
            $customer = $this->getStripeCustomer($request->user_id);

            if (!$customer) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No subscription found'
                ]);
            }

            // Get active subscriptions (user can only have one active subscription)
            $subscriptions = Subscription::all([
                'customer' => $customer->id,
                'status' => 'active',
                'limit' => 1,
            ]);

            // If no active subscription, check for trialing or past_due
            if (empty($subscriptions->data)) {
                $subscriptions = Subscription::all([
                    'customer' => $customer->id,
                    'status' => 'trialing',
                    'limit' => 1,
                ]);
            }

            if (empty($subscriptions->data)) {
                $subscriptions = Subscription::all([
                    'customer' => $customer->id,
                    'status' => 'past_due',
                    'limit' => 1,
                ]);
            }

            // If still no subscription found, return null
            if (empty($subscriptions->data)) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No active subscription found'
                ]);
            }

            // Get the first (and only) subscription
            $subscription = $subscriptions->data[0];

            // Get the price ID from subscription items
            $priceId = null;
            if (!empty($subscription->items->data)) {
                $priceId = $subscription->items->data[0]->price->id;
            }

            // If no price ID found, return basic subscription info
            if (!$priceId) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'start_date' => date('Y-m-d H:i:s', $subscription->current_period_start),
                        'end_date' => date('Y-m-d H:i:s', $subscription->current_period_end),
                        'status' => $subscription->status,
                    ]
                ]);
            }

            // Retrieve price and product details from Stripe
            $price = Price::retrieve($priceId);
            $product = Product::retrieve($price->product);

            // Build subscription plan data
            $planData = [
                'id' => $price->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'description' => $product->description ?? '',
                'amount' => $price->unit_amount / 100, // Convert cents to dollars
                'currency' => $price->currency,
                'interval' => $price->recurring->interval ?? 'month',
                'trial_days' => 7,
                'active' => $price->active,
                'popular' => isset($product->metadata->popular) && $product->metadata->popular === 'true',
                'created_at' => date('Y-m-d H:i:s', $price->created),
                'start_date' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'end_date' => date('Y-m-d H:i:s', $subscription->current_period_end),
            ];

            return response()->json([
                'success' => true,
                'data' => $planData
            ]);
        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'subscription_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get Stripe customer for Firebase user
            $customer = $this->getStripeCustomer($request->user_id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found for this user'
                ], 404);
            }

            // Retrieve the subscription
            $subscription = Subscription::retrieve($request->subscription_id);

            // Verify the subscription belongs to the customer
            if ($subscription->customer !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription does not belong to this user'
                ], 403);
            }

            // Check if subscription is already cancelled or inactive
            if (in_array($subscription->status, ['canceled', 'unpaid', 'incomplete_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription is already cancelled or inactive',
                    'data' => [
                        'subscription_id' => $subscription->id,
                        'status' => $subscription->status,
                    ]
                ], 400);
            }

            // Cancel at period end (standard practice - user keeps access until period ends)
            $subscription->cancel_at_period_end = true;
            $subscription->save();

            // Update local database subscription record
            $dbUserId = null;
            $userEmail = null;

            // Try to find user by email or user_id
            if (is_numeric($request->user_id)) {
                $user = User::find($request->user_id);
                if ($user) {
                    $dbUserId = $user->id;
                    $userEmail = $user->email;
                }
            } else {
                // For Firebase user IDs, try to find by email from Stripe customer
                if ($customer->email) {
                    $user = User::where('email', $customer->email)->first();
                    if ($user) {
                        $dbUserId = $user->id;
                        $userEmail = $user->email;
                    }
                }
            }

            // Update local subscription if user found
            if ($dbUserId && $userEmail) {
                LocalSubscription::where('user_id', $dbUserId)
                    ->where('email', $userEmail)
                    ->update([
                        'isSubscribe' => false,
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription will be cancelled at the end of the current period',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                    'current_period_end' => $subscription->current_period_end
                        ? date('Y-m-d H:i:s', $subscription->current_period_end)
                        : null,
                ]
            ]);
        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resume a cancelled subscription
     */
    public function resumeSubscription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'subscription_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subscription = Subscription::retrieve($request->subscription_id);

            // Resume the subscription
            $subscription->cancel_at_period_end = false;
            $subscription->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscription resumed successfully',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                ]
            ]);
        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resuming subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get or create Stripe customer for Firebase user
     */
    private function getOrCreateStripeCustomer($firebaseUserId)
    {
        try {
            // Search for existing customer by Firebase user ID in metadata
            $customers = Customer::all([
                'limit' => 100,
            ]);

            foreach ($customers->data as $customer) {
                if (
                    isset($customer->metadata->firebase_user_id) &&
                    $customer->metadata->firebase_user_id === $firebaseUserId
                ) {
                    return $customer;
                }
            }

            // Create new Stripe customer for Firebase user
            $customer = Customer::create([
                'metadata' => [
                    'firebase_user_id' => $firebaseUserId,
                ],
            ]);

            return $customer;
        } catch (ApiErrorException $e) {
            throw new \Exception('Error creating Stripe customer: ' . $e->getMessage());
        }
    }

    /**
     * Get Stripe customer for Firebase user
     */
    private function getStripeCustomer($firebaseUserId)
    {
        try {
            // Search for existing customer by Firebase user ID in metadata
            $customers = Customer::all([
                'limit' => 100,
            ]);

            foreach ($customers->data as $customer) {
                if (
                    isset($customer->metadata->firebase_user_id) &&
                    $customer->metadata->firebase_user_id === $firebaseUserId
                ) {
                    return $customer;
                }
            }

            return null;
        } catch (ApiErrorException $e) {
            return null;
        }
    }
}
