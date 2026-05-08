<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En.cafe Admin</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logos.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Import a consistent font for the brand */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Inter:wght@400;500;600&display=swap');

        :root {
            --sidebar-bg: #0F172A;
            --item-active: #2563EB; 
            --item-hover: #1E2937;
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
            padding: 1.5rem 1rem; /* Reduced top padding from 2rem to 1.5rem */
            position: fixed;
            display: flex;
            flex-direction: column;
        }

        /* Fixed Brand Consistency - Logo Spacing */
        .sidebar-brand {
            display: block;
            margin-bottom: 1.5rem; /* Reduced bottom margin for tighter fit */
            padding-left: 0.5rem;
            text-decoration: none;
        }

        /* Logo sizing - No more stretching */
        .sidebar-logo {
            max-width: 140px; /* Adjust this to make the logo larger or smaller */
            height: auto;
            display: block;
        }

        /* Sidebar Link Styling */
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
            box-shadow: none !important;
        }

        .nav-link:hover {
            background: var(--item-hover);
            color: white !important;
        }

        .nav-link.active {
            background: var(--item-active) !important;
            color: white !important;
            font-weight: 600;
        }

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
            <img src="{{ asset('images/logo.png') }}" alt="En.cafe Logo" class="sidebar-logo">
        </a>

        <div class="mb-4 px-2">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">System Admin</small>
            <div class="text-white fw-bold fs-5">{{ auth()->user()->name }}</div>
        </div>

        <nav class="nav flex-column mb-auto">
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

        <form method="POST" action="{{ route('logout') }}" class="mt-auto border-top border-secondary pt-3">
            @csrf
            <button class="nav-link w-100 bg-transparent text-danger border-0">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>
</div>

</body>
</html>