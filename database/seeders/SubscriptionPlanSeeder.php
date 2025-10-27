<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'AutoCoach Golf Monthly',
                'stripe_price_id' => 'price_placeholder_monthly',
                'stripe_product_id' => 'prod_placeholder_monthly',
                'amount' => 29.99,
                'currency' => 'usd',
                'interval' => 'month',
                'trial_days' => 7,
                'description' => 'Monthly subscription to AutoCoach Golf with AI-powered coaching',
                'features' => [
                    'AI-powered swing analysis',
                    'Personalized training plans',
                    'Progress tracking',
                    'Video analysis',
                    'Mobile app access'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'AutoCoach Golf Yearly',
                'stripe_price_id' => 'price_placeholder_yearly',
                'stripe_product_id' => 'prod_placeholder_yearly',
                'amount' => 299.99,
                'currency' => 'usd',
                'interval' => 'year',
                'trial_days' => 7,
                'description' => 'Yearly subscription to AutoCoach Golf with AI-powered coaching (Save 17%)',
                'features' => [
                    'AI-powered swing analysis',
                    'Personalized training plans',
                    'Progress tracking',
                    'Video analysis',
                    'Mobile app access',
                    'Priority support',
                    'Advanced analytics'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
