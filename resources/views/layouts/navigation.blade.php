<nav class="bg-dark py-3">
    <div class="container d-flex justify-content-between align-items-center">

        <!-- LEFT SIDE -->
        <div class="d-flex align-items-center gap-4">
            <a class="text-white text-decoration-none" href="{{ route('dashboard') }}">
                <x-application-logo class="h-8 w-auto" />
            </a>

            @auth
                @if(trim(auth()->user()->role) === 'customer')
                    <a class="text-white text-decoration-none {{ request()->routeIs('menu.index') ? 'fw-bold text-warning' : '' }}"
                       href="{{ route('menu.index') }}">
                        Menu
                    </a>

                    <a class="text-white text-decoration-none {{ request()->routeIs('orders.my') ? 'fw-bold text-warning' : '' }}"
                       href="{{ route('orders.my') }}">
                        My Orders
                    </a>
                @endif

                @if(trim(auth()->user()->role) === 'admin')
                    <a class="text-white text-decoration-none {{ request()->routeIs('foods.*') ? 'fw-bold text-warning' : '' }}"
                       href="{{ route('foods.index') }}">
                        Manage Foods
                    </a>

                    <a class="text-white text-decoration-none {{ request()->routeIs('orders.index') ? 'fw-bold text-warning' : '' }}"
                       href="{{ route('orders.index') }}">
                        All Orders
                    </a>
                @endif

                @if(trim(auth()->user()->role) === 'kitchen')
                    <a class="text-white text-decoration-none {{ request()->routeIs('kitchen.orders') ? 'fw-bold text-warning' : '' }}"
                       href="{{ route('kitchen.orders') }}">
                        Kitchen Orders
                    </a>
                @endif
            @endauth
        </div>

        <!-- RIGHT SIDE -->
        <div class="d-flex align-items-center gap-3">
            @auth
                <a class="text-white text-decoration-none fw-bold" href="{{ route('profile.edit') }}">
                    {{ auth()->user()->name }} ({{ trim(auth()->user()->role) }})
                </a>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            @else
                <a class="btn btn-primary btn-sm" href="{{ route('login') }}">Login</a>
                <a class="btn btn-success btn-sm" href="{{ route('register') }}">Register</a>
            @endauth
        </div>

    </div>
</nav>