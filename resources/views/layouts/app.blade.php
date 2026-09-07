<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>OLT Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('olt_index/images/fevicon.png') }}" type="image/png" />

    <!-- Bootstrap 5 / FontAwesome / Fonts -->
    <link rel="stylesheet" href="{{ asset('olt_index/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="{{ asset('olt_index/js/jquery-3.7.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('olt_index/css/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('olt_index/css/perfect-scrollbar.css') }}" />

    <style>
        :root {
            --sidebar-w: 260px;
            --sidebar-collapsed-w: 76px;
            --topbar-h: 64px;
            --radius: 12px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);

            /* Light Theme */
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-alt: #f1f5f9;
            --border: #e2e8f0;
            --text: #0f172a;
            --text-muted: #64748b;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --primary-soft: #eff6ff;
            --success: #22c55e;
            --danger: #ef4444;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        [data-theme="dark"] {
            --bg: #0f172a;
            --surface: #1e293b;
            --surface-alt: #334155;
            --border: #334155;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #3b82f6;
            --primary-hover: #60a5fa;
            --primary-soft: rgba(59, 130, 246, 0.15);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.3);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.5);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            transition: var(--transition);
            overflow-x: hidden;
        }

        /* ===== Layout Structure ===== */
        .full_container { display: flex; min-height: 100vh; }

        #sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1040;
            transition: var(--transition);
        }

        #content {
            flex: 1;
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* ===== Desktop Collapsed Sidebar ===== */
        body.sidebar-collapsed #sidebar {
            width: var(--sidebar-collapsed-w);
            min-width: var(--sidebar-collapsed-w);
        }

        body.sidebar-collapsed #content {
            margin-left: var(--sidebar-collapsed-w);
        }

        body.sidebar-collapsed .brand span,
        body.sidebar-collapsed .user_info,
        body.sidebar-collapsed #sidebar ul li a span {
            display: none;
        }

        body.sidebar-collapsed .sidebar-header,
        body.sidebar-collapsed .sidebar_user_info,
        body.sidebar-collapsed #sidebar ul li a {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        /* ===== Header & Brand ===== */
        .sidebar-header {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 16px;
            color: var(--text);
        }

        .brand i {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            background: var(--primary);
            color: #fff;
            border-radius: 10px;
            font-size: 16px;
        }

        /* ===== User Info Panel ===== */
        .sidebar_user_info {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .user_profle_side { display: flex; align-items: center; gap: 12px; }

        .user_info h6 { margin: 0; font-size: 14px; font-weight: 600; color: var(--text); }
        .user_info p {
            margin: 2px 0 0;
            font-size: 12px;
            color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
        }

        .online_animation {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--success);
            display: inline-block;
            box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
            70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* ===== Navigation ===== */
        .sidebar_blog_2 {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
            position: relative;
        }

        #sidebar ul { list-style: none; margin: 0; padding: 0; }

        #sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            margin-bottom: 4px;
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
        }

        #sidebar ul li a:hover {
            background: var(--surface-alt);
            color: var(--text);
        }

        #sidebar ul li a.active,
        #sidebar ul li.active > a {
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 600;
        }

        /* ===== Topbar & Dropdown Fixes ===== */
        .topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .topbar .navbar { height: 100%; padding: 0 24px; }
        .topbar-wrapper { display: flex; align-items: center; justify-content: space-between; width: 100%; }

        .sidebar_toggle {
            border: none;
            background: var(--surface-alt);
            color: var(--text);
            width: 38px; height: 38px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }
        .sidebar_toggle:hover { background: var(--primary-soft); color: var(--primary); }

        .right_topbar { display: flex; align-items: center; gap: 12px; }

        .theme-toggle {
            border: none;
            background: var(--surface-alt);
            width: 38px; height: 38px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text);
            cursor: pointer;
            transition: var(--transition);
        }
        .theme-toggle:hover { background: var(--primary-soft); color: var(--primary); }

        /* FIXED RIGHT ALIGNMENT FOR USER DROPDOWN */
        .user_profile_dd { list-style: none; margin: 0; padding: 0; position: relative; }

        .user_profile_dd .dropdown-toggle {
            display: flex; align-items: center; gap: 10px;
            padding: 4px 12px 4px 4px;
            border-radius: 30px;
            background: var(--surface-alt);
            color: var(--text);
            text-decoration: none;
        }

        .user_profile_dd .dropdown-toggle img {
            width: 32px; height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user_profile_dd .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0 !important;
            left: auto !important;
            margin-top: 8px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 6px;
            min-width: 200px;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13.5px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }

        .dropdown-item:hover { background: var(--primary-soft); color: var(--primary); }
        .dropdown-item.text-danger:hover { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        .dropdown-menu form { margin: 0; }
        .dropdown-menu form button {
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
        }

        /* ===== Page Content Area ===== */
        .midde_cont { flex: 1; padding: 24px; }

        .midde_cont .card,
        .midde_cont .table-responsive,
        .midde_cont .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .footer {
            padding: 16px 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            background: var(--surface);
        }

        /* ===== Mobile Overlay & Responsiveness ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(2px);
            z-index: 1035;
        }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 991px) {
            #sidebar {
                transform: translateX(-100%);
                box-shadow: var(--shadow-lg);
            }
            #sidebar.show { transform: translateX(0); }
            #content { margin-left: 0 !important; }
        }

        @media (max-width: 576px) {
            .midde_cont { padding: 16px; }
            .topbar .navbar { padding: 0 16px; }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('olt_index/css/custom.css') }}" />
</head>

<body class="dashboard dashboard_1">
    <div class="full_container">

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigation -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <i class="fa-solid fa-tower-broadcast"></i>
                    <span>OLT Manager</span>
                </div>
            </div>

            <div class="sidebar_user_info">
                <div class="user_profle_side">
                    <div class="user_info">
                        <h6>{{ Auth::user()->name }}</h6>
                        <p><span class="online_animation"></span> Active Now</p>
                    </div>
                </div>
            </div>

            <div class="sidebar_blog_2" id="sidebarScroll">
                <x-sidebar />
            </div>
        </nav>

        <!-- Main Content Shell -->
        <div id="content">

            <!-- Topbar Navigation -->
            <div class="topbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="topbar-wrapper">
                        <div class="topbar-left">
                            <button type="button" id="sidebarCollapse" class="sidebar_toggle" aria-label="Toggle Navigation">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                        </div>

                        <div class="right_topbar">
                            <button type="button" class="theme-toggle" id="themeToggle" title="Toggle Theme" aria-label="Toggle Theme">
                                <i class="fa-solid fa-moon" id="themeIcon"></i>
                            </button>

                            <ul class="user_profile_dd">
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-bs-toggle="dropdown" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                                        <img src="{{ asset('olt_index/images/layout_img/user2.png') }}" alt="User Profile" />
                                        <span class="name_user d-none d-md-inline font-weight-bold">{{ Auth::user()->name }}</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        @can('has-permission', '1,6')
                                        <a class="dropdown-item" href="profile.html">
                                            <i class="fa-solid fa-user"></i> My Profile
                                        </a>
                                        @endcan
                                        <a class="dropdown-item" href="settings.html">
                                            <i class="fa-solid fa-gear"></i> Settings
                                        </a>
                                        <a class="dropdown-item" href="help.html">
                                            <i class="fa-solid fa-circle-question"></i> Help
                                        </a>
                                        <div class="dropdown-divider my-1"></div>
                                        <form action="{{ route('logout') }}" method="post">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Page Body Content -->
            <div class="midde_cont">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                &copy; {{ date('Y') }} OLT Manager. All rights reserved.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('olt_index/js/popper.min.js') }}"></script>
    <script src="{{ asset('olt_index/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('olt_index/js/perfect-scrollbar.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Safe PerfectScrollbar initialization
            const sidebarContainer = document.querySelector('#sidebarScroll');
            if (sidebarContainer && typeof PerfectScrollbar !== 'undefined') {
                new PerfectScrollbar(sidebarContainer);
            }

            // Theme Management
            const root = document.documentElement;
            const toggleBtn = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');

            function applyTheme(theme) {
                root.setAttribute('data-theme', theme);
                themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                localStorage.setItem('olt-theme', theme);
            }

            const savedTheme = localStorage.getItem('olt-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(savedTheme || (prefersDark ? 'dark' : 'light'));

            toggleBtn.addEventListener('click', function () {
                const currentTheme = root.getAttribute('data-theme');
                applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });

            // Sidebar Layout Logic
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleCollapse = document.getElementById('sidebarCollapse');

            function isMobile() {
                return window.innerWidth <= 991;
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }

            toggleCollapse.addEventListener('click', function () {
                if (isMobile()) {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                }
            });

            overlay.addEventListener('click', closeMobileSidebar);

            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    closeMobileSidebar();
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                }
            });
        });
    </script>
</body>
</html>