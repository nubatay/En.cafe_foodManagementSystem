<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - En.cafe</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            background-color: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #111827;
            color: white;
            padding: 24px 18px;
        }

        .sidebar-brand {
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 24px;
        }

        .sidebar-user {
            background: rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 24px;
        }

        .sidebar-user .name {
            font-weight: 700;
            margin-bottom: 2px;
        }

        .sidebar-user .role {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 8px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #1f2937;
            color: #fff;
            font-weight: 600;
        }

        .main-content {
            flex: 1;
            padding: 28px;
        }

        .page-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }
    </style>
</head>
<body>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">En.cafe</a>

        <div class="sidebar-user">
            <div class="name">{{ auth()->user()->name }}</div>
            <div class="role">Administrator</div>
        </div>

        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('foods.index') }}"
               class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}">
                Manage Foods
            </a>

            <a href="{{ route('orders.index') }}"
               class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                All Orders
            </a>

            <a href="{{ route('admin.billing') }}"
               class="nav-link {{ request()->routeIs('admin.billing*') ? 'active' : '' }}">
                Billing
            </a>

            <a href="{{ route('profile.edit') }}"
               class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                Profile
            </a>
        </nav>

        <div class="mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-danger w-100">Logout</button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-4 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-warning border-0 rounded-4 shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>