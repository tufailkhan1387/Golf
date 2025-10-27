<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Exception\ApiErrorException;

class SubscriptionPlanController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            // Get all products from Stripe
            $products = Product::all(['active' => true, 'type' => 'service']);
            $plans = [];

            foreach ($products->data as $product) {
                // Get prices for each product
                $prices = Price::all(['product' => $product->id, 'active' => true]);
                
                foreach ($prices->data as $price) {
                    if (isset($price->recurring)) {
                        $plans[] = [
                            'id' => $price->id,
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'amount' => $price->unit_amount / 100, // Convert from cents
                            'currency' => $price->currency,
                            'interval' => $price->recurring->interval,
                            'trial_days' => 7, // Default trial period
                            'active' => $price->active,
                            'created_at' => date('Y-m-d H:i:s', $price->created),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $plans
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subscription plans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'interval' => 'required|in:month,year',
            'trial_days' => 'integer|min:0|max:365',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create Stripe Product
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => 'service',
                'active' => true,
            ]);

            // Create Stripe Price
            $price = Price::create([
                'product' => $product->id,
                'unit_amount' => $request->amount * 100, // Convert to cents
                'currency' => $request->currency,
                'recurring' => [
                    'interval' => $request->interval,
                ],
                'active' => true,
            ]);

            $plan = [
                'id' => $price->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'amount' => $price->unit_amount / 100,
                'currency' => $price->currency,
                'interval' => $price->recurring->interval,
                'trial_days' => $request->trial_days ?? 7,
                'features' => $request->features,
                'active' => $price->active,
                'created_at' => date('Y-m-d H:i:s', $price->created),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan created successfully',
                'data' => $plan
            ], 201);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating subscription plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $price = Price::retrieve($id);
            $product = Product::retrieve($price->product);

            $plan = [
                'id' => $price->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'amount' => $price->unit_amount / 100,
                'currency' => $price->currency,
                'interval' => $price->recurring->interval,
                'trial_days' => 7,
                'active' => $price->active,
                'created_at' => date('Y-m-d H:i:s', $price->created),
            ];

            return response()->json([
                'success' => true,
                'data' => $plan
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription plan not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subscription plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $price = Price::retrieve($id);
            $product = Product::retrieve($price->product);

            // Update product if name or description changed
            if ($request->has('name') || $request->has('description')) {
                $product->name = $request->name ?? $product->name;
                $product->description = $request->description ?? $product->description;
                $product->save();
            }

            // Update price if active status changed
            if ($request->has('active')) {
                $price->active = $request->active;
                $price->save();
            }

            $plan = [
                'id' => $price->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'amount' => $price->unit_amount / 100,
                'currency' => $price->currency,
                'interval' => $price->recurring->interval,
                'trial_days' => 7,
                'active' => $price->active,
                'created_at' => date('Y-m-d H:i:s', $price->created),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan updated successfully',
                'data' => $plan
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription plan not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating subscription plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $price = Price::retrieve($id);
            
            // Deactivate the price instead of deleting
            $price->active = false;
            $price->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan deactivated successfully'
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription plan not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deactivating subscription plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plans by interval (month/year)
     */
    public function getByInterval(string $interval): JsonResponse
    {
        try {
            // Get all products from Stripe
            $products = Product::all(['active' => true, 'type' => 'service']);
            $plans = [];

            foreach ($products->data as $product) {
                // Get prices for each product
                $prices = Price::all(['product' => $product->id, 'active' => true]);
                
                foreach ($prices->data as $price) {
                    if (isset($price->recurring) && $price->recurring->interval === $interval) {
                        $plans[] = [
                            'id' => $price->id,
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'amount' => $price->unit_amount / 100,
                            'currency' => $price->currency,
                            'interval' => $price->recurring->interval,
                            'trial_days' => 7,
                            'active' => $price->active,
                            'created_at' => date('Y-m-d H:i:s', $price->created),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $plans
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subscription plans: ' . $e->getMessage()
            ], 500);
        }
    }
}
