<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - AutoCoach Golf</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Choose Your AutoCoach Golf Plan
                </h1>
                <p class="text-xl text-gray-600">
                    Start your 7-day free trial today. No credit card required.
                </p>
            </div>

            <!-- Pricing Cards -->
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Monthly Plan -->
                <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-200">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Monthly Plan</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-gray-900">$29.99</span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        <p class="text-gray-600 mb-6">Perfect for getting started</p>
                        
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                AI-powered swing analysis
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Personalized training plans
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Progress tracking
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Video analysis
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Mobile app access
                            </li>
                        </ul>

                        <button onclick="subscribeToPlan('monthly')" class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 transition duration-150 ease-in-out">
                            Start Free Trial
                        </button>
                    </div>
                </div>

                <!-- Yearly Plan -->
                <div class="bg-white rounded-lg shadow-lg p-8 border-2 border-green-500 relative">
                    <!-- Popular Badge -->
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-green-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                            Most Popular
                        </span>
                    </div>
                    
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Yearly Plan</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-gray-900">$299.99</span>
                            <span class="text-gray-600">/year</span>
                        </div>
                        <p class="text-gray-600 mb-2">Save 17% with yearly billing</p>
                        <p class="text-sm text-green-600 font-medium mb-6">Only $24.99/month</p>
                        
                        <ul class="text-left space-y-3 mb-8">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Everything in Monthly
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Priority support
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Advanced analytics
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Exclusive content
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                Early access to new features
                            </li>
                        </ul>

                        <button onclick="subscribeToPlan('yearly')" class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 transition duration-150 ease-in-out">
                            Start Free Trial
                        </button>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-16 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">
                    What's Included in Your Free Trial
                </h2>
                <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-robot text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">AI-Powered Analysis</h3>
                        <p class="text-gray-600">Get instant feedback on your swing with our advanced AI technology.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Progress Tracking</h3>
                        <p class="text-gray-600">Monitor your improvement with detailed analytics and insights.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-mobile-alt text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Mobile Access</h3>
                        <p class="text-gray-600">Take your coaching anywhere with our mobile app.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // This would typically be replaced with actual API calls
        function subscribeToPlan(planType) {
            // For demo purposes, we'll show an alert
            // In a real application, you would call your API here
            alert(`This would subscribe to the ${planType} plan. In a real application, this would call your API to create a checkout session.`);
            
            // Example API call (uncomment when ready):
            /*
            fetch('/api/subscriptions/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: 1, // This would come from your authentication
                    price_id: planType === 'monthly' ? 'price_monthly_id' : 'price_yearly_id',
                    success_url: window.location.origin + '/checkout/success',
                    cancel_url: window.location.origin + '/checkout/cancel'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.data.checkout_url;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
            */
        }
    </script>
</body>
</html>
