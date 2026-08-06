<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Dashboard') ?> | Sales Excellence Awardees Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.170.0/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.170.0/examples/jsm/"
        }
    }
    </script>
    <style>
        :root {
            --ugc-green: #0a6a3b;
            --ugc-green-dark: #07522e;
            --ugc-red: #d71920;
            --ugc-black: #111111;
            --ugc-gray: #667085;
            --ugc-bg: #f3f5f7;
            --ugc-panel: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--ugc-bg);
            font-family: "Segoe UI", Arial, sans-serif;
            color: #121212;
        }

        .dashboard-shell {
            display: flex;
            height: 100vh;
            max-height: 100vh;
            position: relative;
        }

        .sidebar {
            width: 280px;
            background: url('/Side%20bar%20background%20.png') no-repeat center center / cover;
            border-right: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: visible;
            position: relative;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(7,82,46,0.88) 0%, rgba(0,0,0,0.7) 100%);
            z-index: 0;
            pointer-events: none;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .sidebar-toggle {
            display: none !important;
        }

        .brand-wrap {
            display: none !important;
        }

        .side-section {
            padding: 0.8rem 0.85rem;
            border-bottom: 1px solid #eef1f5;
            flex-shrink: 0;
            overflow: visible !important;
            position: relative;
            z-index: 1;
        }

        /* Simple Menu Styles */
        .menu-group {
            margin-bottom: 0rem;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.85rem;
            text-decoration: none;
            color: rgba(255,255,255,0.85);
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            margin-bottom: 0.15rem;
            border-radius: 8px;
            min-height: 44px;
            letter-spacing: 0.2px;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .menu-item.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border-left: 3px solid #fff;
        }

        .menu-icon {
            font-size: 1.1rem;
            flex-shrink: 0;
            min-width: 24px;
            text-align: center;
            opacity: 0.9;
        }

        .menu-label {
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            flex: 1;
            transition: opacity 0.2s ease;
            line-height: 1.3;
            font-size: 0.78rem;
        }

        .sidebar.collapsed .menu-label {
            display: none;
        }

        .sidebar.collapsed .menu-item {
            width: 80px;
            height: 80px;
            padding: 0;
            border: none;
            margin: 0;
            background: transparent;
            border-radius: 0;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 0;
            font-size: 0;
        }

        .sidebar.collapsed .menu-item:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed .menu-icon {
            font-size: 1.5rem;
            min-width: auto;
        }

        .sub-menu-items {
            max-height: 500px;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding-left: 0;
        }

        .sub-menu-items.hidden {
            max-height: 0;
        }

        .sidebar.collapsed .sub-menu-items {
            position: absolute;
            left: 80px;
            top: 60px;
            width: 200px;
            background: var(--ugc-green);
            border: 1px solid #e6e9ef;
            border-radius: 7px;
            padding: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            flex-direction: column;
            gap: 0.3rem;
            max-height: 400px;
        }

        .sidebar.collapsed .sub-menu-items.hidden {
            max-height: 0;
            display: none !important;
        }

        .sub-menu-link {
            display: block;
            padding: 0.5rem 0.8rem 0.5rem 2rem;
            margin: 0;
            text-decoration: none;
            color: #374151;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
            background: #f9fafb;
        }

        .sub-menu-link:hover {
            background: #f3f4f6;
            color: var(--ugc-green);
        }

        .sub-menu-link.active {
            background: #e8f5f1;
            color: var(--ugc-green);
            font-weight: 600;
        }

        .sidebar.collapsed .sub-menu-link {
            padding: 0.65rem 0.75rem;
            padding-left: 0.75rem;
            background: linear-gradient(135deg, #f0f9f7 0%, #e8f5f1 100%);
            border-radius: 5px;
            font-size: 0.82rem;
        }

        .sidebar.collapsed .sub-menu-link:hover {
            background: linear-gradient(135deg, #e8f5f1 0%, #dff0eb 100%);
        }

        .logout-button-box {
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 6px;
            background: rgba(255,255,255,0.05);
            margin: 0 0.85rem;
        }

        .logout-button-box .logout-link {
            padding: 6px 10px !important;
            border-radius: 4px;
        }

        .logout-button-box .logout-link:hover {
            background: rgba(255,255,255,0.1) !important;
        }

        .btn-logout {
            display: block;
            width: calc(100% - 1.7rem);
            margin-left: 0.85rem;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 6px;
            padding: 0.4rem 0.55rem;
            margin-top: 0.35rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
            text-decoration: none;
        }

        .filters-title {
            color: rgba(255,255,255,0.6);
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            padding: 0 0.85rem;
        }

        .filter-label {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.6);
            font-weight: 600;
            margin-bottom: 0.2rem;
            padding: 0 0.85rem;
        }

        .filter-select {
            font-size: 0.75rem !important;
            margin-bottom: 0.4rem !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
            width: calc(100% - 1.7rem) !important;
            margin-left: 0.85rem !important;
            padding: 0.4rem 0.65rem !important;
            cursor: pointer !important;
            background-color: rgba(255,255,255,0.1) !important;
            color: #fff !important;
            appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.65rem center !important;
            background-size: 14px 10px !important;
            padding-right: 2rem !important;
            position: relative !important;
            z-index: 10 !important;
            display: block !important;
            border-radius: 6px !important;
        }
        
        .filter-select:focus {
            outline: none !important;
            border-color: rgba(255,255,255,0.3) !important;
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.1) !important;
        }

        .filter-select:active,
        .filter-select:focus-visible {
            outline: none !important;
            border-color: rgba(255,255,255,0.3) !important;
        }

        .filter-select option {
            background: #0B7A3B;
            color: #fff;
        }

        .btn-reset {
            width: calc(100% - 1.7rem);
            margin-left: 0.85rem;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 6px;
            padding: 0.4rem 0.55rem;
            margin-top: 0.35rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-reset:hover {
            background: rgba(255,255,255,0.15);
        }

        .logout-link {
            display: block;
            width: calc(100% - 1.7rem);
            margin-left: 0.85rem;
            border: 0;
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 6px;
            padding: 0.4rem 0.55rem;
            margin-top: 0.35rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            text-decoration: none;
        }

        .logout-link:hover {
            background: rgba(255,255,255,0.25);
            color: #fff;
            text-decoration: none;
        }

        .main-area {
            flex: 1;
            width: 100%;
            padding: 0.75rem 0.9rem 1.4rem;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .dashboard-shell.sidebar-closed .main-area {
            width: 100%;
            padding: 0.6rem 0.4rem 1rem;
        }

        /* Hover trigger area on the left edge */
        .main-area::before {
            content: '';
            position: fixed;
            left: 0;
            top: 0;
            width: 10px;
            height: 100vh;
            z-index: 999;
        }

        .main-area:hover::before,
        .main-area:hover ~ .sidebar {
            /* Trigger sidebar on hover over left edge */
        }

        .topbar {
            background: var(--ugc-panel);
            border: 1px solid #e5e7eb;
            border-left: 5px solid var(--ugc-green);
            border-radius: 10px;
            padding: 0.7rem 0.85rem;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            margin-bottom: 0.7rem;
            align-items: center;
        }

        .topbar-title {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 900;
            letter-spacing: 0.2px;
            color: var(--ugc-green);
            flex-shrink: 1;
            white-space: normal;
            order: 1;
        }

        .topbar-sub {
            color: #6b7280;
            font-size: 0.83rem;
            margin-top: 0.1rem;
        }

        .topbar-right {
            text-align: right;
            font-size: 0.82rem;
            color: #6b7280;
            font-weight: 600;
        }

        .panel-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 2px 7px rgba(15, 23, 42, 0.05);
        }

        .panel-header-red {
            background: #6b7280;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 0.45rem 0.65rem;
            font-size: 0.79rem;
            font-weight: 800;
            letter-spacing: .25px;
            text-transform: uppercase;
        }

        .panel-header-green {
            background: #6b7280;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 0.45rem 0.65rem;
            font-size: 0.79rem;
            font-weight: 800;
            letter-spacing: .25px;
            text-transform: uppercase;
        }

        .panel-body {
            padding: 0.65rem;
        }

        .compact-table {
            width: 100%;
            font-size: 0.75rem;
        }

        .compact-table th {
            font-size: 0.68rem;
            text-transform: uppercase;
            color: #374151;
            background: transparent;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.35rem;
            font-weight: 700;
        }

        .compact-table td {
            padding: 0.34rem;
            border-bottom: 1px solid #f0f2f6;
            vertical-align: middle;
        }

        @media (max-width: 1200px) {
            .sidebar {
                width: 250px;
            }

            .sidebar.collapsed {
                width: 80px;
            }
        }

        .logo-section {
            background: #ffffff;
            padding: 1.2rem 0.85rem;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid #e6e9ef;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .logo-section img {
            max-width: 100%;
            width: 100%;
            height: auto;
            max-height: 140px;
        }

        .sidebar.collapsed .logo-section {
            padding: 0.5rem;
        }

        .sidebar.collapsed .logo-section img {
            max-height: 50px;
        }

        @media (max-width: 992px) {
            .dashboard-shell {
                display: block;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                max-height: none;
                flex-direction: row;
                flex-wrap: wrap;
            }

            .sidebar.collapsed {
                width: 100%;
            }

            .sidebar-toggle {
                display: none;
            }

            .main-area {
                padding: 0.75rem;
            }

            .side-section {
                display: flex;
                width: 100%;
                flex-wrap: wrap;
            }

            .menu-group {
                flex: 1 1 auto;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-shell">
        <aside class="sidebar" id="sidebar">
            <div class="logo-section">
                <img src="/ugc_logo_03.png" alt="Union Galvasteel Logo">
            </div>

            <div class="side-section" style="padding: 0.5rem 0.85rem;">
                <!-- Menu Tabs -->
                <div class="menu-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <?php
                    $currentUri = service('uri')->getPath();
                    $currentQuery = service('uri')->getQuery();
                    // Normalize: strip 'index.php/' prefix and leading/trailing slashes
                    $normalized = trim(preg_replace('#^index\.php/#', '', $currentUri), '/');
                    $isOverviewActive = ($normalized === 'dashboard') && (strpos($currentQuery, 'tab=masterData') !== false);
                    $isPrimeBendedActive = ($normalized === 'dashboard') && (strpos($currentQuery, 'tab=primeBended') !== false);
                    $isPrimeSpandrelActive = ($normalized === 'dashboard') && (strpos($currentQuery, 'tab=primeSpandrel') !== false);
                    $isSteelDeckActive = ($normalized === 'dashboard') && (strpos($currentQuery, 'tab=steelDeck') !== false);
                    $isLbActive = ($normalized === 'leaderboard' || strpos($normalized, 'leaderboard') === 0);
                    $isExecActive = (strpos($normalized, 'executive') === 0);
                    $isDashboardPage = ($normalized === 'dashboard');
                    $isLeaderboardPage = ($normalized === 'leaderboard' || strpos($normalized, 'leaderboard') === 0);
                    ?>
                    <a href="/executive" class="menu-item <?= (strpos($normalized, 'executive') === 0) ? 'active' : '' ?>" id="executiveMenu">
                        <span class="menu-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></span>
                        <span class="menu-label">EXECUTIVE DASHBOARD</span>
                    </a>
                    <button type="button" class="menu-item <?= $isPrimeBendedActive ? 'active' : '' ?>" id="primeBendedMenu" data-tab="primeBended" onclick="event.preventDefault(); if (typeof window.handleTabSwitch === 'function') { window.handleTabSwitch('primeBended'); } else { window.location.href='/dashboard?tab=primeBended'; }">
                        <span class="menu-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></span>
                        <span class="menu-label">SALES EXCELLENCE AWARDEE</span>
                    </button>
                    <button type="button" class="menu-item <?= $isPrimeSpandrelActive ? 'active' : '' ?>" id="primeSpandrelMenu" data-tab="primeSpandrel" onclick="event.preventDefault(); if (typeof window.handleTabSwitch === 'function') { window.handleTabSwitch('primeSpandrel'); } else { window.location.href='/dashboard?tab=primeSpandrel'; }">
                        <span class="menu-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                        <span class="menu-label">TOP BRANCH RECOGNITION</span>
                    </button>
                    <button type="button" class="menu-item <?= $isSteelDeckActive ? 'active' : '' ?>" id="steelDeckMenu" data-tab="steelDeck" onclick="event.preventDefault(); if (typeof window.handleTabSwitch === 'function') { window.handleTabSwitch('steelDeck'); } else { window.location.href='/dashboard?tab=steelDeck'; }">
                        <span class="menu-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></span>
                        <span class="menu-label">ELITE CIRCLE</span>
                    </button>
                    <a href="/leaderboard" class="menu-item <?= $isLbActive ? 'active' : '' ?>" id="leaderboardMenu">
                        <span class="menu-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg></span>
                        <span class="menu-label">LEADERBOARD</span>
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="side-section">
                <div class="filters-title">Filters</div>
                <div class="filter-label">Region</div>
                <select id="sideRegionFilter" class="form-select form-select-sm filter-select">
                    <option value="">All Regions</option>
                    <option value="SOUTH LUZON">SOUTH LUZON</option>
                    <option value="NORTH & CENTRAL LUZON">NORTH & CENTRAL LUZON</option>
                    <option value="VISAYAS">VISAYAS</option>
                    <option value="MINDANAO">MINDANAO</option>
                </select>
            </div>

            <!-- Settings -->
            <div class="side-section" style="border-bottom: none;">
                <a href="/settings" class="menu-item <?= (strpos($normalized, 'settings') === 0) ? 'active' : '' ?>" id="settingsMenu" style="margin-bottom:0;">
                    <span class="menu-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg></span>
                    <span class="menu-label">SETTINGS</span>
                </a>
            </div>

            <!-- Spacer to push logout to bottom -->
            <div style="flex:1;"></div>

            <!-- Logout Button -->
            <div class="side-section" style="border-bottom: none;">
                <a href="/logout" class="btn-logout">Logout</a>
            </div>
        </aside>

        <main class="main-area" style="display:flex;flex-direction:column;">
            <?php if (!($normalized === 'settings' || strpos($normalized, 'settings') === 0 || $isExecActive)): ?>
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <div>
                            <h4 class="topbar-title" style="text-transform: uppercase; color: #000;font-size:1.4rem;">
                                <?php if ($isExecActive): ?>
                                    EXECUTIVE <span style="color: var(--ugc-red);">ANALYTICS</span> DASHBOARD
                                <?php elseif ($isOverviewActive): ?>
                                    <span style="color: var(--ugc-red);">OVERVIEW AND ANALYTICS</span> DASHBOARD
                                <?php elseif ($isLbActive): ?>
                                    SALES EXCELLENCE <span style="color: var(--ugc-red);">LEADERBOARD</span> DASHBOARD
                                <?php elseif ($isSteelDeckActive): ?>
                                    SALES EXCELLENCE <span style="color: var(--ugc-red);">ELITE CIRCLE</span> DASHBOARD
                                <?php elseif ($isPrimeSpandrelActive): ?>
                                    <span style="color: var(--ugc-red);">TOP BRANCH RECOGNITION</span> DASHBOARD
                                <?php elseif ($isPrimeBendedActive): ?>
                                    SALES EXCELLENCE <span style="color: var(--ugc-red);">AWARDEE</span> DASHBOARD
                                <?php else: ?>
                                    <span style="color: var(--ugc-red);">DASHBOARD</span>
                                <?php endif; ?>
                            </h4>
                                    <div class="topbar-sub">
                                        <?php if ($isExecActive): ?>
                                            Executive-level analytics, KPIs, and consolidated insights across all programs
                                        <?php elseif ($isOverviewActive): ?>
                                            Consolidated summary and analytics across all product segments
                                        <?php elseif ($isLbActive): ?>
                                            Monthly Rank Tracking Per Region
                                        <?php elseif ($isSteelDeckActive): ?>
                                            Top Volume & CM Per Region - Recognizing Top Performing Dealers
                                        <?php elseif ($isPrimeSpandrelActive): ?>
                                            Recognizing Top Performing Branch
                                        <?php elseif ($isPrimeBendedActive): ?>
                                            Recognizing Top Performing Dealers
                                        <?php else: ?>
                                            Sales Excellence Monitoring System
                                        <?php endif; ?>
                                    </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-left:auto;">
                        <div id="viewToggleContainer" style="display: flex; gap: 0.5rem; align-items: flex-start;"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script>
        // Sidebar initialization
        const sidebar = document.getElementById('sidebar');
        const dashboardShell = document.querySelector('.dashboard-shell');

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target)) {
                dashboardShell.classList.remove('sidebar-open');
            }
        });

        // Tab switching is handled in dashboard/index.php
        
        // Generate Elite Circle Summary button
        const generateEliteCircleBtn = document.getElementById('generateEliteCircleBtn');
        if (generateEliteCircleBtn) {
            generateEliteCircleBtn.addEventListener('click', async function() {
                if (!confirm('Generate Elite Circle summary based on current Sales Excellence data?')) {
                    return;
                }
                try {
                    generateEliteCircleBtn.disabled = true;
                    generateEliteCircleBtn.textContent = 'Generating...';
                    
                    const response = await fetch('/data/generate-elite-circle', {
                        method: 'POST'
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Elite Circle summary generated successfully for  ' + result.regions_generated + ' regions!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (result.error || 'Generation failed'));
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                } finally {
                    generateEliteCircleBtn.disabled = false;
                    generateEliteCircleBtn.textContent = 'Generate Elite Circle Summary';
                }
            });
        }

        // Reset Elite Circle Data button
        const resetEliteCircleBtn = document.getElementById('resetEliteCircleBtn');
        if (resetEliteCircleBtn) {
            resetEliteCircleBtn.addEventListener('click', async function() {
                if (!confirm('Delete ALL Elite Circle data? This cannot be undone.')) {
                    return;
                }
                try {
                    resetEliteCircleBtn.disabled = true;
                    resetEliteCircleBtn.textContent = 'Resetting...';
                    
                    const response = await fetch('/data/delete-elite-circle', {
                        method: 'POST'
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('All Elite Circle data has been deleted!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (result.error || 'Reset failed'));
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                } finally {
                    resetEliteCircleBtn.disabled = false;
                    resetEliteCircleBtn.textContent = 'Reset Elite Circle Data';
                }
            });
        }
    </script>
</body>
</html>