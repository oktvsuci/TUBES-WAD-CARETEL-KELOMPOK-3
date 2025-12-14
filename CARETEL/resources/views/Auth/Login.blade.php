<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - CARETEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .caretel-red { color: #E30613; }
        .bg-caretel-red { background-color: #E30613; }
        .hover\:bg-caretel-red-dark:hover { background-color: #C00510; }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <div class="flex h-full">
        <!-- Left Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="flex items-center mb-12">
                    <div class="bg-caretel-red w-12 h-12 rounded flex items-center justify-center">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-gray-900">CARETEL</h1>
                        <p class="text-sm text-gray-500">Telkom University</p>
                    </div>
                </div>

                <!-- Welcome Text -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
                    <p class="text-gray-600">Sign in to report and track campus facility issues.</p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username or Email -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Username or Email</label>
                        <input type="text" name="email" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('email') border-red-500 @enderror" 
                            placeholder="e.g. name@telkomuniversity.ac.id"
                            value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('password') border-red-500 @enderror" 
                                placeholder="Enter your password" required>
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-caretel-red border-gray-300 rounded focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-600">Remember for 30 days</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm caretel-red hover:underline font-semibold">Forgot password?</a>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="w-full bg-caretel-red hover:bg-caretel-red-dark text-white py-3 rounded-lg font-semibold transition duration-200">
                        Sign in
                    </button>

                    <!-- Register Link -->
                    <p class="text-center text-gray-600 text-sm mt-6">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="caretel-red font-semibold hover:underline">Register here</a>
                    </p>
                </form>

                <!-- Footer -->
                <p class="text-center text-gray-400 text-xs mt-12">
                    © 2024 Telkom University. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Right Side - Image & Info -->
        <div class="hidden lg:block lg:w-1/2 relative">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-800"></div>
            <img src="https://images.unsplash.com/photo-1562774053-701939374585?w=800" 
                alt="Campus" 
                class="w-full h-full object-cover opacity-50">
            
            <!-- Overlay Content -->
            <div class="absolute inset-0 flex flex-col justify-end p-12 text-white">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4 inline-flex items-center mb-6 w-fit">
                    <i class="fas fa-building mr-2"></i>
                    <span class="font-semibold">Campus Facilities</span>
                </div>
                <h2 class="text-4xl font-bold mb-4">Efficient Reporting, Better Campus.</h2>
                <p class="text-lg opacity-90">Join the community in maintaining a world-class learning environment at Telkom University.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>