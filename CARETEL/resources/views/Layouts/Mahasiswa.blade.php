<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - CARETEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .caretel-red { color: #E30613; }
        .bg-caretel-red { background-color: #E30613; }
        .border-caretel-red { border-color: #E30613; }
        .hover\:bg-caretel-red-dark:hover { background-color: #C00510; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="bg-caretel-red w-10 h-10 rounded flex items-center justify-center">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-xl font-bold text-gray-900">CARETEL</h1>
                        <p class="text-xs text-gray-500">Telkom University</p>
                    </div>
                </div>

                <!-- Right Menu -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('mahasiswa.dashboard.index') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                    <a href="{{ route('mahasiswa.laporan.index') }}" class="text-gray-700 hover:text-gray-900">My Reports</a>
                    <a href="{{ route('mahasiswa.tracking.index') }}" class="text-gray-700 hover:text-gray-900">Help Center</a>
                    
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-caretel-red">
                            Log Out <i class="fas fa-sign-out-alt ml-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="text-sm mb-6">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('mahasiswa.dashboard.index') }}" class="hover:text-caretel-red">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>

            <!-- Alert Messages -->
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-caretel-red text-red-700 p-4 mb-6 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-500 text-sm">© 2024 Telkom University. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>