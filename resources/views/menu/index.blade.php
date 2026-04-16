@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Menu</h1>

    @if($foods->count() > 0)
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            <div class="row">
                @foreach($foods as $index => $food)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-3">{{ $food->name }}</h5>

                                <p class="mb-2">
                                    <strong>Category:</strong> {{ $food->category }}
                                </p>

                                <p class="mb-2">
                                    <strong>Price:</strong> ₱{{ number_format($food->price, 2) }}
                                </p>

                                <p class="mb-2">
                                    <strong>Stock:</strong> {{ $food->stock }}
                                </p>

                                @if(!empty($food->description))
                                    <p class="text-muted mb-3">{{ $food->description }}</p>
                                @endif

                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $food->id }}">

                                <div class="mb-3">
                                    <label for="qty_{{ $food->id }}" class="form-label fw-semibold">Quantity</label>
                                    <input
                                        type="number"
                                        id="qty_{{ $food->id }}"
                                        name="items[{{ $index }}][qty]"
                                        class="form-control item-qty"
                                        min="0"
                                        max="{{ $food->stock }}"
                                        value="0"
                                        data-price="{{ $food->price }}"
                                    >
                                </div>

                                <p class="mb-0">
                                    <strong>Subtotal:</strong>
                                    ₱<span class="item-subtotal">0.00</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card shadow-sm border-0 rounded-4 mt-3">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3">Order Summary</h4>

                    <p class="mb-2">
                        <strong>Total Order Amount:</strong>
                        <span class="text-primary fs-5">₱<span id="grandTotal">0.00</span></span>
                    </p>

                    <div class="mb-3">
                        <label for="payment_amount" class="form-label fw-semibold">Amount Paid</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="payment_amount"
                            id="payment_amount"
                            class="form-control"
                            value="{{ old('payment_amount') }}"
                            required
                        >
                    </div>

                    <p class="mb-3">
                        <strong>Estimated Change:</strong>
                        <span class="text-success fs-5">₱<span id="changeAmount">0.00</span></span>
                    </p>

                    <button type="submit" class="btn btn-primary">
                        Place Order
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-info rounded-4 shadow-sm border-0">
            No food items available right now.
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInputs = document.querySelectorAll('.item-qty');
    const paymentInput = document.getElementById('payment_amount');
    const grandTotalEl = document.getElementById('grandTotal');
    const changeAmountEl = document.getElementById('changeAmount');

    function updateOrderSummary() {
        let total = 0;

        qtyInputs.forEach(function (input) {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            const subtotal = qty * price;

            total += subtotal;

            const cardBody = input.closest('.card-body');
            const subtotalEl = cardBody.querySelector('.item-subtotal');

            if (subtotalEl) {
                subtotalEl.textContent = subtotal.toFixed(2);
            }
        });

        grandTotalEl.textContent = total.toFixed(2);

        const payment = parseFloat(paymentInput.value) || 0;
        const change = payment - total;

        changeAmountEl.textContent = change >= 0 ? change.toFixed(2) : '0.00';
    }

    qtyInputs.forEach(function (input) {
        input.addEventListener('input', updateOrderSummary);
    });

    if (paymentInput) {
        paymentInput.addEventListener('input', updateOrderSummary);
    }

    updateOrderSummary();
});
</script>
@endsection