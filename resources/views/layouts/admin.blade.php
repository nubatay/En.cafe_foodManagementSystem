<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En.cafe Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Import a consistent font for the brand */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Inter:wght@400;500;600&display=swap');

        :root {
            --sidebar-bg: #0F172A;
            --item-active: #2563EB; /* En.cafe Green */
            --item-hover: #1E293B;
            --text-muted: #94A3B8;
        }

        body {
            background-color: #F8FAFC;
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            min-height: 100vh;
            padding: 2rem 1rem;
            position: fixed;
        }

        /* Fixed Brand Consistency */
        .sidebar-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: white !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2.5rem;
            padding-left: 0.5rem;
        }

        /* Sidebar Link Styling - Prevents "Blue Stick" */
        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border: none !important;
            outline: none !important;
            box-shadow: none !important; /* Removes Bootstrap blue shadow */
        }

        .nav-link:hover {
            background: var(--item-hover);
            color: white !important;
        }

        /* Active State - Matches your video toggle color */
        .nav-link.active {
            background: var(--item-active) !important;
            color: white !important;
            font-weight: 600;
        }

        /* Fix for the blue "outline" when clicking */
        .nav-link:focus {
            background: inherit; 
            color: inherit;
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem;
            width: calc(100% - 280px);
        }
    </style>
</head>
<body>

<div class="d-flex">
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            En.cafe
        </a>

        <div class="mb-4 px-2">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">System Admin</small>
            <div class="text-white fw-bold">{{ auth()->user()->name }}</div>
        </div>

        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            
            <a href="{{ route('foods.index') }}" 
               class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}">
                <i class="bi bi-egg-fried"></i> Menu Items
            </a>

            <a href="{{ route('orders.index') }}" 
               class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Order History
            </a>

            <a href="{{ route('admin.billing.index') }}" 
               class="nav-link {{ request()->routeIs('admin.billing*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Billing Queue
            </a>

            <a href="{{ route('profile.edit') }}" 
               class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Profile Settings
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="mt-auto">
            @csrf
            <button class="btn btn-outline-danger w-100 border-0 rounded-3 text-start px-3">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </button>
        </form>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>
</div>

</body>
</html>