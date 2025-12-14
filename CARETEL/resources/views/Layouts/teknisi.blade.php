@extends('layouts.app')

@section('body')
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top no-print">
    <div class="container-fluid px-4">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('teknisi.dashboard.index') }}">
            <div class="navbar-brand-logo">
                <i class="fas fa-building text-white"></i>
            </div>
            <div class="ms-2">
                <span class="fw-bold">CARETEL</span>
                <br>
                <small class="text-muted" style="font-size: 11px;">Technician Panel</small>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Menu -->
            <ul class="navbar-nav ms-auto me-4">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('teknisi/dashboard*') ? 'active text-caretel-red fw-semibold' : '' }}" 
                       href="{{ route('teknisi.dashboard.index') }}">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('teknisi/tugas*') ? 'active text-caretel-red fw-semibold' : '' }}" 
                       href="{{ route('teknisi.tugas.index') }}">
                        <i class="fas fa-tasks me-1"></i> My Tasks
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('teknisi/riwayat*') ? 'active text-caretel-red fw-semibold' : '' }}" 
                       href="{{ route('teknisi.riwayat.index') }}">
                        <i class="fas fa-history me-1"></i> History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-question-circle me-1"></i> Help
                    </a>
                </li>
            </ul>

            <!-- Right Menu -->
            <div class="d-flex align-items-center">
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-dark position-relative" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fs-5"></i>
                        @if(isset($newTasksCount) && $newTasksCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $newTasksCount }}
                        </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 320px;">
                        <li class="px-3 py-2 border-bottom">
                            <h6 class="mb-0 fw-semibold">New Assignments</h6>
                        </li>
                        <li>
                            <a class="dropdown-item py-3" href="{{ route('teknisi.tugas.index') }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-tasks text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0 small">{{ $newTasksCount ?? 0 }} new tasks assigned to you</p>
                                        <small class="text-muted">Check your task list</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="text-center">
                            <a class="dropdown-item text-caretel-red" href="{{ route('teknisi.tugas.index') }}">View all tasks</a>
                        </li>
                    </ul>
                </div>

                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark d-flex align-items-center text-decoration-none" 
                            data-bs-toggle="dropdown">
                        <div class="bg-caretel-red rounded-circle d-flex align-items-center justify-content-center me-2" 
                             style="width: 36px; height: 36px;">
                            <span class="text-white fw-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>
                        <span class="fw-semibold me-1">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 border-bottom">
                            <small class="text-muted d-block">Technician</small>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 no-print">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('teknisi.dashboard.index') }}" class="text-decoration-none">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Info!</strong> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Validation Error!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Page Content -->
    @yield('content')
</div>

<!-- Footer -->
<footer class="bg-white border-top py-4 mt-5 no-print">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <small class="text-muted">© 2024 CARETEL Technician Panel - Telkom University</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-muted">
                    <a href="#" class="text-decoration-none text-muted me-3">Support</a>
                    <a href="#" class="text-decoration-none text-muted me-3">Guidelines</a>
                    <a href="#" class="text-decoration-none text-muted">Contact Admin</a>
                </small>
            </div>
        </div>
    </div>
</footer>
@endsection