@extends('layouts.mahasiswa')

@section('title', 'Report History')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('mahasiswa.tracking.index') }}">Track Reports</a></li>
<li class="breadcrumb-item active">Report #{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</li>
@endsection

@section('content')
<!-- Report Header (Sesuai Gambar 3) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h3 class="fw-bold mb-0">Report History</h3>
                <p class="text-muted mb-0">Track all activities and updates related to your report</p>
            </div>
            <div class="col-md-4 text-end">
                <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-print"></i>
                </button>
                <a href="{{ route('mahasiswa.tracking.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>

        <!-- Report Summary (Sesuai Gambar 3) -->
        <div class="row">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    @if($laporan->foto)
                    <img src="{{ asset('storage/' . $laporan->foto) }}" 
                         class="rounded me-3" 
                         style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-tools text-secondary fs-2"></i>
                    </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-1">{{ $laporan->judul }}</h5>
                        <p class="text-muted mb-0 small">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $laporan->lokasi }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Boxes (Sesuai Gambar 3) -->
            <div class="col-md-9">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold mb-0 text-primary">{{ $stats['total_updates'] ?? 0 }}</h4>
                            <small class="text-muted">Total Updates</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold mb-0 text-success">{{ $stats['comments'] ?? 0 }}</h4>
                            <small class="text-muted">Comments</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold mb-0 text-warning">{{ $stats['days_open'] ?? $laporan->created_at->diffInDays(now()) }}</h4>
                            <small class="text-muted">Days Open</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="fw-bold mb-0 text-info">{{ $stats['response_time'] ?? '-' }}</h4>
                            <small class="text-muted">Avg Response</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status & History Table (Sesuai Gambar 3) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-history me-2"></i> Activity Timeline
            </h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary active">All</button>
                <button class="btn btn-outline-secondary">Status Changes</button>
                <button class="btn btn-outline-secondary">Comments</button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 150px;">Date</th>
                        <th style="width: 120px;">Time</th>
                        <th style="width: 150px;">Activity Type</th>
                        <th>Description</th>
                        <th style="width: 150px;">By</th>
                        <th style="width: 120px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history ?? [] as $index => $item)
                    <tr>
                        <td class="text-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 30px; height: 30px; margin: auto;">
                                <i class="fas {{ $item->icon ?? 'fa-circle' }} small"></i>
                            </div>
                        </td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td>{{ $item->created_at->format('H:i A') }}</td>
                        <td>
                            <span class="badge bg-{{ $item->type_color ?? 'secondary' }}">
                                {{ $item->type ?? 'Update' }}
                            </span>
                        </td>
                        <td>{{ $item->description }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle me-2" 
                                     style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <small class="fw-bold">{{ strtoupper(substr($item->user_name, 0, 1)) }}</small>
                                </div>
                                <span class="small">{{ $item->user_name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $item->status ?? $laporan->status }}">
                                {{ ucfirst($item->status ?? $laporan->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <!-- Default History - Report Created -->
                    <tr>
                        <td class="text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 30px; height: 30px; margin: auto;">
                                <i class="fas fa-plus text-primary small"></i>
                            </div>
                        </td>
                        <td>{{ $laporan->created_at->format('d M Y') }}</td>
                        <td>{{ $laporan->created_at->format('H:i A') }}</td>
                        <td><span class="badge bg-primary">Created</span></td>
                        <td>Report submitted by student</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle me-2" 
                                     style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <small class="fw-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</small>
                                </div>
                                <span class="small">{{ Auth::user()->name }}</span>
                            </div>
                        </td>
                        <td><span class="status-badge status-pending">Pending</span></td>
                    </tr>

                    @if($laporan->status != 'pending')
                    <tr>
                        <td class="text-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 30px; height: 30px; margin: auto;">
                                <i class="fas fa-tools text-warning small"></i>
                            </div>
                        </td>
                        <td>{{ $laporan->updated_at->format('d M Y') }}</td>
                        <td>{{ $laporan->updated_at->format('H:i A') }}</td>
                        <td><span class="badge bg-warning text-dark">Assigned</span></td>
                        <td>Report assigned to technician: {{ $laporan->teknisi_nama ?? 'N/A' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle me-2" 
                                     style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <small class="fw-bold">A</small>
                                </div>
                                <span class="small">Admin</span>
                            </div>
                        </td>
                        <td><span class="status-badge status-diproses">In Progress</span></td>
                    </tr>
                    @endif

                    @if($laporan->status == 'selesai')
                    <tr>
                        <td class="text-center">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 30px; height: 30px; margin: auto;">
                                <i class="fas fa-check text-success small"></i>
                            </div>
                        </td>
                        <td>{{ $laporan->updated_at->format('d M Y') }}</td>
                        <td>{{ $laporan->updated_at->format('H:i A') }}</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>Report has been completed and closed</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle me-2" 
                                     style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <small class="fw-bold">{{ strtoupper(substr($laporan->teknisi_nama ?? 'T', 0, 1)) }}</small>
                                </div>
                                <span class="small">{{ $laporan->teknisi_nama ?? 'Technician' }}</span>
                            </div>
                        </td>
                        <td><span class="status-badge status-selesai">Completed</span></td>
                    </tr>
                    @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Report Details -->
<div class="row g-4 mt-2">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-alt me-2"></i> Report Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Category</small>
                        <p class="fw-semibold">{{ ucfirst($laporan->kategori) }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Priority</small>
                        <p class="fw-semibold">{{ ucfirst($laporan->prioritas) }}</p>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <p style="white-space: pre-wrap;">{{ $laporan->deskripsi }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-info-circle me-2"></i> Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Report ID</small>
                    <p class="fw-semibold mb-0">#{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="status-badge status-{{ $laporan->status }}">{{ ucfirst($laporan->status) }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
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
@endsection