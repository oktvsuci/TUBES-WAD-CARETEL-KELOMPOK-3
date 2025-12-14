@extends('layouts.mahasiswa')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li class="caretel-red">Dashboard</li>
@endsection

@section('content')
<!-- Welcome Section -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }} 👋</h1>
            <p class="text-gray-600 mt-1">Monitor and manage your facility reports across the campus.</p>
            <p class="text-sm text-gray-500 mt-1">Last login: Today, {{ now()->format('H:i A') }}</p>
        </div>
        <a href="{{ route('mahasiswa.laporan.create') }}" class="bg-caretel-red hover:bg-caretel-red-dark text-white px-6 py-3 rounded-lg font-semibold">
            <i class="fas fa-plus mr-2"></i> New Report
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Reports -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Reports</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $totalLaporan }}</h3>
                <p class="text-blue-600 text-sm mt-1">
                    <i class="fas fa-arrow-up"></i> All time
                </p>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Pending</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $laporanPending }}</h3>
                <p class="text-yellow-600 text-sm mt-1">
                    <i class="fas fa-clock"></i> Waiting review
                </p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- In Progress -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">In Progress</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $laporanDiproses }}</h3>
                <p class="text-orange-600 text-sm mt-1">
                    <i class="fas fa-tools"></i> Being fixed
                </p>
            </div>
            <div class="bg-orange-100 p-4 rounded-full">
                <i class="fas fa-spinner text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Resolved -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Resolved</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $laporanSelesai }}</h3>
                <p class="text-green-600 text-sm mt-1">
                    <i class="fas fa-check-circle"></i> Completed
                </p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-check-double text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Facility Issues & Facility Map -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Recent Issues -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Recent Facility Issues</h2>
            <a href="{{ route('mahasiswa.laporan.index') }}" class="text-sm caretel-red hover:underline">View all →</a>
        </div>
        
        <div class="space-y-3">
            @forelse($laporanTerbaru as $laporan)
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg border">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                        <i class="fas fa-tools text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $laporan->judul }}</h3>
                        <p class="text-sm text-gray-500">{{ $laporan->lokasi }}</p>
                        <p class="text-xs text-gray-400">{{ $laporan->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    @if($laporan->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($laporan->status == 'diproses') bg-orange-100 text-orange-800
                    @elseif($laporan->status == 'selesai') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($laporan->status) }}
                </span>
            </div>
            @empty
            <p class="text-center text-gray-500 py-8">Belum ada laporan</p>
            @endforelse
        </div>
    </div>

    <!-- Facility Map -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Facility Map</h2>
        <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center mb-4">
            <div class="text-center">
                <i class="fas fa-map-marked-alt text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-500 text-sm">Interactive Campus Map</p>
            </div>
        </div>
        
        <div class="space-y-2">
            <h3 class="font-semibold text-gray-900 text-sm">Need immediate help?</h3>
            <p class="text-gray-600 text-sm">Contact our facility management team for urgent issues.</p>
            <button class="w-full bg-caretel-red hover:bg-caretel-red-dark text-white py-2 rounded-lg text-sm font-semibold mt-2">
                <i class="fas fa-phone mr-2"></i> Call 1500-835
            </button>
        </div>
    </div>
</div>
@endsection