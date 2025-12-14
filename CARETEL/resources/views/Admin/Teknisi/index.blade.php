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
                <p class="text-muted mb-0">Manage facility maintenance technicians and their performance</p>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Technicians</p>
                        <h3 class="fw-bold mb-0">{{ $teknisiList->count() }}</h3>
                    </div>
                    <i class="fas fa-users-cog text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Active Tasks</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $stats['active_tasks'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-tasks text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Completed This Month</p>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['completed_month'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-check-circle text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Avg Response Time</p>
                        <h3 class="fw-bold mb-0 text-info">2.4h</h3>
                    </div>
                    <i class="fas fa-clock text-info fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Technicians List -->
<div class="row g-4">
    @forelse($teknisiList as $teknisi)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <!-- Header -->
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-caretel-red text-white rounded-circle me-3" 
                         style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span class="fw-bold fs-4">{{ strtoupper(substr($teknisi->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $teknisi->name }}</h5>
                        <small class="text-muted d-block">{{ $teknisi->email }}</small>
                        <span class="badge bg-success mt-2">
                            <i class="fas fa-circle" style="font-size: 6px;"></i> Active
                        </span>
                    </div>
                </div>

                <hr>

                <!-- Stats -->
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <h4 class="fw-bold mb-0 text-warning">{{ $teknisi->active_tasks ?? 0 }}</h4>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <h4 class="fw-bold mb-0 text-success">{{ $teknisi->completed_tasks ?? 0 }}</h4>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>

                <!-- Performance -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Performance</small>
                        <small class="fw-semibold">{{ $teknisi->performance ?? 85 }}%</small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $teknisi->performance ?? 85 }}%"></div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.teknisi.show', $teknisi->id) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-2"></i> View Details
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal{{ $teknisi->id }}">
                            <i class="fas fa-edit me-2"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="confirmDelete({{ $teknisi->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal for each technician -->
    <div class="modal fade" id="editModal{{ $teknisi->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Technician</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.teknisi.update', $teknisi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ $teknisi->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ $teknisi->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="{{ $teknisi->phone ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-caretel-red">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-users-cog text-muted" style="font-size: 5rem; opacity: 0.2;"></i>
                <h4 class="fw-bold mt-4 mb-2">No Technicians Yet</h4>
                <p class="text-muted mb-4">Add your first technician to start managing facility reports</p>
                <button class="btn btn-caretel-red btn-lg" data-bs-toggle="modal" data-bs-target="#addTeknisiModal">
                    <i class="fas fa-user-plus me-2"></i> Add First Technician
                </button>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Add Technician Modal -->
<div class="modal fade" id="addTeknisiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.teknisi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" 
                               placeholder="Enter full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" 
                               placeholder="Enter email address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" 
                               placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Enter password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" 
                               placeholder="Confirm password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-caretel-red">
                        <i class="fas fa-plus me-2"></i> Add Technician
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if(confirm('Are you sure you want to delete this technician?')) {
        // Submit delete form
        document.getElementById('deleteForm' + id).submit();
    }
}
</script>
@endpush