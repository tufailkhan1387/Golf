# AutoCoach Golf - Subscription Management API Documentation

## Overview
This API allows users to subscribe to AutoCoach Golf plans, manage their subscriptions, and handle the complete subscription lifecycle with Stripe integration.

## Base URL
```
http://your-domain.com/api
```

## Authentication
Currently, the API endpoints are public. In production, you should add authentication middleware.

## Important Notes
- **Firebase Integration**: Uses Firebase user IDs instead of database user IDs
- **Stripe Checkout Integration**: Uses Stripe Checkout for secure payment processing
- **7-Day Free Trial**: All subscriptions include a 7-day free trial
- **Automatic Customer Creation**: Stripe customers are created automatically for Firebase users
- **Subscription Management**: Full lifecycle management (subscribe, cancel, resume)

## Endpoints

### 1. Subscribe to a Plan
**POST** `/api/subscriptions/subscribe`

Creates a Stripe Checkout session for a user to subscribe to a plan.

**Request Body:**
```json
{
    "user_id": "firebase_user_123456789",
    "price_id": "price_1234567890"
}
```

**Required Fields:**
- `user_id` (string): Firebase user ID of the user subscribing
- `price_id` (string): Stripe Price ID of the plan

**Response:**
```json
{
    "success": true,
    "message": "Checkout session created successfully",
    "data": {
        "checkout_url": "https://checkout.stripe.com/c/pay/cs_1234567890",
        "session_id": "cs_1234567890",
        "user_id": 1
    }
}
```

### 2. Get User's Subscriptions
**GET** `/api/subscriptions/user-subscriptions`

Retrieves all subscriptions for a specific user.

**Request Body:**
```json
{
    "user_id": "firebase_user_123456789"
}
```

**Required Fields:**
- `user_id` (string): Firebase user ID of the user

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "sub_1234567890",
            "status": "active",
            "current_period_start": "2024-01-01 00:00:00",
            "current_period_end": "2024-02-01 00:00:00",
            "trial_start": "2024-01-01 00:00:00",
            "trial_end": "2024-01-08 00:00:00",
            "cancel_at_period_end": false,
            "created_at": "2024-01-01 00:00:00"
        }
    ]
}
```

### 3. Cancel Subscription
**POST** `/api/subscriptions/cancel`

Cancels a user's subscription (at the end of the current period by default).

**Request Body:**
```json
{
    "user_id": "firebase_user_123456789",
    "subscription_id": "sub_1234567890",
    "cancel_at_period_end": true
}
```

**Required Fields:**
- `user_id` (string): Firebase user ID of the user
- `subscription_id` (string): Stripe Subscription ID

**Optional Fields:**
- `cancel_at_period_end` (boolean): Whether to cancel at period end (default: true)

**Response:**
```json
{
    "success": true,
    "message": "Subscription cancelled successfully",
    "data": {
        "subscription_id": "sub_1234567890",
        "status": "active",
        "cancel_at_period_end": true
    }
}
```

### 4. Resume Subscription
**POST** `/api/subscriptions/resume`

Resumes a cancelled subscription.

**Request Body:**
```json
{
    "user_id": "firebase_user_123456789",
    "subscription_id": "sub_1234567890"
}
```

**Required Fields:**
- `user_id` (string): Firebase user ID of the user
- `subscription_id` (string): Stripe Subscription ID

**Response:**
```json
{
    "success": true,
    "message": "Subscription resumed successfully",
    "data": {
        "subscription_id": "sub_1234567890",
        "status": "active",
        "cancel_at_period_end": false
    }
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "user_id": ["The user id field is required."],
        "price_id": ["The price id field is required."]
    }
}
```

### User Not Found (404)
```json
{
    "success": false,
    "message": "User not found"
}
```

### Stripe API Error (500)
```json
{
    "success": false,
    "message": "Stripe API error: Invalid API key provided"
}
```

### General Error (500)
```json
{
    "success": false,
    "message": "Error creating subscription: Database connection failed"
}
```

## Example Usage

### Subscribe to Monthly Plan
```bash
curl -X POST http://your-domain.com/api/subscriptions/subscribe \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "firebase_user_123456789",
    "price_id": "price_1234567890"
  }'
```

### Subscribe to Yearly Plan
```bash
curl -X POST http://your-domain.com/api/subscriptions/subscribe \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "firebase_user_123456789",
    "price_id": "price_0987654321"
  }'
```

### Get User's Subscriptions
```bash
curl -X GET http://your-domain.com/api/subscriptions/user-subscriptions \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "firebase_user_123456789"
  }'
```

### Cancel Subscription
```bash
curl -X POST http://your-domain.com/api/subscriptions/cancel \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "firebase_user_123456789",
    "subscription_id": "sub_1234567890",
    "cancel_at_period_end": true
  }'
```

### Resume Subscription
```bash
curl -X POST http://your-domain.com/api/subscriptions/resume \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "firebase_user_123456789",
    "subscription_id": "sub_1234567890"
  }'
```

## Complete Subscription Flow

### 1. Get Available Plans
First, get the available subscription plans:
```bash
curl -X GET http://your-domain.com/api/subscription-plans
```

### 2. Subscribe to a Plan
Use the price_id from the plans to subscribe:
```bash
curl -X POST http://your-domain.com/api/subscriptions/subscribe \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "price_id": "price_1234567890",
    "success_url": "https://your-domain.com/success",
    "cancel_url": "https://your-domain.com/cancel"
  }'
```

### 3. Redirect User to Checkout
Use the `checkout_url` from the response to redirect the user to Stripe Checkout.

### 4. Handle Success/Cancel
- **Success**: User completes payment, redirect to `success_url`
- **Cancel**: User cancels, redirect to `cancel_url`

### 5. Check Subscription Status
After successful payment, check the user's subscriptions:
```bash
curl -X GET http://your-domain.com/api/subscriptions/user-subscriptions \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1
  }'
```

## Subscription Statuses

- `trialing`: User is in the 7-day free trial
- `active`: Subscription is active and paid
- `past_due`: Payment failed, retrying
- `canceled`: Subscription was cancelled
- `unpaid`: Payment failed and won't retry

## Environment Variables Required

Make sure to set these environment variables in your `.env` file:

```env
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

## Notes

1. **Automatic Customer Creation**: Stripe customers are created automatically for users
2. **7-Day Free Trial**: All subscriptions include a 7-day free trial
3. **Stripe Checkout**: Uses Stripe Checkout for secure payment processing
4. **Subscription Management**: Full lifecycle management (subscribe, cancel, resume)
5. **User ID Required**: All subscription operations require a user_id
6. **Stripe Integration**: All operations are performed directly against Stripe API

## Next Steps

1. Add authentication middleware to protect the API endpoints
2. Implement webhook handling for Stripe events
3. Add subscription status checking endpoints
4. Implement Stripe Tax for automatic VAT handling
5. Add comprehensive logging and monitoring
