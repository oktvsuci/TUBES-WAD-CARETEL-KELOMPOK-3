@extends('layouts.admin')

@section('title', 'Student Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<!-- Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Student Management</h2>
                <p class="text-muted mb-0">View students who have submitted facility reports</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Print List
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Students</p>
                        <h3 class="fw-bold mb-0">{{ $mahasiswaList->total() }}</h3>
                    </div>
                    <i class="fas fa-users text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Active Reporters</p>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['active'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-user-check text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Reports</p>
                        <h3 class="fw-bold mb-0 text-info">{{ $stats['total_reports'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-clipboard-list text-info fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">This Month</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $stats['this_month'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-calendar-alt text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.mahasiswa.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by name or NIM..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="name">Sort by Name</option>
                        <option value="reports" {{ request('sort') == 'reports' ? 'selected' : '' }}>Most Reports</option>
                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Student</th>
                        <th class="py-3">NIM</th>
                        <th class="py-3">Email</th>
                        <th class="py-3 text-center">Total Reports</th>
                        <th class="py-3 text-center">Pending</th>
                        <th class="py-3 text-center">Completed</th>
                        <th class="py-3">Last Report</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswaList as $mahasiswa)
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle me-3" 
                                     style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span class="fw-bold">{{ strtoupper(substr($mahasiswa->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $mahasiswa->name }}</h6>
                                    <small class="text-muted">Student</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $mahasiswa->nim }}</span>
                        </td>
                        <td>
                            <small>{{ $mahasiswa->email }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $mahasiswa->total_reports ?? 0 }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark">{{ $mahasiswa->pending_reports ?? 0 }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $mahasiswa->completed_reports ?? 0 }}</span>
                        </td>
                        <td>
                            @if($mahasiswa->last_report_date)
                            <small>{{ \Carbon\Carbon::parse($mahasiswa->last_report_date)->format('d M Y') }}</small>
                            <br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($mahasiswa->last_report_date)->diffForHumans() }}</small>
                            @else
                            <small class="text-muted">No reports yet</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.mahasiswa.show', $mahasiswa->id) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.laporan.index', ['mahasiswa_id' => $mahasiswa->id]) }}" 
                                   class="btn btn-sm btn-outline-secondary"
                                   title="View Reports">
                                    <i class="fas fa-clipboard-list"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-users text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
                            <p class="text-muted mt-3 mb-0">No students found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
@if($mahasiswaList->hasPages())
<div class="mt-4">
    {{ $mahasiswaList->links() }}
</div>
@endif

<!-- Top Contributors -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">
            <i class="fas fa-trophy me-2 text-warning"></i> Top Contributors
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($topContributors ?? [] as $index => $contributor)
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($index == 0)
                                <div class="bg-warning text-white rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-crown fs-4"></i>
                                </div>
                                @elseif($index == 1)
                                <div class="bg-secondary text-white rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-medal fs-4"></i>
                                </div>
                                @else
                                <div class="bg-danger text-white rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-award fs-4"></i>
                                </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $contributor->name }}</h6>
                                <small class="text-muted">{{ $contributor->total_reports }} reports</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted text-center mb-0">No data available</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection