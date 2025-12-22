@extends('layouts.teknisi')

@section('title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Welcome Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="fw-bold mb-2">Welcome, {{ Auth::user()->name }} 👋</h2>
        <p class="text-muted mb-0">Here's your task overview for today</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
            <div class="card-body text-white">
                <h3 class="fw-bold">{{ $tugasPending }}</h3>
                <p class="mb-0">Pending Tasks</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
            <div class="card-body text-white">
                <h3 class="fw-bold">{{ $tugasProses }}</h3>
                <p class="mb-0">In Progress</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body text-white">
                <h3 class="fw-bold">{{ $tugasSelesai }}</h3>
                <p class="mb-0">Completed</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('teknisi.tugas.index') }}" class="btn btn-outline-primary w-100">
                    <i class="fas fa-tasks me-2"></i> View Tasks
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('teknisi.riwayat.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-history me-2"></i> History
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('teknisi.profil.index') }}" class="btn btn-outline-info w-100">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection