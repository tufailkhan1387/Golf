<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - AutoCoach Golf</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <!-- Cancel Icon -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-6">
                    <i class="fas fa-times text-4xl text-yellow-600"></i>
                </div>
                
                <!-- Cancel Message -->
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Checkout Cancelled
                </h2>
                
                <p class="text-lg text-gray-600 mb-8">
                    Your subscription process was cancelled. No charges have been made to your account.
                </p>

                <!-- Information Box -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">What happens next?</h3>
                            <p class="text-sm text-yellow-600">You can try subscribing again anytime. Your account remains unchanged.</p>
                        </div>
                    </div>
                </div>

                <!-- Session Details -->
                @if($session_id)
                <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Session Details</h3>
                    <div class="text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Session ID:</span>
                            <span class="font-mono text-xs">{{ $session_id }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-4">
                    <a href="/pricing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <i class="fas fa-credit-card mr-2"></i>
                        Try Again
                    </a>
                    
                    <a href="/" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <i class="fas fa-home mr-2"></i>
                        Back to Home
                    </a>
                </div>

                <!-- Help Section -->
                <div class="mt-8 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Need Help?</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        If you encountered any issues during checkout, we're here to help.
                    </p>
                    <div class="space-y-2">
                        <a href="mailto:support@autocoachgolf.com" class="text-sm text-green-600 hover:text-green-500 flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            support@autocoachgolf.com
                        </a>
                        <a href="/contact" class="text-sm text-green-600 hover:text-green-500 flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            Contact Support
                        </a>
                    </div>
                </div>

                <!-- Why Subscribe Section -->
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-green-800 mb-2">Why Subscribe to AutoCoach Golf?</h3>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            AI-powered swing analysis
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            Personalized training plans
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            7-day free trial
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-2"></i>
                            Progress tracking
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
