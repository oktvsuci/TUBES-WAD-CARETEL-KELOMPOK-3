@extends('layouts.mahasiswa')

@section('title', 'Track My Reports')

@section('breadcrumb')
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li class="caretel-red">Track Reports</li>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Track My Reports</h1>
            <p class="text-gray-600 mt-1">Monitor the progress of your facility reports in real-time</p>
        </div>
        <a href="{{ route('mahasiswa.laporan.create') }}" class="bg-caretel-red hover:bg-caretel-red-dark text-white px-6 py-3 rounded-lg font-semibold">
            <i class="fas fa-plus mr-2"></i> New Report
        </a>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Active Reports</p>
                <h3 class="text-3xl font-bold mt-2">{{ $laporans->where('status', '!=', 'selesai')->count() }}</h3>
            </div>
            <i class="fas fa-clipboard-list text-4xl opacity-50"></i>
        </div>
    </div>

    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm">Pending Review</p>
                <h3 class="text-3xl font-bold mt-2">{{ $laporans->where('status', 'pending')->count() }}</h3>
            </div>
            <i class="fas fa-clock text-4xl opacity-50"></i>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">In Progress</p>
                <h3 class="text-3xl font-bold mt-2">{{ $laporans->where('status', 'diproses')->count() }}</h3>
            </div>
            <i class="fas fa-tools text-4xl opacity-50"></i>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Completed</p>
                <h3 class="text-3xl font-bold mt-2">{{ $laporans->where('status', 'selesai')->count() }}</h3>
            </div>
            <i class="fas fa-check-circle text-4xl opacity-50"></i>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="border-b">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button class="border-b-2 border-caretel-red caretel-red py-4 px-1 text-sm font-semibold">
                All Reports ({{ $laporans->count() }})
            </button>
            <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-semibold">
                Pending ({{ $laporans->where('status', 'pending')->count() }})
            </button>
            <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-semibold">
                In Progress ({{ $laporans->where('status', 'diproses')->count() }})
            </button>
            <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-semibold">
                Completed ({{ $laporans->where('status', 'selesai')->count() }})
            </button>
        </nav>
    </div>
</div>

<!-- Reports List -->
<div class="space-y-4">
    @forelse($laporans as $laporan)
    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6">
        <div class="flex items-start justify-between">
            <!-- Left Content -->
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="font-mono text-sm font-semibold text-gray-500">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                        @if($laporan->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($laporan->status == 'diproses') bg-orange-100 text-orange-800
                        @elseif($laporan->status == 'selesai') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($laporan->status) }}
                    </span>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $laporan->judul }}</h3>
                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                    <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $laporan->lokasi }}</span>
                    <span><i class="fas fa-calendar mr-1"></i> {{ $laporan->created_at->format('d M Y') }}</span>
                    <span><i class="fas fa-clock mr-1"></i> {{ $laporan->created_at->diffForHumans() }}</span>
                </div>

                <!-- Mini Progress Bar -->
                <div class="max-w-md">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span>Progress</span>
                        <span>
                            @if($laporan->status == 'pending') 25%
                            @elseif($laporan->status == 'diproses') 66%
                            @elseif($laporan->status == 'selesai') 100%
                            @else 0%
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-caretel-red h-2 rounded-full transition-all duration-500" 
                            style="width: @if($laporan->status == 'pending') 25% 
                                        @elseif($laporan->status == 'diproses') 66% 
                                        @elseif($laporan->status == 'selesai') 100%
                                        @else 0% @endif">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Action -->
            <div class="flex flex-col items-end space-y-2">
                <a href="{{ route('mahasiswa.tracking.show', $laporan->id) }}" 
                    class="bg-caretel-red hover:bg-caretel-red-dark text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    <i class="fas fa-eye mr-2"></i> View Details
                </a>
                
                @if($laporan->status == 'pending')
                <form action="{{ route('mahasiswa.laporan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this report?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-times-circle mr-1"></i> Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No Reports Yet</h3>
        <p class="text-gray-500 mb-6">You haven't submitted any facility reports. Start by creating your first report!</p>
        <a href="{{ route('mahasiswa.laporan.create') }}" class="inline-block bg-caretel-red hover:bg-caretel-red-dark text-white px-6 py-3 rounded-lg font-semibold">
            <i class="fas fa-plus mr-2"></i> Create First Report
        </a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($laporans->hasPages())
<div class="mt-6">
    {{ $laporans->links() }}
</div>
@endif

<!-- Help Section -->
<div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
    <div class="flex items-start space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
            <i class="fas fa-question-circle text-blue-600 text-2xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-gray-900 mb-1">Need Help Tracking Your Report?</h3>
            <p class="text-gray-600 text-sm mb-3">Our support team is available 24/7 to assist you with any questions about your facility reports.</p>
            <button class="bg-white hover:bg-gray-50 text-gray-900 px-4 py-2 rounded-lg text-sm font-semibold border">
                <i class="fas fa-headset mr-2"></i> Contact Support
            </button>
        </div>
    </div>
</div>
@endsection