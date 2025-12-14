<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - CARETEL Admin</title>
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
                        <p class="text-xs text-gray-500">Admin Panel</p>
                    </div>
                </div>

                <!-- Right Menu -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard.index') }}" class="text-gray-700 hover:text-gray-900">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.laporan.index') }}" class="text-gray-700 hover:text-gray-900">
                        <i class="fas fa-clipboard-list mr-1"></i> All Reports
                    </a>
                    <a href="{{ route('admin.teknisi.index') }}" class="text-gray-700 hover:text-gray-900">
                        <i class="fas fa-users-cog mr-1"></i> Technicians
                    </a>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="text-gray-700 hover:text-gray-900">
                        <i class="fas fa-user-graduate mr-1"></i> Students
                    </a>
                    
                    <!-- Notifications -->
                    <button class="relative text-gray-700 hover:text-gray-900">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-caretel-red text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">3</span>
                    </button>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                            <div class="w-8 h-8 bg-caretel-red rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-caretel-red">
                            <i class="fas fa-sign-out-alt"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py