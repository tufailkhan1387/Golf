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
                <!-- Error Icon -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                </div>
                
                <!-- Error Message -->
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    {{ $title }}
                </h2>
                
                <p class="text-lg text-gray-600 mb-8">
                    {{ $message }}
                </p>

                <!-- Error Details -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-bug text-red-600 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Technical Issue</h3>
                            <p class="text-sm text-red-600">We're sorry for the inconvenience. Our team has been notified.</p>
                        </div>
                    </div>
                </div>

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

                <!-- Support Information -->
                <div class="mt-8 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Need Immediate Help?</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        Our support team is available to help you resolve this issue.
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

                <!-- Alternative Options -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">Alternative Options</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li class="flex items-center">
                            <i class="fas fa-arrow-right text-blue-600 mr-2"></i>
                            Try a different payment method
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-arrow-right text-blue-600 mr-2"></i>
                            Contact support for manual processing
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-arrow-right text-blue-600 mr-2"></i>
                            Check your internet connection
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
