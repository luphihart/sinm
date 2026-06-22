<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SINM') - {{ \App\Models\Setting::get('app_name', 'Sistem Informasi Nilai Murid') }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    
    <!-- Google Fonts (Outfit) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome (Icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --font-family: 'Outfit', sans-serif;
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --accent-color: #0d9488;
            --bg-light: #f8fafc;
            --card-light: #ffffff;
            --sidebar-light: #ffffff;
            --text-light: #1e293b;
            
            --bg-dark: #0f172a;
            --card-dark: #1e293b;
            --sidebar-dark: #1e293b;
            --text-dark: #f8fafc;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-light);
            color: var(--text-light);
            transition: background-color 0.3s, color 0.3s;
            min-height: 100vh;
        }

        [data-bs-theme="dark"] body {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        /* Glassmorphism card styles */
        .glass-card {
            background: var(--card-light);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        [data-bs-theme="dark"] .glass-card {
            background: var(--card-dark);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        [data-bs-theme="dark"] .glass-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Sidebar custom styles */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-light);
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
        }

        [data-bs-theme="dark"] .sidebar {
            background-color: var(--sidebar-dark);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Sidebar Backdrop */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 90;
            transition: all 0.3s ease;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        @media (max-width: 767.98px) {
            .main-content {
                padding: 15px;
            }
            .glass-card.p-4 {
                padding: 1rem !important;
            }
            .glass-card.p-5 {
                padding: 1.25rem !important;
            }
            .navbar h4 {
                font-size: 1.15rem !important;
            }
            .table th, .table td {
                padding: 10px 8px !important;
                font-size: 0.875rem !important;
            }
            .nav-tabs .nav-link {
                padding: 8px 12px !important;
                font-size: 0.875rem !important;
            }
            .nav-pills .nav-link {
                padding: 6px 12px !important;
                font-size: 0.8rem !important;
            }
        }

        .sidebar-brand {
            padding: 24px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        [data-bs-theme="dark"] .sidebar-brand {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: #64748b;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--primary-color);
            background-color: rgba(37, 99, 235, 0.05);
            border-left-color: var(--primary-color);
        }

        [data-bs-theme="dark"] .nav-link-custom {
            color: #94a3b8;
        }

        [data-bs-theme="dark"] .nav-link-custom:hover, 
        [data-bs-theme="dark"] .nav-link-custom.active {
            color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
        }

        /* Buttons & Badges */
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            border-radius: 10px;
            font-weight: 500;
            padding: 8px 20px;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .theme-toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .theme-toggle-btn:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        [data-bs-theme="dark"] .theme-toggle-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Table custom styling */
        .table-custom-container {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #94a3b8;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop d-none" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center justify-content-between">
            <span class="d-flex align-items-center">
                <i class="fa-solid fa-graduation-cap me-2 fs-4"></i> {{ \App\Models\Setting::get('app_name', 'SINM') }}
            </span>
            <button class="btn d-lg-none" onclick="toggleSidebar()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="py-4">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line me-3"></i> Dashboard
                </a>
                <a href="{{ route('admin.jurusan.index') }}" class="nav-link-custom {{ Route::is('admin.jurusan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-network-wired me-3"></i> Data Jurusan
                </a>
                <a href="{{ route('admin.kelas.index') }}" class="nav-link-custom {{ Route::is('admin.kelas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-school me-3"></i> Data Kelas
                </a>
                <a href="{{ route('admin.murid.index') }}" class="nav-link-custom {{ Route::is('admin.murid.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-viewfinder me-3"></i> Data Murid
                </a>
                <a href="{{ route('admin.mapel.index') }}" class="nav-link-custom {{ Route::is('admin.mapel.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-book me-3"></i> Mata Pelajaran
                </a>
                <a href="{{ route('admin.semester.index') }}" class="nav-link-custom {{ Route::is('admin.semester.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-days me-3"></i> Semester
                </a>
                <a href="{{ route('admin.nilai.index') }}" class="nav-link-custom {{ Route::is('admin.nilai.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-list me-3"></i> Data Nilai & Ranking
                </a>
                <a href="{{ route('admin.snbp.index') }}" class="nav-link-custom {{ Route::is('admin.snbp.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-award me-3"></i> Seleksi SNBP
                </a>
                <a href="{{ route('admin.setting.index') }}" class="nav-link-custom {{ Route::is('admin.setting.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear me-3"></i> Pengaturan
                </a>
            @else
                <a href="{{ route('murid.dashboard') }}" class="nav-link-custom {{ Route::is('murid.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line me-3"></i> Dashboard Rapor
                </a>
                @if(\App\Models\Setting::get('snbp_menu_status', 'nonaktif') === 'aktif')
                    <a href="{{ route('murid.snbp.index') }}" class="nav-link-custom {{ Route::is('murid.snbp.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-award me-3"></i> Seleksi SNBP
                    </a>
                @endif
            @endif
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg mb-4 p-0">
            <div class="container-fluid p-0">
                <button class="btn d-lg-none me-3" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars fs-4"></i>
                </button>
                <h4 class="m-0 fw-700">@yield('page_title', 'Sistem Informasi Nilai Murid')</h4>
                
                <div class="ms-auto d-flex align-items-center">
                    <!-- Light/Dark Toggle -->
                    <button class="theme-toggle-btn me-3" id="themeToggle" onclick="toggleTheme()">
                        <i class="fa-solid fa-moon text-secondary" id="themeIcon"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn border-0 d-flex align-items-center dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: 600;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-start d-none d-sm-block">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ ucfirst(Auth::user()->role) }}</small>
                                <span class="fw-600" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end glass-card mt-2">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="text-center py-4 mt-5 border-top text-muted small">
            {{ \App\Models\Setting::get('footer_text', '© ' . date('Y') . ' SINM. All Rights Reserved.') }}
        </footer>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (Required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Sidebar Toggler for Mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarBackdrop').classList.toggle('d-none');
        }

        // Theme Toggle (Dark / Light)
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('themeIcon');
            
            if (html.getAttribute('data-bs-theme') === 'light') {
                html.setAttribute('data-bs-theme', 'dark');
                icon.className = 'fa-solid fa-sun text-warning';
                localStorage.setItem('theme', 'dark');
            } else {
                html.setAttribute('data-bs-theme', 'light');
                icon.className = 'fa-solid fa-moon text-secondary';
                localStorage.setItem('theme', 'light');
            }
        }

        // Load cached theme preference
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            const icon = document.getElementById('themeIcon');
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                if (icon) icon.className = 'fa-solid fa-sun text-warning';
            }
            
            // Initialize DataTables
            $('.datatable-custom').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                responsive: true
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
