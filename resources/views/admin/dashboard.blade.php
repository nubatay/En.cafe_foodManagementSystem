@extends('layouts.admin')

@section('content')
<style>
    :root {
        --primary: #2563EB;
        --secondary: #1E293B;
        --accent: #22C55E;
        --background: #F8FAFC;
        --text-main: #0F172A;
        --text-muted: #64748B;
    }

    body { background-color: var(--background); color: var(--text-main); font-family: 'Inter', sans-serif; }
    
    .page-title { font-weight: 800; color: var(--text-main); letter-spacing: -0.025em; }
    .soft-text { color: var(--text-muted); font-size: 0.95rem; }

    /* Stat Cards */
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .stat-label { font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); }
    .stat-number { font-size: 1.75rem; font-weight: 800; color: var(--text-main); }
    
    /* Icon Accent Borders */
    .border-primary-left { border-left: 4px solid var(--primary); }
    .border-accent-left { border-left: 4px solid var(--accent); }
    .border-danger-left { border-left: 4px solid #EF4444; }

    /* Tables */
    .card-clean { border: none; border-radius: 16px; background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .table thead th {
        background-color: #F1F5F9;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border: none;
        padding: 15px;
    }
    .table td { padding: 15px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }

    /* Status Badges */
    .badge-status { padding: 6px 12px; border-radius: 99px; font-weight: 600; font-size: 0.75rem; }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-delivered { background: #DCFCE7; color: #166534; }
    .status-preparing { background: #DBEAFE; color: #1E40AF; }

    /* Buttons */
    .btn-primary { background-color: var(--primary); border: none; padding: 10px 20px; font-weight: 600; border-radius: 8px; }
    .btn-primary:hover { background-color: #1D4ED8; }
    .btn-outline-primary { border: 2px solid var(--primary); color: var(--primary); font-weight: 600; border-radius: 8px; }
    .btn-outline-primary:hover { background-color: var(--primary); color: white; }
</style>

<div class="container-fluid py-4">
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1" style="color: var(--secondary);">En.cafe Admin Dashboard</h1>
        <p class="soft-text">Operational overview for foods, orders, and real-time billing.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-primary-left">
                <div class="card-body">
                    <div class="stat-label">Total Foods</div>
                    <div class="stat-number">{{ $totalFoods }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-accent-left">
                <div class="card-body">
                    <div class="stat-label">Available</div>
                    <div class="stat-number text-success">{{ $availableFoods }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-danger-left">
                <div class="card-body">
                    <div class="stat-label">Out of Stock</div>
                    <div class="stat-number text-danger">{{ $outOfStockFoods }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm" style="background-color: var(--secondary);">
                <div class="card-body">
                    <div class="stat-label text-slate-400 text-white opacity-75">Billing Requests</div>
                    <div class="stat-number text-white">{{ $billingRequests }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Orders</div>
                        <div class="stat-number">{{ $totalOrders }}</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-circle text-primary">
                        <i class="bi bi-cart-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-label">Pending Approval</div>
                    <div class="stat-number text-warning">{{ $pendingOrders }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <div class="stat-label">Prep / Delivered</div>
                    <div class="stat-number">
                        <span class="text-primary">{{ $preparingOrders }}</span> 
                        <span class="text-muted mx-2">/</span> 
                        <span class="text-success">{{ $deliveredOrders }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-9">
            <div class="card-clean shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 fw-bold">Live Order Stream</h5>
                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">Real-time Updates</span>
                    </div>

                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Table No.</th>
                                        <th>Session Code</th>
                                        <th>Status</th>
                                        <th>Billing</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="fw-bold">#{{ $order->id }}</td>
                                            <td><span class="badge bg-light text-dark px-3 py-2">Table {{ $order->table_number }}</span></td>
                                            <td class="text-muted font-monospace">{{ $order->session_code }}</td>
                                            <td>
                                                <span class="badge-status {{ 
                                                    $order->status == 'pending' ? 'status-pending' : 
                                                    ($order->status == 'delivered' ? 'status-delivered' : 'status-preparing') 
                                                }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="fw-semibold text-uppercase text-muted">{{ $order->billing_status }}</small>
                                            </td>
                                            <td class="fw-bold text-dark">₱{{ number_format($order->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted italic">No active orders found in the system.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card-clean h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="mb-4 fw-bold">Management</h5>

                    <div class="d-grid gap-3">
                        <a href="{{ route('foods.index') }}" class="btn btn-primary">
                            Manage Menu
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                            Full Order History
                        </a>
                        <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-dark border-2 fw-bold">
                            Billing Terminal
                        </a>
                        <hr class="my-2 opacity-10">
                        <p class="text-muted small text-center mb-0">System Version 2.0.4</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection