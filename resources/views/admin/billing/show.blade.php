@extends('layouts.admin')

@section('content')
<h2 class="mb-3">Table {{ $table }} - {{ $session }}</h2>

<div class="card mb-4">
    <div class="card-body">
        <h4>Total: ₱{{ number_format($total, 2) }}</h4>
    </div>
</div>

@foreach($orders as $order)
    <div class="card mb-3">
        <div class="card-header">
            Order #{{ $order->id }} ({{ $order->status }})
        </div>

        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Food</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $d)
                        <tr>
                            <td>{{ $d->food->name }}</td>
                            <td>{{ $d->qty }}</td>
                            <td>₱{{ number_format($d->price, 2) }}</td>
                            <td>₱{{ number_format($d->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

<form action="{{ route('admin.billing.pay', [$table, $session]) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="payment" class="form-label">Payment Amount</label>
        <input type="number" step="0.01" min="0" class="form-control" id="payment" name="payment" value="{{ old('payment', $total) }}" required>
    </div>
    <button class="btn btn-success">
        Confirm Payment
    </button>
</form>
@endsection