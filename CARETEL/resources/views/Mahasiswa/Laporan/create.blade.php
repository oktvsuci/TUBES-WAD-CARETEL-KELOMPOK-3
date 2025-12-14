@extends('layouts.mahasiswa')

@section('title', 'Submit a Facility Report')

@section('breadcrumb')
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li><a href="{{ route('mahasiswa.laporan.index') }}" class="hover:text-caretel-red">Reports</a></li>
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li class="caretel-red">Create New Report</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Submit a Facility Report</h1>
        <p class="text-gray-600 mt-1">Help us maintain campus facilities by reporting issues you encounter.</p>
    </div>

    <!-- Form -->
    <form action="{{ route('mahasiswa.laporan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <!-- Step 1: Report Details -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="bg-caretel-red text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">1</div>
                    <h2 class="text-lg font-bold text-gray-900">Report Details</h2>
                </div>

                <!-- Report Title -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        Report Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('judul') border-red-500 @enderror" 
                        placeholder="e.g., Broken AC in Room 301"
                        value="{{ old('judul') }}" required>
                    @error('judul')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        Location <span class="text-red-500">*</span>
                    </label>
                    <select name="lokasi" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('lokasi') border-red-500 @enderror" required>
                        <option value="">Select building and location...</option>
                        <option value="TULT Building - Floor 1">TULT Building - Floor 1</option>
                        <option value="TULT Building - Floor 2">TULT Building - Floor 2</option>
                        <option value="Open Library - 2nd Floor">Open Library - 2nd Floor</option>
                        <option value="Dormitory A - Living 1">Dormitory A - Living 1</option>
                        <option value="GKU Building - East">GKU Building - East</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('lokasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Step 2: Evidence & Description -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="bg-caretel-red text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">2</div>
                    <h2 class="text-lg font-bold text-gray-900">Evidence & Description</h2>
                </div>

                <!-- Photo Upload -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        Photo <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-caretel-red transition">
                        <input type="file" name="foto" id="foto" class="hidden" accept="image/*" required onchange="previewImage(event)">
                        <label for="foto" class="cursor-pointer">
                            <div id="preview-container" class="hidden mb-4">
                                <img id="preview" class="mx-auto max-h-48 rounded">
                            </div>
                            <div id="upload-prompt">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 mb-1">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>
                        </label>
                    </div>
                    @error('foto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" rows="5" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('deskripsi') border-red-500 @enderror" 
                        placeholder="Please describe the issue in detail..."
                        required>{{ old('deskripsi') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Provide as much detail as possible to help us resolve the issue quickly.</p>
                    @error('deskripsi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Step 3: Priority Level -->
            <div class="mb-6">
                <div class="flex items-center mb-6">
                    <div class="bg-caretel-red text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">3</div>
                    <h2 class="text-lg font-bold text-gray-900">Priority Level</h2>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="prioritas" value="low" class="hidden peer" checked>
                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                            <i class="fas fa-circle text-green-500 text-2xl mb-2"></i>
                            <p class="font-semibold text-gray-900">Low</p>
                            <p class="text-xs text-gray-500">Not urgent</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="prioritas" value="medium" class="hidden peer">
                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:border-yellow-300">
                            <i class="fas fa-circle text-yellow-500 text-2xl mb-2"></i>
                            <p class="font-semibold text-gray-900">Medium</p>
                            <p class="text-xs text-gray-500">Needs attention</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="prioritas" value="high" class="hidden peer">
                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300">
                            <i class="fas fa-circle text-red-500 text-2xl mb-2"></i>
                            <p class="font-semibold text-gray-900">High</p>
                            <p class="text-xs text-gray-500">Urgent</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('mahasiswa.laporan.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-caretel-red hover:bg-caretel-red-dark text-white rounded-lg font-semibold">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Report
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview-container').classList.remove('hidden');
            document.getElementById('upload-prompt').classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection