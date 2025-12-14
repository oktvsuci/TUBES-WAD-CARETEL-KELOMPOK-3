@extends('layouts.app')

@section('title', 'Sign In')

@section('body')
<div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Left Side - Login Form -->
        <div class="col-md-5 d-flex align-items-center justify-content-center bg-white">
            <div class="w-100 px-5" style="max-width: 480px;">
                <!-- Logo -->
                <div class="mb-5">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-caretel-red rounded d-flex align-items-center justify-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-building text-white fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0 fw-bold">CARETEL</h4>
                            <small class="text-muted">Telkom University</small>
                        </div>
                    </div>
                </div>

                <!-- Welcome Text -->
                <div class="mb-4">
                    <h2 class="fw-bold mb-2">Welcome back</h2>
                    <p class="text-muted">Sign in to report and track campus facility issues.</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Error!</strong> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Username/Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Username or Email</label>
                        <input type="text" 
                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               placeholder="e.g. name@telkomuniversity.ac.id"
                               value="{{ old('email') }}"
                               required 
                               autofocus>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember for 30 days
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-caretel-red text-decoration-none">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="btn btn-caretel-red btn-lg w-100 mb-3">
                        Sign in
                    </button>

                    <!-- Register Link -->
                    <div class="text-center">
                        <span class="text-muted">Don't have an account?</span>
                        <a href="{{ route('register') }}" class="text-caretel-red text-decoration-none fw-semibold ms-1">
                            Register here
                        </a>
                    </div>
                </form>

                <!-- Footer -->
                <div class="text-center mt-5">
                    <small class="text-muted">© 2024 Telkom University. All rights reserved.</small>
                </div>
            </div>
        </div>

        <!-- Right Side - Image & Info -->
        <div class="col-md-7 d-none d-md-flex align-items-end justify-content-center position-relative" 
             style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
            <!-- Background Image -->
            <img src="{{ asset('images/campus-building.jpg') }}" 
                 alt="Campus Building" 
                 class="position-absolute w-100 h-100 object-fit-cover opacity-50"
                 onerror="this.style.display='none'">
            
            <!-- Content Overlay -->
            <div class="position-relative text-white p-5 mb-5" style="z-index: 2; max-width: 600px;">
                <div class="bg-white bg-opacity-10 backdrop-blur rounded-3 p-4 mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-building fs-4 me-2"></i>
                        <span class="badge bg-light text-dark">Campus Facilities</span>
                    </div>
                    <h1 class="display-5 fw-bold mb-3">Efficient Reporting, Better Campus.</h1>
                    <p class="lead mb-0">Join the community in maintaining a world-class learning environment at Telkom University.</p>
                </div>

                <!-- Features -->
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 backdrop-blur rounded-3 p-3">
                            <i class="fas fa-clock fs-4 mb-2"></i>
                            <h6 class="fw-semibold mb-1">Real-time Tracking</h6>
                            <small>Monitor report progress</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 backdrop-blur rounded-3 p-3">
                            <i class="fas fa-shield-alt fs-4 mb-2"></i>
                            <h6 class="fw-semibold mb-1">Secure & Private</h6>
                            <small>Your data is protected</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle Password Visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
</script>
@endpush
@endsection