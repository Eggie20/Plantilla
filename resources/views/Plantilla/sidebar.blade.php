<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Municipality of Magallanes - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('auth.loginModal')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="{{ asset('css/plantilla.css') }}"> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Custom CSS for Plantilla System */
        :root {
            --primary-color: #4f4f4f;
            --primary-hover: #3d3d3d;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #0dcaf0;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --header-height: 60px;
        }
        
        /* Base styles */
        html, body {
            overflow-x: hidden;
            max-width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark-color) 0%, var(--primary-color) 100%);
            height: 100vh;
            position: fixed;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
        }
        
        .sidebar-brand img {
            height: 40px;
            width: auto;
            margin-right: 10px;
        }
        
        .sidebar-brand span {
            font-weight: 600;
            font-size: 1.1rem;
            white-space: nowrap;
        }
        
        .sidebar-toggle {
            color: white;
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }
        
        .sidebar-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu {
            padding: 1rem 0;
            overflow-y: auto;
            height: calc(100vh - var(--header-height) - 80px);
        }
        
        .sidebar-menu-category {
            padding: 0.5rem 1.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-top: 1rem;
        }
        
        .sidebar-menu-item {
            margin-bottom: 0.25rem;
        }
        
        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0.375rem;
            margin: 0 0.5rem;
            transition: all 0.2s;
        }
        
        .sidebar-menu-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar-menu-link.active {
            background-color: var(--secondary-color);
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-menu-icon {
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            font-size: 1.1rem;
        }
        
        .sidebar-menu-text {
            font-weight: 500;
            white-space: nowrap;
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-user {
            display: flex;
            align-items: center;
        }
        
        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .sidebar-user-info {
            overflow: hidden;
        }
        
        .sidebar-user-name {
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar-user-role {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
        }
        
        /* Main content area */
        .main-header {
            height: var(--header-height);
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
        }

        .logout-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            white-space: nowrap;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        .logout-button i {
            font-size: 1rem;
        }

        .logout-button span {
            font-size: 0.875rem;
        }

        /* Ensure proper width calculation with sidebar states */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .sidebar.collapsed ~ .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        @media (max-width: 992px) {
            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content {
                padding: 0.5rem;
            }
        }
        
        /* Collapsed sidebar */
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar.collapsed .sidebar-brand span,
        .sidebar.collapsed .sidebar-menu-text,
        .sidebar.collapsed .sidebar-menu-category,
        .sidebar.collapsed .sidebar-user-info {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-menu-link {
            justify-content: center;
            padding: 0.75rem;
        }
        
        .sidebar.collapsed .sidebar-menu-icon {
            margin-right: 0;
        }
        
        .sidebar.collapsed .sidebar-user {
            justify-content: center;
        }
        
        .sidebar.collapsed .sidebar-user-avatar {
            margin-right: 0;
        }
        
        .sidebar.collapsed ~ .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Search container and other styles from original */
        .search-container {
            max-width: 400px;
            margin-left: auto;
        }
        
        .search-wrapper {
            position: relative;
        }
        
        .search-box {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
        }
        
        .search-icon {
            color: #6b7280;
            margin-right: 0.5rem;
        }
        
        .search-input {
            border: none;
            outline: none;
            width: 100%;
            background: transparent;
        }
        
        .search-input:focus {
            box-shadow: none;
        }
        
        /* Table and card styles */
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border: none;
        }
        
        .card-header {
            border-bottom: 1px solid #e5e7eb;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .table td {
            vertical-align: middle;
            font-size: 0.875rem;
        }
        
        .btn-group .btn {
            border-radius: 6px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .alert-container {
            margin-bottom: 1rem;
        }
        
        .alert {
            border-radius: 8px;
            padding: 0.75rem;
        }
        
        /* Prevent horizontal scrolling */
        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }
        
        /* Ensure tables fit within container */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        .logout-button i {
            font-size: 1rem;
        }

        .logout-button span {
            font-size: 0.875rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="d-flex">
       <!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('index') }}" class="sidebar-brand">
            <span>Plantilla System</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="sidebar-menu">
        <div class="sidebar-menu-category">Main</div>
        <ul class="list-unstyled">
            <li class="sidebar-menu-item">
                <a href="{{ route('index') }}" class="sidebar-menu-link {{ request()->routeIs('index') ? 'active' : '' }}">
                    <div class="sidebar-menu-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <span class="sidebar-menu-text">Dashboard</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-menu-category">Personnel</div>
        <ul class="list-unstyled">
            <li class="sidebar-menu-item">
                <a href="{{ route('Plantilla.Pages.vacant') }}" class="sidebar-menu-link {{ request()->routeIs('Plantilla.Pages.vacant') ? 'active' : '' }}">
                    <div class="sidebar-menu-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <span class="sidebar-menu-text">Vacant Positions</span>
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('Plantilla.Pages.YearsOfService') }}" class="sidebar-menu-link {{ request()->routeIs('Plantilla.Pages.YearsOfService') ? 'active' : '' }}">
                    <div class="sidebar-menu-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="sidebar-menu-text">Years of Service</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-menu-category">Documents</div>
        <ul class="list-unstyled">
<!-- <li class="sidebar-menu-item">
    <a href="#" class="sidebar-menu-link">
        <div class="sidebar-menu-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <span class="sidebar-menu-text">NOSA</span>
    </a>
</li> -->
            <li class="sidebar-menu-item">
                <a href="{{ route('Plantilla.Pages.Remarks') }}" class="sidebar-menu-link {{ request()->routeIs('Plantilla.Pages.Remarks') ? 'active' : '' }}">
                    <div class="sidebar-menu-icon">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <span class="sidebar-menu-text">Remarks</span>
                </a>
            </li>
        </ul>
        
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
        <div class="sidebar-menu-category">Administration</div>
        <ul class="list-unstyled">
            <li class="sidebar-menu-item">
                <a href="{{ route('Plantilla.Pages.Accounts') }}" class="sidebar-menu-link {{ request()->routeIs('Plantilla.Pages.Accounts') ? 'active' : '' }}">
                    <div class="sidebar-menu-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <span class="sidebar-menu-text">User Accounts</span>
                </a>
            </li>
            @if(Auth::user()->role === 'superadmin')
                <!-- Auth Logs Link -->
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.authlogs.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.authlogs.index') ? 'active' : '' }}">
                        <div class="sidebar-menu-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <span class="sidebar-menu-text">Auth Logs</span>
                    </a>
                </li>
                <!-- Activity Log Link -->
                <li class="sidebar-menu-item">
                    <a href="{{ route('activity.log') }}" class="sidebar-menu-link {{ request()->routeIs('activity.log') ? 'active' : '' }}">
                        <div class="sidebar-menu-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <span class="sidebar-menu-text">Activity Log</span>
                    </a>
                </li>
            @endif
        </ul>
        @endif
    </div>

    
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
        </div>
    </div>
</aside>


        <!-- Main Content Area -->
        <div class="main-wrapper" id="mainWrapper">
            <!-- Header -->
            <header class="main-header">
                <h1 class="header-title">
                    @yield('title', 'Dashboard')
                </h1>
                <div class="header-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content -->
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Include modals -->
    @stack('modals')

    <!-- Scripts -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            // Check for saved sidebar state
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            // Apply initial state
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
            }
            
            // Toggle sidebar on button click
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });
            
            // Handle mobile view
            const mobileToggle = document.getElementById('mobileToggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const windowWidth = window.innerWidth;
                if (windowWidth < 992 && !sidebar.contains(event.target) && !mobileToggle?.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>