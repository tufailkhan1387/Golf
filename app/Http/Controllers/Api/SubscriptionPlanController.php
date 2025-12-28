<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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
    public function index()
    {
        try {
            $products = Product::all(['active' => true, 'type' => 'service']);
            // return $products;
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
                            'amount' => $price->unit_amount / 100,
                            'currency' => $price->currency,
                            'interval' => $price->recurring->interval,
                            'trial_days' => 7,
                            'active' => $price->active,
                            'popular' => isset($product->metadata->popular) &&
                                $product->metadata->popular === 'true', // From product metadata
                            'created_at' => date('Y-m-d H:i:s', $price->created),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => "1",
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
        try {
            $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'amount' => 'required|numeric',
                'currency' => 'required|string',
                'interval' => 'required|in:day,week,month,year',
                'trial_days' => 'required|integer',
                'features' => 'sometimes|array',
                'ispopular' => 'sometimes|boolean'
            ]);

            // Retrieve the existing price
            $price = Price::retrieve($id);

            // Retrieve the product associated with this price
            $product = Product::retrieve($price->product);

            // Update the product (name, description, metadata)
            $productUpdateData = [
                'name' => $request->name,
                'description' => $request->description,
            ];

            // Add popular flag to product metadata
            if ($request->has('ispopular')) {
                $productUpdateData['metadata'] = [
                    'popular' => $request->ispopular ? 'true' : 'false'
                ];
            }

            // Update features in metadata if provided
            if ($request->has('features')) {
                $productUpdateData['metadata']['features'] = json_encode($request->features);
            }

            Product::update($product->id, $productUpdateData);

            // Since Stripe prices are immutable, we need to create a new price
            // and deactivate the old one if any pricing details changed
            $priceChanged = (
                $price->unit_amount / 100 != $request->amount ||
                $price->currency != $request->currency ||
                $price->recurring->interval != $request->interval
            );

            $newPrice = null;

            if ($priceChanged) {
                // Create a new price with updated details first
                $newPrice = Price::create([
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
                Product::update($product->id, [
                    'default_price' => $newPrice->id
                ]);

                // Now we can safely deactivate the old price
                // (it's no longer the default, so Stripe will allow it)
                try {
                    Price::update($price->id, ['active' => false]);
                } catch (\Exception $e) {
                    // If deactivation fails, log but don't fail the update
                    // The new price is already set as default, which is what matters
                    Log::warning('Could not deactivate old price: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan updated successfully',
                'data' => [
                    'product_id' => $product->id,
                    'price_id' => $newPrice ? $newPrice->id : $price->id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'interval' => $request->interval,
                    'trial_days' => $request->trial_days,
                    'features' => $request->features,
                    'popular' => $request->ispopular ?? false,
                    'price_updated' => $priceChanged
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
