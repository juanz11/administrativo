<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Administrativo</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #9ca3af;
            --sidebar-active: #ffffff;
            --border-color: #e5e7eb;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 24px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidebar-active);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header span {
            color: var(--primary-color);
        }

        .nav-list {
            list-style: none;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-item {
            padding: 0 16px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.05);
            color: var(--sidebar-active);
        }

        .nav-link.active {
            background-color: var(--primary-color);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .content-wrapper {
            padding: 32px;
            flex: 1;
        }

        /* Dashboard Cards */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-color);
        }
        
        .card.success::before { background: var(--success-color); }
        .card.warning::before { background: var(--warning-color); }
        .card.danger::before { background: var(--danger-color); }

        .card-title {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Charts Section */
        .chart-section {
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-main);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 600;
        }

        .logout-button {
            border: none;
            border-radius: 8px;
            padding: 9px 12px;
            background: var(--danger-color);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <span>Admin</span>Panel
        </div>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('divisas.index') }}" class="nav-link {{ request()->routeIs('divisas.*') ? 'active' : '' }}">Divisas</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('bancos.index') }}" class="nav-link {{ request()->routeIs('bancos.*') ? 'active' : '' }}">Bancos</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Cuentas por Cobrar</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Cuentas por Pagar</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar">
            <div class="topbar-title">@yield('title', 'Inicio')</div>
            <div class="user-profile">
                <span>{{ session('panel_user', 'Admin') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-button">Salir</button>
                </form>
            </div>
        </header>

        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
