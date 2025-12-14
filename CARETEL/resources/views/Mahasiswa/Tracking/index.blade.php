@extends('layouts.mahasiswa')

@section('title', 'Track My Reports')

@section('breadcrumb')
<li class="breadcrumb-item active">Track Reports</li>
@endsection

@section('content')
<!-- Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Track My Reports</h2>
                <p class="text-muted mb-0">Monitor the progress of your facility reports in real-time</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('mahasiswa.laporan.create') }}" class="btn btn-caretel-red">
                    <i class="fas fa-plus me-2"></i> New Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="opacity-75 mb-1 small">Active Reports</p>
                        <h2 class="fw-bold mb-0">{{ $stats['active'] ?? $laporans->where('status', '!=', 'selesai')->count() }}</h2>
                    </div>
                    <i class="fas fa-clipboard-list fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="opacity-75 mb-1 small">Pending Review</p>
                        <h2 class="fw-bold mb-0">{{ $stats['pending'] ?? $laporans->where('status', 'pending')->count() }}</h2>
                    </div>
                    <i class="fas fa-clock fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="opacity-75 mb-1 small">In Progress</p>
                        <h2 class="fw-bold mb-0">{{ $stats['progress'] ?? $laporans->where('status', 'diproses')->count() }}</h2>
                    </div>
                    <i class="fas fa-tools fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="opacity-75 mb-1 small">Completed</p>
                        <h2 class="fw-bold mb-0">{{ $stats['completed'] ?? $laporans->where('status', 'selesai')->count() }}</h2>
                    </div>
                    <i class="fas fa-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <ul class="nav nav-pills" id="statusTabs">
            <li class="nav-item">
                <a class="nav-link {{ request('status') == '' || request('status') == 'all' ? 'active' : '' }}" 
                   href="?status=all">
                    All Reports ({{ $laporans->total() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                   href="?status=pending">
                    Pending ({{ $laporans->where('status', 'pending')->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'diproses' ? 'active' : '' }}" 
                   href="?status=diproses">
                    In Progress ({{ $laporans->where('status', 'diproses')->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'selesai' ? 'active' : '' }}" 
                   href="?status=selesai">
                    Completed ({{ $laporans->where('status', 'selesai')->count() }})
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Reports List -->
<div class="row g-4">
    @forelse($laporans as $laporan)
    <div class="col-12">
        <div class="card border-0 shadow-sm card-hover">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <!-- Left: Report Info -->
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start mb-3">
                            <!-- Icon -->
                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                 style="width: 60px; height: 60px; flex-shrink: 0;">
                                @if($laporan->foto)
                                <img src="{{ asset('storage/' . $laporan->foto) }}" 
                                     class="rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <i class="fas fa-tools text-secondary fs-4"></i>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-secondary me-2">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span class="status-badge status-{{ $laporan->status }}">
                                        {{ ucfirst($laporan->status) }}
                                    </span>
                                    @if($laporan->prioritas == 'tinggi')
                                    <span class="badge bg-danger ms-2">
                                        <i class="fas fa-exclamation-triangle"></i> High Priority
                                    </span>
                                    @endif
                                </div>

                                <h5 class="fw-bold mb-2">{{ $laporan->judul }}</h5>
                                
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span>
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $laporan->lokasi }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $laporan->created_at->format('d M Y') }}
                                    </span>
                                    <span>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $laporan->created_at->diffForHumans() }}
                                    </span>
                                    @if($laporan->teknisi_nama)
                                    <span>
                                        <i class="fas fa-user-cog me-1"></i>
                                        {{ $laporan->teknisi_nama }}
                                    </span>
                                    @endif
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted">Progress</span>
                                        <span class="small fw-semibold">
                                            @php
                                                $progress = 0;
                                                if($laporan->status == 'pending') $progress = 25;
                                                elseif($laporan->status == 'diproses') $progress = 66;
                                                elseif($laporan->status == 'selesai') $progress = 100;
                                            @endphp
                                            {{ $progress }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-caretel-red" 
                                             role="progressbar" 
                                             style="width: {{ $progress }}%"
                                             aria-valuenow="{{ $progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('mahasiswa.tracking.show', $laporan->id) }}" 
                               class="btn btn-caretel-red">
                                <i class="fas fa-eye me-2"></i> View Details
                            </a>
                            
                            @if($laporan->status == 'pending')
                            <div class="btn-group">
                                <a href="{{ route('mahasiswa.laporan.edit', $laporan->id) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('mahasiswa.laporan.destroy', $laporan->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Cancel this report?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list text-muted mb-4" style="font-size: 5rem; opacity: 0.2;"></i>
                <h4 class="fw-bold mb-2">No Reports Yet</h4>
                <p class="text-muted mb-4">You haven't submitted any facility reports. Start by creating your first report!</p>
                <a href="{{ route('mahasiswa.laporan.create') }}" class="btn btn-caretel-red btn-lg">
                    <i class="fas fa-plus me-2"></i> Create First Report
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($laporans->hasPages())
<div class="mt-4">
    {{ $laporans->links() }}
</div>
@endif

<!-- Help Section -->
<div class="card border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-2">
                    <i class="fas fa-question-circle text-primary me-2"></i>
                    Need Help Tracking Your Report?
                </h5>
                <p class="text-muted mb-0">Our support team is available 24/7 to assist you with any questions about your facility reports.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-headset me-2"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection