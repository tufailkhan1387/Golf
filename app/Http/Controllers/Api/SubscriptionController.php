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

            // Create Stripe Checkout Session
            $session = Session::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $request->price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'subscription_data' => [
                    'trial_period_days' => 7, // 7-day free trial
                ],
                'success_url' => request()->getSchemeAndHttpHost() . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => request()->getSchemeAndHttpHost() . '/checkout/cancel?session_id={CHECKOUT_SESSION_ID}',
                'metadata' => [
                    'user_id' => $request->user_id,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout session created successfully',
                'data' => [
                    'checkout_url' => $session->url,
                    'session_id' => $session->id,
                    'user_id' => $request->user_id,
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
    public function getUserSubscriptions(Request $request): JsonResponse
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
