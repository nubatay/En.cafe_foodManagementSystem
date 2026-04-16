@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Kitchen Orders</h1>

    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Order #{{ $order->id }}</strong>
                        <span class="ms-2 text-muted">Customer: {{ $order->user->name ?? 'Unknown' }}</span>
                    </div>
                    <div>
                        @if($order->status === 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($order->status === 'Preparing')
                            <span class="badge bg-primary">Preparing</span>
                        @elseif($order->status === 'Completed')
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-secondary">{{ $order->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <p class="mb-1"><strong>Date:</strong> {{ $order->date }}</p>
                    <p class="mb-3"><strong>Total:</strong> ₱{{ number_format($order->total_price, 2) }}</p>

                    <div class="table-responsive mb-3">
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
                                        <td>{{ $detail->food->name ?? 'Food item unavailable' }}</td>
                                        <td>₱{{ number_format($detail->price, 2) }}</td>
                                        <td>{{ $detail->qty }}</td>
                                        <td>₱{{ number_format($detail->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                        @csrf
                        @method('PATCH')

                        <label for="status_{{ $order->id }}" class="form-label mb-0">Update Status:</label>

                        <select name="status" id="status_{{ $order->id }}" class="form-select w-auto">
                            <option value="Pending" {{ $order->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Preparing" {{ $order->status === 'Preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="Completed" {{ $order->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm">
                            Update
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            No kitchen orders available.
        </div>
    @endif
</div>
@endsection