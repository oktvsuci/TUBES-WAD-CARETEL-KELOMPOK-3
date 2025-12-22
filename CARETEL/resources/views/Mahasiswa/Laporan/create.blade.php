@extends('layouts.mahasiswa')

@section('title', 'Create New Report')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('mahasiswa.laporan.index') }}">My Reports</a></li>
<li class="breadcrumb-item active">Create Report</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Header Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="fw-bold mb-2">Create New Facility Report</h2>
                <p class="text-muted mb-0">Fill in the details below to report a facility issue</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('mahasiswa.laporan.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                    @csrf

                    <!-- Report Title -->
                    <div class="mb-4">
                        <label for="judul" class="form-label fw-semibold">
                            Report Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('judul') is-invalid @enderror" 
                               id="judul" 
                               name="judul" 
                               placeholder="e.g., Broken AC in Room 301"
                               value="{{ old('judul') }}"
                               required>
                        @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Be specific and concise</small>
                    </div>

                    <!-- Category -->
                                <select class="form-select form-select-lg @error('kategori_id') is-invalid @enderror" 
                                id="kategori_id" 
                                name="kategori_id" 
                                required>
                            <option value="">Select Category</option>
                            @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                            @endforeach
                        </select>
                                    @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                   placeholder="e.g., Building A, Room 301"
                                   value="{{ old('lokasi') }}"
                                   required>
                            @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Be as specific as possible (Building, Floor, Room)</small>
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
                                  placeholder="Describe the issue in detail. Include when you first noticed it, how it affects you, and any other relevant information..."
                                  required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between">
                            <small class="form-text text-muted">Minimum 20 characters</small>
                            <small class="form-text text-muted" id="charCount">0 characters</small>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Priority Level</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="prioritas" id="prioritas-low" value="rendah" {{ old('prioritas', 'sedang') == 'rendah' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success" for="prioritas-low">
                                <i class="fas fa-circle me-2"></i> Low
                            </label>

                            <input type="radio" class="btn-check" name="prioritas" id="prioritas-medium" value="sedang" {{ old('prioritas', 'sedang') == 'sedang' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="prioritas-medium">
                                <i class="fas fa-circle me-2"></i> Medium
                            </label>

                            <input type="radio" class="btn-check" name="prioritas" id="prioritas-high" value="tinggi" {{ old('prioritas') == 'tinggi' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger" for="prioritas-high">
                                <i class="fas fa-circle me-2"></i> High
                            </label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <strong>High:</strong> Urgent safety/security issues | 
                            <strong>Medium:</strong> Affects daily activities | 
                            <strong>Low:</strong> Minor inconveniences
                        </small>
                    </div>

                    <!-- Photo Upload -->
                    <div class="mb-4">
                        <label for="foto" class="form-label fw-semibold">
                            Upload Photo (Optional)
                        </label>
                        <div class="border rounded p-4 text-center bg-light">
                            <input type="file" 
                                   class="form-control @error('foto') is-invalid @enderror" 
                                   id="foto" 
                                   name="foto" 
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            @error('foto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="preview" src="" class="img-fluid rounded" style="max-height: 300px;">
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                            
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-1">Click to upload or drag and drop</p>
                                <small class="text-muted">PNG, JPG up to 5MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I confirm that the information provided is accurate and I understand that false reports may result in consequences.
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('mahasiswa.laporan.index') }}" class="btn btn-lg btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-lg btn-caretel-red">
                            <i class="fas fa-paper-plane me-2"></i> Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 shadow-sm mt-4 bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-lightbulb text-warning me-2"></i> Tips for Effective Reporting</h6>
                <ul class="mb-0 small">
                    <li>Be specific about the location (Building, Floor, Room number)</li>
                    <li>Describe the problem clearly and include when it started</li>
                    <li>Upload photos if possible - they help technicians understand the issue faster</li>
                    <li>Set appropriate priority level based on urgency</li>
                    <li>For emergency situations, call facility management directly: <strong>1500-835</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Character counter
document.getElementById('deskripsi').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('charCount').textContent = count + ' characters';
});

// Image preview
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('uploadPlaceholder').style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

// Remove image
function removeImage() {
    document.getElementById('foto').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'block';
}

// Form validation
document.getElementById('reportForm').addEventListener('submit', function(e) {
    const deskripsi = document.getElementById('deskripsi').value;
    if (deskripsi.length < 20) {
        e.preventDefault();
        alert('Description must be at least 20 characters long');
        return false;
    }
});
</script>
@endpush