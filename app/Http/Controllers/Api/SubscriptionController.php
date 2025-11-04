<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\PaymentMethod;
use Stripe\Exception\ApiErrorException;

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

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                    'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                    'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
                    'trial_start' => $subscription->trial_start ? date('Y-m-d H:i:s', $subscription->trial_start) : null,
                    'trial_end' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
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
     * Get user's subscriptions
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
                    'data' => []
                ]);
            }

            // Get subscriptions
            $subscriptions = Subscription::all([
                'customer' => $customer->id,
                'status' => 'all',
            ]);

            $subscriptionData = [];
            foreach ($subscriptions->data as $subscription) {
                $subscriptionData[] = [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                    'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
                    'trial_start' => $subscription->trial_start ? date('Y-m-d H:i:s', $subscription->trial_start) : null,
                    'trial_end' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                    'created_at' => date('Y-m-d H:i:s', $subscription->created),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $subscriptionData
            ]);
        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subscriptions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'subscription_id' => 'required|string',
            'cancel_at_period_end' => 'boolean',
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

            // Cancel the subscription
            $subscription->cancel_at_period_end = $request->cancel_at_period_end ?? true;
            $subscription->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
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
