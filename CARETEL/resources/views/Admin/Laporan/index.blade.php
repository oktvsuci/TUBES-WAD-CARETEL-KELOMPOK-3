@extends('layouts.admin')

@section('title', 'Manage Technicians')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Technicians</li>
@endsection

@section('content')
<!-- Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Manage Technicians</h2>
                <p class="text-muted mb-0">Manage facility maintenance technicians and their assignments</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-caretel-red" data-bs-toggle="modal" data-bs-target="#addTeknisiModal">
                    <i class="fas fa-user-plus me-2"></i> Add Technician
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
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Total Technicians</p>
                        <h3 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-users-cog text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Active Tasks</p>
                        <h3 class="fw-bold mb-0">{{ $stats['active_tasks'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-tasks text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Completed This Month</p>
                        <h3 class="fw-bold mb-0">{{ $stats['completed'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-check-circle text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Avg Response Time</p>
                        <h3 class="fw-bold mb-0">{{ $stats['avg_time'] ?? '2.4' }}h</h3>
                    </div>
                    <i class="fas fa-clock text-info fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Search technician by name or email...">
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i> Search
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Technicians List -->
<div class="row g-4">
    @forelse($teknisi as $tech)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-caretel-red text-white rounded-circle me-3" 
                         style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span class="fw-bold fs-4">{{ strtoupper(substr($tech->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $tech->