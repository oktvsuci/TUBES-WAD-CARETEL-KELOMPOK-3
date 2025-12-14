@extends('layouts.admin')

@section('title', 'Report Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.laporan.index') }}">Reports</a></li>
<li class="breadcrumb-item active">Report #{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Header Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="mb-2">
                            <span class="badge bg-secondary me-2">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="status-badge status-{{ $laporan->status }}">
                                {{ ucfirst($laporan->status) }}
                            </span>
                            @if($laporan->prioritas == 'tinggi')
                            <span class="badge bg-danger ms-1">
                                <i class="fas fa-exclamation-triangle"></i> High Priority
                            </span>
                            @elseif($laporan->prioritas == 'sedang')
                            <span class="badge bg-warning text-dark ms-1">Medium Priority</span>
                            @else
                            <span class="badge bg-success ms-1">Low Priority</span>
                            @endif
                        </div>
                        <h2 class="fw-bold mb-2">{{ $laporan->judul }}</h2>
                        <div class="text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $laporan->lokasi }}
                            <span class="mx-2">•</span>
                            <i class="fas fa-tag me-2"></i>{{ ucfirst($laporan->kategori) }}
                            <span class="mx-2">•</span>
                            <i class="fas fa-clock me-2"></i>{{ $laporan->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                    <i class="fas fa-edit me-2"></i> Update Status
                                </a>
                            </li>
                            @if(!$laporan->teknisi_id)
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#assignModal">
                                    <i class="fas fa-user-plus me-2"></i> Assign Technician
                                </a>
                            </li>
                            @else
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#reassignModal">
                                    <i class="fas fa-user-edit me-2"></i> Reassign Technician
                                </a>
                            </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i> Print Report
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.laporan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Delete this report?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-trash me-2"></i> Delete Report
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo -->
        @if($laporan->foto)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <img src="{{ asset('storage/' . $laporan->foto) }}" 
                     class="img-fluid w-100" 
                     style="max-height: 500px; object-fit: cover;">
            </div>
        </div>
        @endif

        <!-- Description -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-align-left me-2"></i> Description</h5>
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $laporan->deskripsi }}</p>
            </div>
        </div>

        <!-- Activity Log / Comments -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-history me-2"></i> Activity Log</h5>
            </div>
            <div class="card-body">
                @forelse($laporan->komentar ?? [] as $activity)
                <div class="d-flex mb-3 pb-3 border-bottom">
                    <div class="bg-light rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user text-secondary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $activity->user_name }}</strong>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 mt-1">{{ $activity->komentar }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-history fs-1 opacity-25 mb-3"></i>
                    <p class="mb-0">No activity yet</p>
                </div>
                @endforelse

                <!-- Add Comment Form -->
                <form action="{{ route('admin.komentar.store', $laporan->id) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               name="komentar" 
                               placeholder="Add a note or comment..."
                               required>
                        <button class="btn btn-caretel-red" type="submit">
                            <i class="fas fa-paper-plane"></i> Add Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-bolt me-2"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                        <i class="fas fa-edit me-2"></i> Update Status
                    </button>
                    @if(!$laporan->teknisi_id)
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="fas fa-user-plus me-2"></i> Assign Technician
                    </button>
                    @endif
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-tasks me-2"></i> Status Progress</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item {{ $laporan->status == 'pending' ? 'active' : 'completed' }}">
                        <div class="timeline-marker">
                            <i class="fas {{ $laporan->status == 'pending' ? 'fa-circle' : 'fa-check-circle' }}"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Reported</h6>
                            <small class="text-muted">{{ $laporan->created_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>

                    <div class="timeline-item {{ $laporan->status == 'diproses' ? 'active' : ($laporan->status == 'selesai' ? 'completed' : '') }}">
                        <div class="timeline-marker">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">In Progress</h6>
                            <small class="text-muted">
                                @if(in_array($laporan->status, ['diproses', 'selesai']))
                                    {{ $laporan->updated_at->format('d M Y, H:i') }}
                                @else
                                    Waiting
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="timeline-item {{ $laporan->status == 'selesai' ? 'completed' : '' }}">
                        <div class="timeline-marker">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Completed</h6>
                            <small class="text-muted">
                                @if($laporan->status == 'selesai')
                                    {{ $laporan->updated_at->format('d M Y, H:i') }}
                                @else
                                    Pending
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporter Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-user me-2"></i> Reporter</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-white fw-bold fs-5">{{ strtoupper(substr($laporan->mahasiswa_nama, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $laporan->mahasiswa_nama }}</h6>
                        <small class="text-muted">{{ $laporan->mahasiswa_nim }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Technician -->
        @if($laporan->teknisi_id)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-user-cog me-2"></i> Assigned Technician</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-caretel-red rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-white fw-bold fs-5">{{ strtoupper(substr($laporan->teknisi_nama, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $laporan->teknisi_nama }}</h6>
                        <small class="text-muted">Facility Technician</small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Report Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-info-circle me-2"></i> Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Report ID</small>
                    <p class="mb-0 fw-semibold">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Category</small>
                    <p class="mb-0">{{ ucfirst($laporan->kategori) }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Priority</small>
                    <p class="mb-0">{{ ucfirst($laporan->prioritas) }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Submitted</small>
                    <p class="mb-0">{{ $laporan->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <small class="text-muted d-block">Last Updated</small>
                    <p class="mb-0">{{ $laporan->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Report Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.laporan.update-status', $laporan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $laporan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="diproses" {{ $laporan->status == 'diproses' ? 'selected' : '' }}>In Progress</option>
                            <option value="selesai" {{ $laporan->status == 'selesai' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Add Note</label>
                        <textarea name="komentar" class="form-control" rows="3" 
                                  placeholder="Add note about status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-caretel-red">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Technician Modal -->
@if(!$laporan->teknisi_id)
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.laporan.assign', $laporan->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Technician</label>
                        <select name="teknisi_id" class="form-select" required>
                            <option value="">Choose technician...</option>
                            @foreach($teknisiList ?? [] as $teknisi)
                            <option value="{{ $teknisi->id }}">{{ $teknisi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-caretel-red">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-item.active .timeline-marker {
    border-color: #3b82f6;
    background: #3b82f6;
    color: white;
}

.timeline-item.completed .timeline-marker {
    border-color: #10b981;
    background: #10b981;
    color: white;
}

.timeline-content h6 {
    font-size: 14px;
    color: #6b7280;
}

.timeline-item.active .timeline-content h6,
.timeline-item.completed .timeline-content h6 {
    color: #111827;
    font-weight: 600;
}
</style>
@endpush