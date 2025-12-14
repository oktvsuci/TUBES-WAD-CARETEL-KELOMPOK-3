@extends('layouts.mahasiswa')

@section('title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Welcome Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Welcome back, {{ Auth::user()->name }} 👋</h2>
                <p class="text-muted mb-2">Monitor and manage your facility reports across the campus.</p>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i> Last login: Today, {{ now()->format('H:i A') }}
                </small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('mahasiswa.laporan.create') }}" class="btn btn-caretel-red btn-lg">
                    <i class="fas fa-plus me-2"></i> New Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total Reports -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="border-left-color: #3b82f6 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted mb-1 small fw-semibold">TOTAL REPORTS</p>
                        <h2 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h2>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-file-alt text-primary fs-4"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-arrow-up me-1"></i> All time
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="border-left-color: #fbbf24 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted mb-1 small fw-semibold">PENDING</p>
                        <h2 class="fw-bold mb-0">{{ $stats['pending'] ?? 0 }}</h2>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fas fa-hourglass-half text-warning fs-4"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock me-1"></i> Waiting review
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="border-left-color: #f97316 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted mb-1 small fw-semibold">IN PROGRESS</p>
                        <h2 class="fw-bold mb-0">{{ $stats['diproses'] ?? 0 }}</h2>
                    </div>
                    <div class="rounded-circle" style="background-color: #fed7aa; padding: 12px;">
                        <i class="fas fa-spinner" style="color: #f97316; font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge" style="background-color: #fed7aa; color: #f97316;">
                        <i class="fas fa-tools me-1"></i> Being fixed
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolved -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100" style="border-left-color: #10b981 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted mb-1 small fw-semibold">RESOLVED</p>
                        <h2 class="fw-bold mb-0">{{ $stats['selesai'] ?? 0 }}</h2>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fas fa-check-double text-success fs-4"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success bg-opacity-10 text-success">
                        <i class="fas fa-check-circle me-1"></i> Completed
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Reports (Left Side) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Recent Facility Issues</h5>
                    <a href="{{ route('mahasiswa.laporan.index') }}" class="text-caretel-red text-decoration-none">
                        View all <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($recentReports ?? [] as $report)
                <div class="p-3 border-bottom card-hover">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                             style="width: 48px; height: 48px;">
                            <i class="fas fa-tools text-secondary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">{{ $report->judul }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $report->lokasi }}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> {{ $report->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <span class="status-badge status-{{ $report->status }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No recent reports</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Monthly Activity Chart -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Monthly Report Activity</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Facility Map -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Facility Map</h5>
            </div>
            <div class="card-body">
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-0">Interactive Campus Map</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="fw-semibold mb-2">Need immediate help?</h6>
                    <p class="text-muted small mb-3">Contact our facility management team for urgent issues.</p>
                    <button class="btn btn-caretel-red w-100">
                        <i class="fas fa-phone me-2"></i> Call 1500-835
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('mahasiswa.laporan.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i> New Report
                    </a>
                    <a href="{{ route('mahasiswa.tracking.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-map-marker-alt me-2"></i> Track Reports
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-question-circle me-2"></i> Help Center
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Monthly Report Chart
const ctx = document.getElementById('monthlyChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Reports',
                data: @json($chartData ?? [12, 19, 15, 25, 22, 30, 28, 35, 32, 38, 42, 45]),
                borderColor: '#E30613',
                backgroundColor: 'rgba(227, 6, 19, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    }
                }
            }
        }
    });
}
</script>
@endpush