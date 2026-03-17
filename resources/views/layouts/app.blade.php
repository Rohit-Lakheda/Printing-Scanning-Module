<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Badge Printing System')</title>
    
    <!-- Google Fonts - Comfortaa -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Comfortaa', sans-serif;
            background-color: #ffffff;
            color: #1a1a1a;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            font-family: 'Comfortaa', sans-serif;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background-color: #60a5fa;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #3b82f6;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .card-header {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 24px;
            font-weight: 600;
            color: #1e40af;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }

        .form-label .tooltip {
            position: relative;
            display: inline-block;
            margin-left: 5px;
            cursor: help;
        }

        .form-label .tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1e40af;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            white-space: nowrap;
            font-size: 12px;
            z-index: 1000;
            margin-bottom: 5px;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Comfortaa', sans-serif;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .form-check-label {
            cursor: pointer;
            font-size: 14px;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: 600;
        }

        .table tr:hover {
            background-color: #f9fafb;
        }

        /* Alerts */
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Navigation */
        .navbar {
            background-color: #1e40af;
            padding: 16px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            color: white;
            font-size: 20px;
            font-weight: 600;
            text-decoration: none;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            background: transparent;
            border: none;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* Event Logo */
        .event-logo-container {
            background-color: #ffffff;
            padding: 15px 0;
            border-bottom: 2px solid #e5e7eb;
            text-align: center;
        }

        .event-logo-container img {
            max-height: 80px;
            max-width: 300px;
            object-fit: contain;
        }

        .navbar-nav {
            display: flex;
            gap: 20px;
            list-style: none;
            align-items: center;
        }

        .navbar-nav a {
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            white-space: nowrap;
            font-size: 14px;
        }

        .navbar-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-nav button {
            white-space: nowrap;
        }

        /* Dropdown Styles */
        .navbar-nav li {
            position: relative;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            cursor: pointer;
        }

        .dropdown-toggle::after {
            content: ' ▼';
            font-size: 10px;
            margin-left: 4px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #1e40af;
            min-width: 200px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            margin-top: 4px;
            z-index: 1000;
            list-style: none;
            padding: 8px 0;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown.active .dropdown-menu {
            display: block;
        }

        .dropdown-menu li {
            margin: 0;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 22px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* Badge Preview */
        .badge-preview {
            position: relative;
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 12px;
            margin: 20px 0;
        }

        .badge-element {
            position: absolute;
            cursor: move;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px dashed #3b82f6;
        }

        .badge-element:hover {
            background-color: rgba(59, 130, 246, 0.2);
        }

        /* Responsive Tables */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive table {
            min-width: 600px;
        }

        /* Mobile Card View for Tables */
        .table-card {
            display: none;
        }

        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .badge-print, .badge-print * {
                visibility: visible;
            }
            .badge-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }

        /* Mobile / medium screen responsive styles */
        @media screen and (max-width: 1165px) {
            .container {
                padding: 10px;
            }

            .card {
                padding: 16px;
                margin-bottom: 15px;
            }

            .card-title {
                font-size: 20px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .card-header h1 {
                margin-bottom: 0;
            }

            /* Hamburger Menu */
            .hamburger {
                display: flex;
            }

            .navbar-nav {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 60px);
                background-color: #1e40af;
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
                gap: 0;
                transition: left 0.3s ease;
                z-index: 999;
                overflow-y: auto;
            }

            .navbar-nav.active {
                left: 0;
            }

            .navbar-nav li {
                width: 100%;
                margin-bottom: 10px;
            }

            .navbar-nav a {
                display: block;
                width: 100%;
                padding: 12px 16px;
            }

            .dropdown {
                width: 100%;
            }

            .dropdown-menu {
                position: static;
                display: none;
                width: 100%;
                background-color: rgba(255, 255, 255, 0.1);
                box-shadow: none;
                margin-top: 0;
                margin-left: 20px;
            }

            .dropdown.active .dropdown-menu {
                display: block;
            }

            .dropdown-toggle::after {
                float: right;
            }

            /* Tables - keep standard responsive tables visible */
            .table-responsive {
                display: block;
            }

            .table-card {
                display: none;
            }

            .table-card-item {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 15px;
            }

            .table-card-item .card-row {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .table-card-item .card-row:last-child {
                border-bottom: none;
            }

            .table-card-item .card-label {
                font-weight: 600;
                color: #374151;
            }

            .table-card-item .card-value {
                color: #6b7280;
            }

            .table-card-item .card-actions {
                margin-top: 10px;
                display: flex;
                gap: 10px;
            }

            /* Buttons */
            .btn {
                padding: 10px 20px;
                font-size: 14px;
                width: 100%;
                text-align: center;
            }

            .btn-group {
                flex-direction: column;
                gap: 10px;
            }

            .btn-group .btn {
                width: 100%;
            }

            /* Forms */
            .form-group {
                margin-bottom: 15px;
            }

            .form-control {
                padding: 10px 14px;
                font-size: 16px; /* Prevents zoom on iOS */
            }

            /* Event Logo */
            .event-logo-container img {
                max-height: 60px;
                max-width: 200px;
            }

            /* Badge Preview */
            .badge-preview {
                margin: 15px 0;
            }
        }

        /* Lead settings grids */
        .lead-settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            align-items: flex-start;
        }

        .lead-fields-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 8px 16px;
        }

        .lead-field-checkbox {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .lead-field-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 8px;
            }

            .card {
                padding: 12px;
            }

            .card-title {
                font-size: 18px;
            }

            .navbar-brand {
                font-size: 16px;
            }

            .btn {
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @php
        use Illuminate\Support\Facades\Storage;
        $eventSettings = \App\Models\EventSetting::getSettings();
        $isAdminPage = auth()->check() && auth()->user()->isAdmin() && !request()->is('operator*') && !request()->is('lead/*');
        // Show logo on operator pages only, not on admin pages (including event logo page)
        $showLogo = $eventSettings->logo_path && !$isAdminPage;
    @endphp
    
    @if($showLogo)
        <div class="event-logo-container">
            <img src="{{ Storage::url($eventSettings->logo_path) }}" alt="Event Logo">
        </div>
    @endif

    @if($isAdminPage)
    <nav class="navbar">
        <div class="navbar-content">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand">Badge System</a>
            <button class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="navbar-nav" id="navbar-nav">
                <li><a href="{{ route('admin.dashboard') }}" onclick="closeMobileMenu()">Dashboard</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Event</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.event-logo.index') }}" onclick="closeMobileMenu()">Event Logo</a></li>
                        <li><a href="{{ route('admin.import-data.index') }}" onclick="closeMobileMenu()">Import Data</a></li>
                        <li><a href="{{ route('admin.api-configurations.index') }}" onclick="closeMobileMenu()">Post Data API Configuration</a></li>
                        <li><a href="{{ route('admin.get-data-api-configurations.index') }}" onclick="closeMobileMenu()">Get Data API Configuration</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Categories</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.categories.index') }}" onclick="closeMobileMenu()">Manage Categories</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Printing</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.badge-layout.edit', optional(\App\Models\Category::first())->Category ?? 'default') }}" onclick="closeMobileMenu()">Layout Editor</a></li>
                        <li><a href="{{ route('admin.unique-print.index') }}" onclick="closeMobileMenu()">Unique Print Settings</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Scanning</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.scanning.type.edit') }}" onclick="closeMobileMenu()">Scanning Type</a></li>
                        <li><a href="{{ route('admin.locations.index') }}" onclick="closeMobileMenu()">Locations</a></li>
                        <li><a href="{{ route('admin.blocked-regids.index') }}" onclick="closeMobileMenu()">Blocked RegIDs</a></li>
                        <li><a href="{{ route('admin.master-regids.index') }}" onclick="closeMobileMenu()">Master RegIDs</a></li>
                        <li><a href="{{ route('admin.bypassed-regids.index') }}" onclick="closeMobileMenu()">Bypassed RegIDs</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleDropdown(event, this)">Lead Generation</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.leads.settings') }}" onclick="closeMobileMenu()">Lead &amp; Mail Settings</a></li>
                        <li><a href="{{ route('admin.leads.share') }}" onclick="closeMobileMenu()">Lead Sharing &amp; Export</a></li>
                    </ul>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: red; border: none; color: white; cursor: pointer; padding: 8px 16px; border-radius: 6px; font-family: 'Comfortaa', sans-serif; width: 100%; text-align: left;">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
    @endif

    <script>
        function toggleMobileMenu() {
            const nav = document.getElementById('navbar-nav');
            const hamburger = document.getElementById('hamburger');
            nav.classList.toggle('active');
            hamburger.classList.toggle('active');
        }

        function closeMobileMenu() {
            const nav = document.getElementById('navbar-nav');
            const hamburger = document.getElementById('hamburger');
            nav.classList.remove('active');
            hamburger.classList.remove('active');
        }

        function toggleDropdown(event, element) {
            if (window.innerWidth <= 1165) {
                event.preventDefault();
                const dropdown = element.closest('.dropdown');
                dropdown.classList.toggle('active');
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('navbar-nav');
            const hamburger = document.getElementById('hamburger');
            if (window.innerWidth <= 1165 && nav && !nav.contains(event.target) && !hamburger.contains(event.target)) {
                closeMobileMenu();
            }
        });
    </script>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
