@extends('layouts.mahasiswa')

@section('title', 'Edit Report')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('mahasiswa.laporan.index') }}">My Reports</a></li>
<li class="breadcrumb-item active">Edit Report #{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Header Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2">Edit Report #{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}</h2>
                        <p class="text-muted mb-0">Update your facility report details</p>
                    </div>
                    <span class="status-badge status-{{ $laporan->status }}">
                        {{ ucfirst($laporan->status) }}
                    </span>
                </div>
            </div>
        </div>

        @if($laporan->status != 'pending')
        <!-- Warning Alert -->
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Note:</strong> This report has been processed and cannot be edited. You can only edit reports with "Pending" status.
        </div>
        @else
        <!-- Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('mahasiswa.laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Report Title -->
                    <div class="mb-4">
                        <label for="judul" class="form-label fw-semibold">
                            Report Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('judul') is-invalid @enderror" 
                               id="judul" 
                               name="judul" 
                               value="{{ old('judul', $laporan->judul) }}"
                               required>
                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label for="kategori" class="form-label fw-semibold">
                            Category <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-lg @error('kategori') is-invalid @enderror" 
                                id="kategori" 
                                name="kategori" 
                                required>
                            <option value="">Select Category</option>
                            <option value="electrical" {{ old('kategori', $laporan->kategori) == 'electrical' ? 'selected' : '' }}>⚡ Electrical Issues</option>
                            <option value="plumbing" {{ old('kategori', $laporan->kategori) == 'plumbing' ? 'selected' : '' }}>🚰 Plumbing & Water</option>
                            <option value="hvac" {{ old('kategori', $laporan->kategori) == 'hvac' ? 'selected' : '' }}>❄️ HVAC (AC/Heating)</option>
                            <option value="furniture" {{ old('kategori', $laporan->kategori) == 'furniture' ? 'selected' : '' }}>🪑 Furniture & Fixtures</option>
                            <option value="cleaning" {{ old('kategori', $laporan->kategori) == 'cleaning' ? 'selected' : '' }}>🧹 Cleaning & Sanitation</option>
                            <option value="safety" {{ old('kategori', $laporan->kategori) == 'safety' ? 'selected' : '' }}>🚨 Safety & Security</option>
                            <option value="technology" {{ old('kategori', $laporan->kategori) == 'technology' ? 'selected' : '' }}>💻 Technology & Equipment</option>
                            <option value="other" {{ old('kategori', $laporan->kategori) == 'other' ? 'selected' : '' }}>📋 Other</option>
                        </select>
                        @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="mb-4">
                        <label for="lokasi" class="form-label fw-semibold">
                            Location <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" 
                                   class="form-control @error('lokasi') is-invalid @enderror" 
                                   id="lokasi" 
                                   name="lokasi" 
                                   value="{{ old('lokasi', $laporan->lokasi) }}"
                                   required>
                            @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" 
                                  name="deskripsi" 
                                  rows="6" 
                                  required>{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Priority Level</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="prioritas" id="prioritas-low" value="rendah" {{ old('prioritas', $laporan->prioritas) == 'rendah' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success" for="prioritas-low">
                                <i class="fas fa-circle me-2"></i> Low
                            </label>

                            <input type="radio" class="btn-check" name="prioritas" id="prioritas-medium" value="sedang" {{ old('prioritas', $laporan->prioritas) == 'sedang' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="prioritas-medium">
                                <i class="fas fa-circle me-2"></i> Medium
                            </label>

                            <input type="radio" class="btn-check" name="prioritas" id="prioritas-high" value="tinggi" {{ old('prioritas', $laporan->prioritas) == 'tinggi' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger" for="prioritas-high">
                                <i class="fas fa-circle me-2"></i> High
                            </label>
                        </div>
                    </div>

                    <!-- Current Photo -->
                    @if($laporan->foto)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Current Photo</label>
                        <div class="border rounded p-3 bg-light">
                            <img src="{{ asset('storage/' . $laporan->foto) }}" 
                                 class="img-fluid rounded" 
                                 style="max-height: 200px;">
                        </div>
                    </div>
                    @endif

                    <!-- New Photo Upload -->
                    <div class="mb-4">
                        <label for="foto" class="form-label fw-semibold">
                            {{ $laporan->foto ? 'Change Photo (Optional)' : 'Upload Photo (Optional)' }}
                        </label>
                        <input type="file" 
                               class="form-control @error('foto') is-invalid @enderror" 
                               id="foto" 
                               name="foto" 
                               accept="image/*"
                               onchange="previewImage(event)">
                        @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="preview" src="" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('mahasiswa.laporan.index') }}" class="btn btn-lg btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-lg btn-caretel-red">
                            <i class="fas fa-save me-2"></i> Update Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Report Info -->
        <div class="card border-0 shadow-sm mt-4 bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Report Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Created</small>
                        <p class="mb-2">{{ $laporan->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Last Updated</small>
                        <p class="mb-2">{{ $laporan->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush