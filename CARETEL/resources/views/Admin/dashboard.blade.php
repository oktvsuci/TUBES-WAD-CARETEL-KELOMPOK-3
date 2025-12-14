@extends('layouts.admin')

@section('title', 'Manage Reports')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">All Reports</li>
@endsection

@section('content')
<!-- Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold mb-2">All Facility Reports</h2>
                <p class="text-muted mb-0">Manage and monitor all facility maintenance reports</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                    <button class="btn btn-caretel-red" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="fas fa-user-plus me-2"></i> Assign Technician
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Total</p>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h4>
                    </div>
                    <i class="fas fa-clipboard-list text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Pending</p>
                        <h4 class="fw-bold mb-0 text-warning">{{ $stats['pending'] ?? 0 }}</h4>
                    </div>
                    <i class="fas fa-clock text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">In Progress</p>
                        <h4 class="fw-bold mb-0" style="color: #f97316;">{{ $stats['diproses'] ?? 0 }}</h4>
                    </div>
                    <i class="fas fa-tools fs-3" style="color: #f97316;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Completed</p>
                        <h4 class="fw-bold mb-0 text-success">{{ $stats['selesai'] ?? 0 }}</h4>
                    </div>
                    <i class="fas fa-check-circle text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.laporan.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by ID, title, location..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>In Progress</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="prioritas" class="form-select">
                        <option value="">All Priority</option>
                        <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>High</option>
                        <option value="sedang" {{ request('prioritas') == 'sedang' ? 'selected' : '' }}>Medium</option>
                        <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="kategori" class="form-select">
                        <option value="">All Category</option>
                        <option value="electrical">Electrical</option>
                        <option value="plumbing">Plumbing</option>
                        <option value="hvac">HVAC</option>
                        <option value="furniture">Furniture</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reports Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="py-3">Issue</th>
                        <th class="py-3">Reporter</th>
                        <th class="py-3">Priority</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Technician</th>
                        <th class="py-3">Date</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $laporan)
                    <tr>
                        <td class="px-4">
                            <span class="badge bg-secondary">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-1 fw-semibold">{{ Str::limit($laporan->judul, 40) }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($laporan->lokasi, 30) }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="small fw-semibold">{{ $laporan->mahasiswa_nama }}</div>
                                <small class="text-muted">{{ $laporan->mahasiswa_nim }}</small>
                            </div>
                        </td>
                        <td>
                            @if($laporan->prioritas == 'tinggi')
                            <span class="badge bg-danger">High</span>
                            @elseif($laporan->prioritas == 'sedang')
                            <span class="badge bg-warning text-dark">Medium</span>
                            @else
                            <span class="badge bg-success">Low</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $laporan->status }}">
                                {{ ucfirst($laporan->status) }}
                            </span>
                        </td>
                        <td>
                            @if($laporan->teknisi_id)
                            <span class="small">{{ $laporan->teknisi_nama }}</span>
                            @else
                            <span class="badge bg-secondary">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $laporan->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.laporan.show', $laporan->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox text-muted" style="font-size: 3rem; opacity: 0.2;"></i>
                            <p class="text-muted mt-3">No reports found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
@if($laporans->hasPages())
<div class="mt-4">
    {{ $laporans->links() }}
</div>
@endif
@endsection