@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- HERO -->
    <div class="p-5 mb-4 rounded-4 text-white shadow" style="background: linear-gradient(135deg, #212529, #495057);">
        <div class="container-fluid py-3">
            <h1 class="display-5 fw-bold mb-3">Welcome to En.cafe</h1>
            <p class="fs-5 mb-4">
                Manage food orders smoothly for customers, admins, and kitchen staff.
            </p>

            @if(trim(auth()->user()->role) === 'customer')
                <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg me-2">Order Now</a>
                <a href="{{ route('orders.my') }}" class="btn btn-outline-light btn-lg">My Orders</a>
            @endif

            @if(trim(auth()->user()->role) === 'admin')
                <a href="{{ route('foods.index') }}" class="btn btn-primary btn-lg me-2">Manage Foods</a>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-lg">All Orders</a>
            @endif

            @if(trim(auth()->user()->role) === 'kitchen')
                <a href="{{ route('kitchen.orders') }}" class="btn btn-primary btn-lg">Kitchen Orders</a>
            @endif
        </div>
    </div>

    <!-- USER INFO -->
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-4">
            <h3 class="mb-2">Hello, {{ auth()->user()->name }} 👋</h3>
            <p class="mb-0 text-muted">
                You are logged in as <strong>{{ ucfirst(trim(auth()->user()->role)) }}</strong>.
            </p>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="row g-4">

        @if(trim(auth()->user()->role) === 'customer')
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Browse Menu</h4>
                        <p class="text-muted">
                            View available food items, check prices, and choose what you want to order.
                        </p>
                        <a href="{{ route('menu.index') }}" class="btn btn-primary">Go to Menu</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Track My Orders</h4>
                        <p class="text-muted">
                            Check your recent orders and see whether they are pending, preparing, or completed.
                        </p>
                        <a href="{{ route('orders.my') }}" class="btn btn-primary">View My Orders</a>
                    </div>
                </div>
            </div>
        @endif

        @if(trim(auth()->user()->role) === 'admin')
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Manage Foods</h4>
                        <p class="text-muted">
                            Add new items, edit details, update stock, and deactivate unavailable foods.
                        </p>
                        <a href="{{ route('foods.index') }}" class="btn btn-primary">Manage Foods</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Monitor Orders</h4>
                        <p class="text-muted">
                            View all customer orders and keep track of order progress in one place.
                        </p>
                        <a href="{{ route('orders.index') }}" class="btn btn-primary">View All Orders</a>
                    </div>
                </div>
            </div>
        @endif

        @if(trim(auth()->user()->role) === 'kitchen')
            <div class="col-md-12">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3">Kitchen Queue</h4>
                        <p class="text-muted">
                            Open the kitchen page to see incoming orders and update them to Preparing or Completed.
                        </p>
                        <a href="{{ route('kitchen.orders') }}" class="btn btn-primary">Open Kitchen Orders</a>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- EXTRA INFO -->
    <div class="row g-4 mt-1">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">Simple Workflow</h5>
                    <p class="text-muted mb-0">
                        Customers place orders, admins manage foods, and kitchen staff update statuses.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">Real-Time Stock</h5>
                    <p class="text-muted mb-0">
                        Food stock decreases automatically whenever an order is placed successfully.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">Order Tracking</h5>
                    <p class="text-muted mb-0">
                        Everyone sees the right information based on their role in the system.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection