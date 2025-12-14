@extends('layouts.admin')

@section('title', 'Report History')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Report History</li>
@endsection

@section('content')
<!-- Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Report History & Analytics</h2>
                <p class="text-muted mb-0">View historical data and performance metrics</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-download me-2"></i> Export History
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.laporan.history') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Date Range</label>
                    <select name="period" class="form-select">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Category</label>
                    <select name="kategori" class="form-select">
                        <option value="">All Categories</option>
                        <option value="electrical">Electrical</option>
                        <option value="plumbing">Plumbing</option>
                        <option value="hvac">HVAC</option>
                        <option value="furniture">Furniture</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="diproses">In Progress</option>
                        <option value="selesai">Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Reports</p>
                        <h3 class="fw-bold mb-0">{{ $stats['total'] ?? 245 }}</h3>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 12% vs last period
                        </small>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="fas fa-clipboard-list text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Avg Resolution Time</p>
                        <h3 class="fw-bold mb-0">2.4d</h3>
                        <small class="text-success">
                            <i class="fas fa-arrow-down"></i> 15% faster
                        </small>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="fas fa-clock text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Completion Rate</p>
                        <h3 class="fw-bold mb-0">87%</h3>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 3% increase
                        </small>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="fas fa-check-circle text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">User Satisfaction</p>
                        <h3 class="fw-bold mb-0">4.5/5</h3>
                        <small class="text-muted">Based on feedback</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="fas fa-star text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <!-- Monthly Trend -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Monthly Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">By Category</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Completed Reports -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Recently Completed Reports</h5>
            <a href="{{ route('admin.laporan.index', ['status' => 'selesai']) }}" class="text-caretel-red text-decoration-none">
                View all <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="py-3">Issue</th>
                        <th class="py-3">Category</th>
                        <th class="py-3">Technician</th>
                        <th class="py-3">Completion Date</th>
                        <th class="py-3">Resolution Time</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedReports ?? [] as $report)
                    <tr>
                        <td class="px-4">
                            <span class="badge bg-secondary">#{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ Str::limit($report->judul, 40) }}</h6>
                                <small class="text-muted">{{ $report->lokasi }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($report->kategori) }}</span>
                        </td>
                        <td>{{ $report->teknisi_nama }}</td>
                        <td>{{ $report->updated_at->format('d M Y') }}</td>
                        <td>
                            @php
                                $days = $report->created_at->diffInDays($report->updated_at);
                            @endphp
                            <span class="badge {{ $days <= 2 ? 'bg-success' : ($days <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                {{ $days }}d
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.laporan.show', $report->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No completed reports in this period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Trend Chart
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Reported',
                data: [45, 52, 48, 60, 55, 68, 62, 70, 65, 75, 72, 80],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Completed',
                data: [40, 48, 45, 55, 52, 65, 58, 68, 62, 72, 68, 78],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Category Chart
const categoryCtx = document.getElementById('categoryChart');
if (categoryCtx) {
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Electrical', 'Plumbing', 'HVAC', 'Furniture'],
            datasets: [{
                data: [35, 25, 25, 15],
                backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>
@endpush