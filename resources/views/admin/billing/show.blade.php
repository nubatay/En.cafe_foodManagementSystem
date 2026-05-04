@extends('layouts.admin')

@section('content')
<style>
    :root {
        --pos-bg: #F8FAFC;
        --accent-success: #10B981;
        --slate-700: #334155;
    }

    .pos-header {
        background: white;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #E2E8F0;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
    }

    .table-pill {
        background: var(--slate-700);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .billing-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .total-banner {
        background: #F1F5F9;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        border: 2px dashed #CBD5E1;
    }

    .amount-label { color: #64748B; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
    .amount-value { font-size: 3rem; font-weight: 900; color: #1E293B; display: block; line-height: 1; margin-top: 0.5rem; }

    .payment-input {
        font-size: 2rem;
        font-weight: 800;
        text-align: center;
        border: 2px solid var(--accent-success);
        border-radius: 12px;
        padding: 0.8rem;
    }

    .change-box {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        border-radius: 12px;
        padding: 1rem;
    }

    .order-item-row:last-child { border-bottom: none !important; }
</style>

<div class="pos-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
        <span class="table-pill">TABLE {{ $table }}</span>
        <h3 class="fw-bold mb-0">Payment Processing</h3>
    </div>
    <div class="text-end">
        <span class="text-muted small d-block">Session Code</span>
        <span class="fw-bold text-primary">{{ $session }}</span>
    </div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-5 order-lg-2">
            <div class="billing-card p-4 sticky-top" style="top: 1rem;">
                <div class="total-banner mb-4">
                    <span class="amount-label">Grand Total</span>
                    <span class="amount-value">₱{{ number_format($total, 2) }}</span>
                </div>

                <form action="{{ route('admin.billing.pay', [$table, $session]) }}" method="POST" id="payForm">
                    @csrf
                    <div class="mb-4">
                        <label class="amount-label d-block mb-2 text-center">Cash Received</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 fw-bold fs-4">₱</span>
                            <input type="number" step="0.01" min="{{ $total }}"
                                   class="form-control payment-input border-start-0"
                                   id="payment" name="payment"
                                   value="{{ old('payment', $total) }}"
                                   oninput="updateChange()" required>
                        </div>
                    </div>

                    <div class="change-box mb-4 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success">CHANGE DUE</span>
                        <span class="h4 fw-bold text-success mb-0" id="change-text">₱0.00</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg py-3 fw-bold rounded-3">
                            <i class="bi bi-check-all me-2"></i>Confirm & Settle
                        </button>
                        <a href="{{ route('admin.billing.index') }}" class="btn btn-link text-muted fw-bold">Back to Requests</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-7 order-lg-1">
            <h5 class="fw-bold mb-3 d-flex align-items-center">
                <i class="bi bi-receipt me-2"></i>Session Details
            </h5>

            @foreach($orders as $order)
                <div class="card border-0 shadow-sm mb-3 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between">
                        <span class="fw-bold">Order #{{ $order->id }}</span>
                        <span class="badge bg-secondary-subtle text-secondary border px-3">{{ strtoupper($order->status) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th class="ps-4">ITEM</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end pe-4">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $d)
                                    <tr class="order-item-row border-bottom">
                                        <td class="ps-4 py-3 fw-medium">{{ $d->food->name }}</td>
                                        <td class="text-center py-3">{{ $d->qty }}</td>
                                        <td class="text-end pe-4 py-3 fw-bold">₱{{ number_format($d->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function updateChange() {
        const total = {{ $total }};
        const payment = document.getElementById('payment').value || 0;
        const change = payment - total;
        const changeText = document.getElementById('change-text');

        if (change >= 0) {
            changeText.innerText = '₱' + change.toLocaleString(undefined, {minimumFractionDigits: 2});
            changeText.classList.remove('text-danger');
        } else {
            changeText.innerText = 'Insufficient';
            changeText.classList.add('text-danger');
        }
    }

    // Run once on load to init change
    window.onload = updateChange;
</script>
@endsection