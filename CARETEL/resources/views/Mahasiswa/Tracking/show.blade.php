@extends('layouts.mahasiswa')

@section('title', 'Report Details')

@section('breadcrumb')
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li><a href="{{ route('mahasiswa.laporan.index') }}" class="hover:text-caretel-red">Reports</a></li>
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li class="caretel-red">Detail</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Report Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="text-sm text-gray-500 font-mono">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $laporan->judul }}</h1>
                    <p class="text-gray-600 mt-1">{{ $laporan->lokasi }}</p>
                </div>
                <span class="px-4 py-2 text-sm font-semibold rounded-full
                    @if($laporan->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($laporan->status == 'diproses') bg-orange-100 text-orange-800
                    @elseif($laporan->status == 'selesai') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($laporan->status) }}
                </span>
            </div>

            <!-- Progress Timeline -->
            <div class="relative mt-8">
                <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200">
                    <div class="h-full bg-caretel-red transition-all duration-500" 
                        style="width: @if($laporan->status == 'pending') 25% 
                                    @elseif($laporan->status == 'diproses') 66% 
                                    @else 100% @endif">
                    </div>
                </div>
                
                <div class="relative flex justify-between">
                    <!-- Submitted -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-caretel-red flex items-center justify-center mb-2 z-10">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-900">Reported</span>
                        <span class="text-xs text-gray-500">Submitted</span>
                    </div>

                    <!-- Reviewing -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full 
                            @if($laporan->status == 'pending') bg-gray-200 text-gray-400
                            @else bg-caretel-red text-white @endif 
                            flex items-center justify-center mb-2 z-10">
                            @if($laporan->status == 'pending')
                                <i class="fas fa-circle text-xs"></i>
                            @else
                                <i class="fas fa-check"></i>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-gray-900">Reviewing</span>
                        <span class="text-xs text-gray-500">Verification</span>
                    </div>

                    <!-- In Progress -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full 
                            @if(in_array($laporan->status, ['pending', 'diproses'])) bg-gray-200 text-gray-400
                            @else bg-caretel-red text-white @endif 
                            flex items-center justify-center mb-2 z-10">
                            @if(in_array($laporan->status, ['pending', 'diproses']))
                                <i class="fas fa-circle text-xs"></i>
                            @else
                                <i class="fas fa-check"></i>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-gray-900">In Progress</span>
                        <span class="text-xs text-gray-500">Being Fixed</span>
                    </div>

                    <!-- Resolved -->
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full 
                            @if($laporan->status == 'selesai') bg-green-500 text-white
                            @else bg-gray-200 text-gray-400 @endif 
                            flex items-center justify-center mb-2 z-10">
                            @if($laporan->status == 'selesai')
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas fa-circle text-xs"></i>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-gray-900">Resolved</span>
                        <span class="text-xs text-gray-500">Completed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Report Details</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Description</p>
                    <p class="text-gray-900">{{ $laporan->deskripsi }}</p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-2">Evidence Photo</p>
                    <div class="grid grid-cols-2 gap-3">
                        @if($laporan->foto)
                        <img src="{{ Storage::url($laporan->foto) }}" alt="Evidence" class="rounded-lg w-full h-48 object-cover">
                        @else
                        <div class="bg-gray-100 rounded-lg w-full h-48 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Activity Log</h2>
            
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Report has been submitted</p>
                        <p class="text-xs text-gray-500">{{ $laporan->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600 mt-1">Your facility report has been successfully submitted and is waiting for review.</p>
                    </div>
                </div>

                @if($laporan->status != 'pending')
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Report is being reviewed</p>
                        <p class="text-xs text-gray-500">{{ $laporan->updated_at->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600 mt-1">Our team is reviewing your report and will take necessary action.</p>
                    </div>
                </div>
                @endif

                @if($laporan->status == 'selesai')
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Issue has been resolved</p>
                        <p class="text-xs text-gray-500">{{ $laporan->updated_at->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600 mt-1">The facility issue has been successfully fixed. Thank you for your report!</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Assigned Technician -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4">Assigned Technician</h3>
            
            @if($laporan->teknisi_id)
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">{{ strtoupper(substr($laporan->teknisi->name ?? 'TK', 0, 2)) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $laporan->teknisi->name ?? 'Technician' }}</p>
                    <p class="text-xs text-gray-500">Facility Maintenance</p>
                </div>
            </div>
            <button class="w-full mt-4 border border-caretel-red caretel-red py-2 rounded-lg hover:bg-red-50 text-sm font-semibold">
                <i class="fas fa-comment-dots mr-2"></i> Chat
            </button>
            @else
            <p class="text-sm text-gray-500 text-center py-6">
                <i class="fas fa-user-clock text-2xl mb-2 block text-gray-400"></i>
                Waiting for technician assignment
            </p>
            @endif
        </div>

        <!-- Help Center -->
        <div class="bg-gradient-to-br from-red-500 to-pink-500 rounded-lg shadow-sm p-6 text-white">
            <i class="fas fa-headset text-3xl mb-3"></i>
            <h3 class="font-bold text-lg mb-2">Need Help?</h3>
            <p class="text-sm opacity-90 mb-4">Our support team is ready to assist you with any questions.</p>
            <button class="w-full bg-white text-caretel-red py-2 rounded-lg hover:bg-gray-100 font-semibold text-sm">
                Contact Support
            </button>
        </div>
    </div>
</div>
@endsection