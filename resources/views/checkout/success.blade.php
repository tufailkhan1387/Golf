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
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                    <i class="fas fa-check text-4xl text-green-600"></i>
                </div>
                
                <!-- Success Message -->
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Welcome to AutoCoach Golf!
                </h2>
                
                <p class="text-lg text-gray-600 mb-8">
                    Your subscription has been successfully activated. You now have access to our AI-powered golf coaching features.
                </p>

                <!-- Trial Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-gift text-blue-600 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-blue-800">7-Day Free Trial</h3>
                            <p class="text-sm text-blue-600">Your trial period has started. Enjoy full access to all features!</p>
                        </div>
                    </div>
                </div>

                <!-- Subscription Details -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Subscription Details</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Session ID:</span>
                            <span class="font-mono text-xs">{{ $session_id }}</span>
                        </div>
                        @if($subscription_id)
                        <div class="flex justify-between">
                            <span>Subscription ID:</span>
                            <span class="font-mono text-xs">{{ $subscription_id }}</span>
                        </div>
                        @endif
                        @if($user_id)
                        <div class="flex justify-between">
                            <span>User ID:</span>
                            <span class="font-mono text-xs">{{ $user_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                    <a href="/dashboard" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Dashboard
                    </a>
                    
                    <a href="/features" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <i class="fas fa-star mr-2"></i>
                        Explore Features
                    </a>
                </div>

                <!-- Support Information -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Need help? Contact our support team at 
                        <a href="mailto:support@autocoachgolf.com" class="text-green-600 hover:text-green-500">
                            support@autocoachgolf.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Confetti Animation (Optional) -->
    <script>
        // Simple confetti effect
        function createConfetti() {
            const colors = ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B'];
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.borderRadius = '50%';
            confetti.style.pointerEvents = 'none';
            confetti.style.zIndex = '1000';
            confetti.style.animation = 'fall 3s linear forwards';
            
            document.body.appendChild(confetti);
            
            setTimeout(() => {
                confetti.remove();
            }, 3000);
        }

        // Add CSS for confetti animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                }
            }
        `;
        document.head.appendChild(style);

        // Create confetti on page load
        for (let i = 0; i < 50; i++) {
            setTimeout(() => createConfetti(), i * 100);
        }
    </script>
</body>
</html>
