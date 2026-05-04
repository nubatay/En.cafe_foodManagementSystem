@extends('layouts.customer')

@section('content')
<style>
    :root {
        --primary: #DC2626;
        --accent: #FACC15;
        --dark: #7F1D1D;
        --bg-warm: #FFF7ED;
        --text: #1F2937;
    }

    body { background-color: var(--bg-warm); color: var(--text); font-family: 'Inter', sans-serif; }

    /* Header & Action Buttons */
    .btn-back { color: var(--dark); border: 2px solid var(--dark); border-radius: 12px; font-weight: 600; }
    .btn-back:hover { background: var(--dark); color: #fff; }

    /* Order Summary Card */
    .summary-card {
        background: white;
        border-radius: 20px;
        border-left: 6px solid var(--accent);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    /* Order Detail Cards */
    .order-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(127, 29, 29, 0.05);
        overflow: hidden;
    }
    .order-card-header {
        background-color: #fcfcfc;
        border-bottom: 1px solid #f1f1f1;
        padding: 1.25rem;
    }

    /* Custom Badges */
    .badge-pending { background-color: var(--accent); color: var(--dark); }
    .badge-preparing { background-color: var(--primary); color: white; }
    .badge-delivered { background-color: #16A34A; color: white; }
    
    .table-custom thead { background-color: #F9FAFB; }
    .table-custom th { font-weight: 700; color: var(--dark); text-transform: uppercase; font-size: 0.75rem; border: none; }
    
    .grand-total-text { color: var(--primary); font-weight: 800; font-size: 1.5rem; }

    .modal-content { border-radius: 24px; border: none; }
    .btn-success-custom { background-color: #16A34A; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0" style="color: var(--dark);">My Orders</h1>
        <a href="{{ route('menu.index') }}" class="btn btn-back px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Menu
        </a>
    </div>

    @if($orders->count() > 0)
        @php
            $currentBillingStatus = $orders->first()->billing_status;
            $allDelivered = $orders->every(fn($order) => $order->computed_status === 'Delivered');
            $grandTotal = $orders->sum('total_price');
        @endphp

        <div class="card summary-card mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex gap-3 mb-2 flex-wrap">
                            <span><small class="text-muted fw-bold">TABLE</small> <span class="fw-bold">#{{ $orders->first()->table_number }}</span></span>
                            <span class="text-muted">|</span>
                            <span><small class="text-muted fw-bold">SESSION</small> <span class="text-monospace">{{ $orders->first()->session_code }}</span></span>
                        </div>
                        <div class="badge rounded-pill {{ $currentBillingStatus == 'Ordering' ? 'bg-light text-dark border' : 'bg-success' }} px-3 py-2">
                           Status: {{ $currentBillingStatus }}
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <small class="text-muted d-block fw-bold">TOTAL AMOUNT</small>
                        <span class="grand-total-text">₱{{ number_format($grandTotal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @foreach($orders as $order)
            <div class="card order-card mb-4">
                <div class="order-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">ORDER ID</span>
                        <h6 class="fw-bold mb-0">#{{ $order->id }}</h6>
                    </div>
                    <div>
                        @if($order->computed_status === 'Pending')
                            <span class="badge badge-pending px-3 py-2 rounded-pill"><i class="bi bi-clock-history me-1"></i> Pending</span>
                        @elseif($order->computed_status === 'Preparing')
                            <span class="badge badge-preparing px-3 py-2 rounded-pill"><i class="bi bi-fire me-1"></i> Preparing</span>
                        @elseif($order->computed_status === 'Delivered')
                            <span class="badge badge-delivered px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> Delivered</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $order->computed_status }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4">Item Details</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark">{{ $detail->food->name }}</div>
                                            @if($detail->option)
                                                <div class="small text-muted font-italic">{{ $detail->option }}</div>
                                            @endif
                                            <div class="mt-1">
                                                @if($detail->status === 'Pending')
                                                    <span class="badge bg-light text-warning border-0 p-0 fw-bold" style="font-size: 0.7rem;">● PENDING</span>
                                                @elseif($detail->status === 'Preparing')
                                                    <span class="badge bg-light text-primary border-0 p-0 fw-bold" style="font-size: 0.7rem;">● PREPARING</span>
                                                @elseif($detail->status === 'Delivered')
                                                    <span class="badge bg-light text-success border-0 p-0 fw-bold" style="font-size: 0.7rem;">● DELIVERED</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold text-muted">{{ $detail->qty }}</td>
                                        <td class="text-end pe-4 fw-bold">₱{{ number_format($detail->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 px-4 py-3 text-end">
                    <small class="text-muted">Order Placed: {{ $order->date }}</small>
                </div>
            </div>
        @endforeach

        {{-- BILLING ACTION --}}
        <div class="mt-5 pb-5 text-center">
            @if($currentBillingStatus === 'Ordering')
                @if($allDelivered)
                    <div class="p-4 rounded-4 bg-white shadow-sm border">
                        <h5 class="fw-bold mb-3">All items served!</h5>
                        <button class="btn btn-success-custom shadow w-100" style="max-width: 400px;" data-bs-toggle="modal" data-bs-target="#confirmModal">
                           <i class="bi bi-wallet2 me-2"></i> Proceed to Counter
                        </button>
                    </div>

                    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-5 text-center">
                                    <div class="mb-4">
                                        <i class="bi bi-question-circle text-primary display-4"></i>
                                    </div>
                                    <h4 class="fw-bold mb-3">Proceed to Checkout?</h4>
                                    <p class="text-muted mb-4">You are about to close this session and proceed to the counter for payment of <strong>₱{{ number_format($grandTotal, 2) }}</strong>.</p>
                                    
                                    <div class="d-grid gap-2">
                                        <form action="{{ route('orders.proceedToCounter') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold">Yes, Close My Session</button>
                                        </form>
                                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold" data-bs-dismiss="modal">I'm still eating</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-light border rounded-4 p-3 shadow-sm">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i>
                        You can proceed to the counter once your kitchen staff delivers all remaining items.
                    </div>
                @endif

            @elseif($currentBillingStatus === 'Requested')
                <div class="p-4 rounded-4 bg-white border border-warning shadow-sm">
                    <i class="bi bi-send-check display-5 text-warning mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">Bill Requested</h5>
                    <p class="text-muted mb-0">Please head over to the cashier counter to settle your payment. Thank you!</p>
                </div>

            @elseif($currentBillingStatus === 'Paid')
                <div class="p-4 rounded-4 bg-white border border-success shadow-sm">
                    <i class="bi bi-heart-fill display-5 text-success mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">Payment Received</h5>
                    <p class="text-muted mb-0">Thank you for dining with En.cafe! We hope to see you again soon.</p>
                </div>
            @endif
        </div>

    @else
        <div class="text-center py-5">
            <i class="bi bi-basket display-1 text-muted opacity-25"></i>
            <p class="h5 text-muted mt-3">You don't have any active orders yet.</p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary mt-3 px-5 py-3 rounded-pill">Start My First Order</a>
        </div>
    @endif
</div>
@endsection