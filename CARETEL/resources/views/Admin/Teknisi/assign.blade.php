@extends('layouts.admin')

@section('title', 'Assign Technician')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.laporan.index') }}">Reports</a></li>
<li class="breadcrumb-item active">Assign Technician</li>
@endsection

@section('content')
<div class="row">
    <!-- Left: Report Details -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-clipboard-list me-2"></i> Report Details
                </h5>
            </div>
            <div class="card-body">
                <!-- Status Badge -->
                <div class="mb-3">
                    <span class="badge bg-secondary">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="status-badge status-{{ $laporan->status }}">
                        {{ ucfirst($laporan->status) }}
                    </span>
                    @if($laporan->prioritas == 'tinggi')
                    <span class="badge bg-danger">
                        <i class="fas fa-exclamation-triangle"></i> High Priority
                    </span>
                    @elseif($laporan->prioritas == 'sedang')
                    <span class="badge bg-warning text-dark">Medium</span>
                    @else
                    <span class="badge bg-success">Low</span>
                    @endif
                </div>

                <!-- Title -->
                <h4 class="fw-bold mb-3">{{ $laporan->judul }}</h4>

                <!-- Location -->
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Location</small>
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                        {{ $laporan->lokasi }}
                    </p>
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Category</small>
                    <span class="badge bg-info">{{ ucfirst($laporan->kategori) }}</span>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Description</small>
                    <p class="mb-0 text-muted" style="font-size: 14px;">
                        {{ Str::limit($laporan->deskripsi, 200) }}
                    </p>
                </div>

                <!-- Photo -->
                @if($laporan->foto)
                <div class="mb-3">
                    <small class="text-muted d-block mb-2">Photo Evidence</small>
                    <img src="{{ asset('storage/' . $laporan->foto) }}" 
                         class="img-fluid rounded w-100" 
                         style="max-height: 200px; object-fit: cover;">
                </div>
                @endif

                <hr>

                <!-- Reporter Info -->
                <div class="mb-3">
                    <small class="text-muted d-block mb-2">Reported By</small>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle me-3" 
                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <span class="text-white fw-bold">
                                {{ strtoupper(substr($laporan->mahasiswa_nama, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="mb-0 fw-semibold">{{ $laporan->mahasiswa_nama }}</p>
                            <small class="text-muted">{{ $laporan->mahasiswa_nim }}</small>
                        </div>
                    </div>
                </div>

                <!-- Date -->
                <div class="mb-0">
                    <small class="text-muted d-block mb-1">Reported At</small>
                    <p class="mb-0">{{ $laporan->created_at->format('d M Y, H:i') }}</p>
                    <small class="text-muted">{{ $laporan->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Available Technicians -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-users-cog me-2"></i> Select Technician
                </h5>
            </div>
            <div class="card-body">
                <!-- Search Box -->
                <div class="mb-4">
                    <input type="text" 
                           class="form-control" 
                           id="searchTechnician" 
                           placeholder="Search technician by name...">
                </div>

                <!-- Technicians List -->
                <div class="technician-list">
                    @forelse($teknisiList as $teknisi)
                    <div class="card border mb-3 technician-item">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Avatar & Info -->
                                <div class="col-md-7">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-caretel-red text-white rounded-circle me-3" 
                                             style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span class="fw-bold fs-5">
                                                {{ strtoupper(substr($teknisi->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $teknisi->name }}</h6>
                                            <small class="text-muted d-block">{{ $teknisi->email }}</small>
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark me-1">
                                                    <i class="fas fa-tasks"></i> {{ $teknisi->active_tasks ?? 0 }}
                                                </span>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> {{ $teknisi->completed_tasks ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Workload & Action -->
                                <div class="col-md-5">
                                    <!-- Workload Bar -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">Workload</small>
                                            @php
                                                $workload = ($teknisi->active_tasks ?? 0) * 20;
                                                $workload = min($workload, 100);
                                            @endphp
                                            <small class="fw-semibold">{{ $workload }}%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $workload > 80 ? 'bg-danger' : ($workload > 50 ? 'bg-warning' : 'bg-success') }}" 
                                                 style="width: {{ $workload }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Assign Button -->
                                    <form action="{{ route('admin.laporan.assign', $laporan->id) }}" 
                                          method="POST" 
                                          class="d-grid">
                                        @csrf
                                        <input type="hidden" name="teknisi_id" value="{{ $teknisi->id }}">
                                        <button type="submit" class="btn btn-caretel-red">
                                            <i class="fas fa-user-check me-2"></i> Assign
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-users-cog text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
                        <h5 class="fw-bold mt-3 mb-2">No Technicians Available</h5>
                        <p class="text-muted mb-4">Please add technicians first before assigning tasks</p>
                        <a href="{{ route('admin.teknisi.index') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i> Manage Technicians
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-3">
            <a href="{{ route('admin.laporan.show', $laporan->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Report
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search Technician
document.getElementById('searchTechnician')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.technician-item');
    
    items.forEach(item => {
        const name = item.querySelector('h6')?.textContent.toLowerCase();
        const email = item.querySelector('.text-muted')?.textContent.toLowerCase();
        
        if (name?.includes(searchTerm) || email?.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endpush