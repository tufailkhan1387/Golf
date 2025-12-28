# AutoCoach Golf - Subscription Plans API Documentation

## Overview
This API allows you to manage Stripe subscription plans for AutoCoach Golf. The API works directly with Stripe as the primary source of truth - no local database storage. All plans are created, updated, and retrieved directly from Stripe.

## Base URL
```
http://your-domain.com/api
```

## Authentication
Currently, the API endpoints are public. In production, you should add authentication middleware.

## Important Notes
- **No Database Storage**: Plans are stored and managed entirely in Stripe
- **Stripe as Source of Truth**: All operations are performed directly against Stripe API
- **Real-time Data**: All responses reflect the current state in Stripe

## Endpoints

### 1. Get All Subscription Plans
**GET** `/api/subscription-plans`

Returns all active subscription plans from Stripe.

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "price_1234567890",
            "product_id": "prod_1234567890",
            "name": "AutoCoach Golf Monthly",
            "description": "Monthly subscription to AutoCoach Golf",
            "amount": 29.99,
            "currency": "usd",
            "interval": "month",
            "trial_days": 7,
            "active": true,
            "created_at": "2024-01-01 00:00:00"
        }
    ]
}
```

### 2. Create Subscription Plan
**POST** `/api/subscription-plans`

Creates a new subscription plan in Stripe.

**Request Body:**
```json
{
    "name": "AutoCoach Golf Monthly",
    "amount": 29.99,
    "currency": "usd",
    "interval": "month",
    "trial_days": 7,
    "description": "Monthly subscription to AutoCoach Golf",
    "features": [
        "AI-powered swing analysis",
        "Personalized training plans",
        "Progress tracking"
    ]
}
```

**Required Fields:**
- `name` (string, max 255 characters)
- `amount` (numeric, minimum 0)
- `currency` (string, 3 characters)
- `interval` (enum: "month" or "year")

**Optional Fields:**
- `trial_days` (integer, 0-365, default: 7)
- `description` (string)
- `features` (array)

**Response:**
```json
{
    "success": true,
    "message": "Subscription plan created successfully",
    "data": {
        "id": "price_1234567890",
        "product_id": "prod_1234567890",
        "name": "AutoCoach Golf Monthly",
        "description": "Monthly subscription to AutoCoach Golf",
        "amount": 29.99,
        "currency": "usd",
        "interval": "month",
        "trial_days": 7,
        "features": [
            "AI-powered swing analysis",
            "Personalized training plans"
        ],
        "active": true,
        "created_at": "2024-01-01 00:00:00"
    }
}
```

### 3. Get Single Subscription Plan
**GET** `/api/subscription-plans/{id}`

Returns a specific subscription plan by Stripe Price ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "id": "price_1234567890",
        "product_id": "prod_1234567890",
        "name": "AutoCoach Golf Monthly",
        "description": "Monthly subscription to AutoCoach Golf",
        "amount": 29.99,
        "currency": "usd",
        "interval": "month",
        "trial_days": 7,
        "active": true,
        "created_at": "2024-01-01 00:00:00"
    }
}
```

### 4. Update Subscription Plan
**PUT** `/api/subscription-plans/{id}`

Updates an existing subscription plan in Stripe.

**Request Body:**
```json
{
    "name": "Updated Plan Name",
    "description": "Updated description",
    "active": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Subscription plan updated successfully",
    "data": {
        "id": "price_1234567890",
        "product_id": "prod_1234567890",
        "name": "Updated Plan Name",
        "description": "Updated description",
        "amount": 29.99,
        "currency": "usd",
        "interval": "month",
        "trial_days": 7,
        "active": true,
        "created_at": "2024-01-01 00:00:00"
    }
}
```

### 5. Deactivate Subscription Plan
**DELETE** `/api/subscription-plans/{id}`

Deactivates a subscription plan in Stripe (sets active to false).

**Response:**
```json
{
    "success": true,
    "message": "Subscription plan deactivated successfully"
}
```

### 6. Get Plans by Interval
**GET** `/api/subscription-plans/interval/{interval}`

Returns subscription plans filtered by interval (month or year).

**Parameters:**
- `interval` (string): "month" or "year"

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "price_1234567890",
            "product_id": "prod_1234567890",
            "name": "AutoCoach Golf Monthly",
            "description": "Monthly subscription to AutoCoach Golf",
            "amount": 29.99,
            "currency": "usd",
            "interval": "month",
            "trial_days": 7,
            "active": true,
            "created_at": "2024-01-01 00:00:00"
        }
    ]
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "amount": ["The amount must be at least 0."]
    }
}
```

### Not Found Error (404)
```json
{
    "success": false,
    "message": "Subscription plan not found"
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
    "message": "Error creating subscription plan: Database connection failed"
}
```

## Example Usage

### Create Monthly Plan
```bash
curl -X POST http://your-domain.com/api/subscription-plans \
  -H "Content-Type: application/json" \
  -d '{
    "name": "AutoCoach Golf Monthly",
    "amount": 29.99,
    "currency": "usd",
    "interval": "month",
    "trial_days": 7,
    "description": "Monthly subscription to AutoCoach Golf",
    "features": [
      "AI-powered swing analysis",
      "Personalized training plans",
      "Progress tracking"
    ]
  }'
```

### Create Yearly Plan
```bash
curl -X POST http://your-domain.com/api/subscription-plans \
  -H "Content-Type: application/json" \
  -d '{
    "name": "AutoCoach Golf Yearly",
    "amount": 299.99,
    "currency": "usd",
    "interval": "year",
    "trial_days": 7,
    "description": "Yearly subscription to AutoCoach Golf (Save 17%)",
    "features": [
      "AI-powered swing analysis",
      "Personalized training plans",
      "Progress tracking",
      "Priority support",
      "Advanced analytics"
    ]
  }'
```

### Get All Plans
```bash
curl -X GET http://your-domain.com/api/subscription-plans
```

### Get Monthly Plans Only
```bash
curl -X GET http://your-domain.com/api/subscription-plans/interval/month
```

### Get Specific Plan
```bash
curl -X GET http://your-domain.com/api/subscription-plans/price_1234567890
```

### Update Plan
```bash
curl -X PUT http://your-domain.com/api/subscription-plans/price_1234567890 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Plan Name",
    "description": "Updated description",
    "active": true
  }'
```

### Deactivate Plan
```bash
curl -X DELETE http://your-domain.com/api/subscription-plans/price_1234567890
```

## Environment Variables Required

Make sure to set these environment variables in your `.env` file:

```env
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

## Notes

1. **Stripe Integration**: All operations are performed directly against Stripe API
2. **No Local Storage**: Plans are not stored in your database
3. **Real-time Data**: All responses reflect the current state in Stripe
4. **Trial Period**: All plans include a 7-day free trial by default
5. **Currency**: Currently supports USD, but can be extended to other currencies
6. **Features**: The features field is included in responses but not stored in Stripe
7. **Deactivation**: Plans are deactivated (not deleted) to maintain data integrity

## Next Steps

1. Add authentication middleware to protect the API endpoints
2. Implement webhook handling for Stripe events
3. Add subscription management endpoints for users
4. Implement Stripe Tax for automatic VAT handling
5. Add comprehensive logging and monitoring