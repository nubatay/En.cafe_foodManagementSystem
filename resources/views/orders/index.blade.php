@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">All Orders</h1>

    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Order #{{ $order->id }}</strong>
                        <span class="ms-2 text-muted">
                            Customer: {{ $order->user->name ?? 'Unknown' }}
                        </span>
                    </div>

                    <div>
                        @if($order->billing_status === 'Ordering')
                            <span class="badge bg-warning text-dark">Ordering</span>
                        @elseif($order->billing_status === 'Requested')
                            <span class="badge bg-primary">Requested</span>
                        @elseif($order->billing_status === 'Paid')
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-secondary">{{ $order->billing_status }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <p class="mb-1"><strong>Date:</strong> {{ $order->date }}</p>
                    <p class="mb-1"><strong>Total:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                    <p class="mb-1"><strong>Payment:</strong> ₱{{ number_format($order->payment_amount, 2) }}</p>
                    <p class="mb-1"><strong>Change:</strong> ₱{{ number_format($order->change_amount, 2) }}</p>
                    <p class="mb-3"><strong>Payment Status:</strong> {{ $order->payment_status }}</p>
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
                                        <td>{{ $detail->food->name ?? 'Food unavailable' }}</td>
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
    @else
        <div class="alert alert-info">
            No orders found.
        </div>
    @endif
</div>
@endsection 