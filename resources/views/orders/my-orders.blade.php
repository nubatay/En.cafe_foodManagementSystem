@extends('layouts.customer')

@section('content')
<div class="container">
    <h1 class="mb-4">My Orders</h1>

    <div class="mb-3">
        <a href="{{ route('menu.index') }}" class="btn btn-outline-dark">
            ← Back
        </a>
    </div>

    @if($orders->count() > 0)
        @php
            $currentBillingStatus = $orders->first()->billing_status;
            $allDelivered = $orders->every(function ($order) {
                return $order->computed_status === 'Delivered';
            });
            $grandTotal = $orders->sum('total_price');
        @endphp

        <div class="card mb-4 shadow-sm border-0 rounded-4">
            <div class="card-body">
                <p class="mb-2"><strong>Table Number:</strong> {{ $orders->first()->table_number }}</p>
                <p class="mb-2"><strong>Session Code:</strong> {{ $orders->first()->session_code }}</p>
                <p class="mb-2"><strong>Billing Status:</strong> {{ $currentBillingStatus }}</p>
                <p class="mb-0"><strong>Total So Far:</strong> ₱{{ number_format($grandTotal, 2) }}</p>
            </div>
        </div>

        @foreach($orders as $order)
            <div class="card mb-4 shadow-sm border-0 rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Order #{{ $order->id }}</strong>
                    </div>
                    <div>
                        @if($order->computed_status === 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($order->computed_status === 'Preparing')
                            <span class="badge bg-primary">Preparing</span>
                        @elseif($order->computed_status === 'Delivered')
                            <span class="badge bg-success">Delivered</span>
                        @else
                            <span class="badge bg-secondary">{{ $order->computed_status }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <p class="mb-1"><strong>Date:</strong> {{ $order->date }}</p>
                    <p class="mb-3"><strong>Total:</strong> ₱{{ number_format($order->total_price, 2) }}</p>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Food Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->food->name }}

                                            @if($detail->option)
                                                <br>
                                                <small class="text-muted">({{ $detail->option }})</small>
                                            @endif

                                            <br>
                                            @if($detail->status === 'Pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($detail->status === 'Preparing')
                                                <span class="badge bg-primary">Preparing</span>
                                            @elseif($detail->status === 'Delivered')
                                                <span class="badge bg-success">Delivered</span>
                                            @endif
                                        </td>
                                        <td>₱{{ number_format($detail->price, 2) }}</td>
                                        <td>{{ $detail->qty }}</td>
                                        <td>₱{{ number_format($detail->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- BILLING ACTION / MESSAGE --}}
        @if($currentBillingStatus === 'Ordering')
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    @if($allDelivered)
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                            Proceed to Counter
                        </button>

                        <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p class="mb-2">
                                            Are you sure you want to proceed to the counter?
                                        </p>

                                        <p class="mb-2">
                                            <strong>Total: ₱{{ number_format($grandTotal, 2) }}</strong>
                                        </p>

                                        <small class="text-muted">
                                            You will no longer be able to add new orders.
                                        </small>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <form action="{{ route('orders.proceedToCounter') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                Yes, Proceed
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            You can proceed to the counter only when all orders are delivered.
                        </div>
                    @endif
                </div>
            </div>

        @elseif($currentBillingStatus === 'Requested')
            <div class="alert alert-warning">
                Your bill request has been sent. Please proceed to the counter.
            </div>

        @elseif($currentBillingStatus === 'Paid')
            <div class="alert alert-success">
                This table session has already been paid.
            </div>
        @endif

    @else
        <div class="alert alert-info">
            You do not have any active orders yet.
        </div>
    @endif
</div>
@endsection