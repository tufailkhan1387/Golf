<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Handle successful checkout
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return view('checkout.error', [
                'message' => 'No session ID provided',
                'title' => 'Checkout Error'
            ]);
        }

        try {
            // Retrieve the checkout session from Stripe
            $session = Session::retrieve($sessionId);
            
            // Get subscription details
            $subscriptionId = $session->subscription;
            $customerId = $session->customer;
            $userId = $session->metadata->user_id ?? null;

            return view('checkout.success', [
                'session_id' => $sessionId,
                'subscription_id' => $subscriptionId,
                'customer_id' => $customerId,
                'user_id' => $userId,
                'title' => 'Subscription Successful'
            ]);

        } catch (ApiErrorException $e) {
            return view('checkout.error', [
                'message' => 'Error retrieving checkout session: ' . $e->getMessage(),
                'title' => 'Checkout Error'
            ]);
        } catch (\Exception $e) {
            return view('checkout.error', [
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                'title' => 'Checkout Error'
            ]);
        }
    }

    /**
     * Handle cancelled checkout
     */
    public function cancel(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        return view('checkout.cancel', [
            'session_id' => $sessionId,
            'title' => 'Checkout Cancelled'
        ]);
    }
}
