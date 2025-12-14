<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CARETEL') - Telkom University</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --caretel-red: #E30613;
            --caretel-red-dark: #C00510;
            --caretel-red-light: #FF2D3A;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .bg-caretel-red {
            background-color: var(--caretel-red) !important;
        }
        
        .text-caretel-red {
            color: var(--caretel-red) !important;
        }
        
        .btn-caretel-red {
            background-color: var(--caretel-red);
            border-color: var(--caretel-red);
            color: white;
        }
        
        .btn-caretel-red:hover {
            background-color: var(--caretel-red-dark);
            border-color: var(--caretel-red-dark);
            color: white;
        }
        
        .border-caretel-red {
            border-color: var(--caretel-red) !important;
        }
        
        .navbar-brand-logo {
            width: 40px;
            height: 40px;
            background: var(--caretel-red);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-diproses {
            background-color: #cfe2ff;
            color: #084298;
        }
        
        .status-selesai {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-ditolak {
            background-color: #f8d7da;
            color: #842029;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        
        .sidebar .nav-link {
            color: #6c757d;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: var(--caretel-red);
            background-color: #fff5f5;
            border-left-color: var(--caretel-red);
        }
        
        .card-hover:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @yield('body')
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>