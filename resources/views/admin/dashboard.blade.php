@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="page-title">Dashboard</h2>
    <p class="soft-text mb-0">Overview of foods, orders, and billing requests.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="soft-text">Total Foods</div>
                <div class="stat-number">{{ $totalFoods }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="soft-text">Available Foods</div>
                <div class="stat-number">{{ $availableFoods }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="soft-text">Out of Stock</div>
                <div class="stat-number">{{ $outOfStockFoods }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="soft-text">Billing Requests</div>
                <div class="stat-number">{{ $billingRequests }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="soft-text">Total Orders</div>
                <div class="stat-number">{{ $totalOrders }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="soft-text">Pending Orders</div>
                <div class="stat-number">{{ $pendingOrders }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="soft-text">Preparing / Delivered</div>
                <div class="stat-number">{{ $preparingOrders }} / {{ $deliveredOrders }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-clean">
            <div class="card-body p-4">
                <h5 class="mb-3 fw-bold">Recent Orders</h5>

                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Table</th>
                                    <th>Session</th>
                                    <th>Status</th>
                                    <th>Billing</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->table_number }}</td>
                                        <td>{{ $order->session_code }}</td>
                                        <td>{{ $order->status }}</td>
                                        <td>{{ $order->billing_status }}</td>
                                        <td>₱{{ number_format($order->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">No recent orders yet.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-clean">
            <div class="card-body p-4">
                <h5 class="mb-3 fw-bold">Quick Actions</h5>

                <div class="d-grid gap-2">
                    <a href="{{ route('foods.index') }}" class="btn btn-primary">Manage Foods</a>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">View All Orders</a>
                    <a href="{{ route('admin.billing') }}" class="btn btn-outline-dark">Open Billing</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection