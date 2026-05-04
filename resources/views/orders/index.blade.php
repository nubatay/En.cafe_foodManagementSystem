@extends('layouts.admin')

@section('content')
<style>
    :root {
        --primary: #2563EB;
        --secondary: #1E293B;
        --background: #F8FAFC;
        --border-color: #E2E8F0;
    }

    .order-container {
        padding: 2rem;
        background: var(--background);
    }

    .page-title {
        font-weight: 800;
        color: var(--secondary);
        margin-bottom: 1.5rem;
    }

    /* Table Styling */
    .orders-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-custom { margin-bottom: 0; }
    .table-custom thead th {
        background: #F1F5F9;
        color: #64748B;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        padding: 1rem;
        border: none;
    }

    .order-main-row {
        cursor: pointer;
        transition: all 0.2s;
    }

    .order-main-row:hover {
        background-color: #F8FAFC;
    }

    .order-main-row.is-active {
        background-color: #EFF6FF;
    }

    .order-main-row td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
    }

    /* Detail Row Styling */
    .detail-row {
        background-color: #F8FAFC;
    }

    .detail-content-box {
        padding: 1.5rem 3rem;
    }

    .receipt-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Badges */
    .badge-status {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .bg-success-soft { background: #DCFCE7; color: #15803d; }
    .bg-warning-soft { background: #FEF3C7; color: #b45309; }

    .item-pill {
        background: #E2E8F0;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        margin-right: 4px;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--secondary);">Order History</h1>
            <p class="text-muted">Monitor and review all transactions from En.cafe</p>
        </div>
    </div>

    <div class="orders-card">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th class="ps-4">Order ID</th>
                    <th>Customer / Date</th>
                    <th>Items Ordered</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="order-main-row" id="order-row-{{ $order->id }}" onclick="toggleOrder('{{ $order->id }}')">
                        <td class="ps-4">
                            <span class="text-primary fw-bold">#{{ $order->id }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $order->user->name ?? 'Guest' }}</div>
                            <div class="text-muted small">{{ $order->created_at->format('M d, Y • h:i A') }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap" style="max-width: 250px;">
                                @foreach($order->details as $detail)
                                    <span class="item-pill">{{ $detail->qty }}x {{ Str::limit($detail->food->name ?? 'Item', 10) }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">₱{{ number_format($order->total_price, 2) }}</div>
                            <small class="text-muted">via {{ $order->payment_method ?? 'Cash' }}</small>
                        </td>
                        <td>
                            <span class="badge-status {{ $order->billing_status == 'Paid' ? 'bg-success-soft' : 'bg-warning-soft' }}">
                                {{ strtoupper($order->billing_status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-light btn-sm border fw-bold px-3">View Details</button>
                        </td>
                    </tr>

                    <tr id="details-{{ $order->id }}" class="detail-row" style="display: none;">
                        <td colspan="6" class="p-0">
                            <div class="detail-content-box">
                                <div class="receipt-card">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h5 class="fw-bold mb-1">Transaction Summary</h5>
                                            <div class="text-muted small">Session: <span class="text-primary">{{ $order->session_code }}</span></div>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <button class="btn btn-dark btn-sm px-4" 
                                                onclick="printSpecificOrder(event, '{{ $order->id }}', '{{ $order->session_code }}', '{{ $order->total_price }}', '{{ $order->payment_amount }}', '{{ $order->change_amount }}', {{ json_encode($order->details->map(fn($d) => ['name' => $d->food->name ?? 'Item', 'qty' => $d->qty, 'price' => $d->price])) }})">
                                                Print Receipt
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-3">
                                            <label class="text-muted small d-block">Payment Amount</label>
                                            <span class="fw-bold">₱{{ number_format($order->payment_amount, 2) }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="text-muted small d-block">Change Provided</label>
                                            <span class="fw-bold">₱{{ number_format($order->change_amount, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="border-bottom">
                                                <tr class="text-muted small">
                                                    <th>ITEM DESCRIPTION</th>
                                                    <th class="text-center">PRICE</th>
                                                    <th class="text-center">QTY</th>
                                                    <th class="text-end">SUBTOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->details as $detail)
                                                <tr>
                                                    <td class="py-3 fw-semibold">{{ $detail->food->name ?? 'Item' }}</td>
                                                    <td class="py-3 text-center">₱{{ number_format($detail->price, 2) }}</td>
                                                    <td class="py-3 text-center">{{ $detail->qty }}</td>
                                                    <td class="py-3 text-end fw-bold">₱{{ number_format($detail->subtotal, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* This CSS ensures only the receipt is visible when printing */
    @media print {
        body * { visibility: hidden; }
        #print-receipt-section, #print-receipt-section * { visibility: visible; }
        #print-receipt-section { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 80mm; /* Standard receipt width */
            padding: 10px;
        }
    }
</style>

<script>
    /**
     * Toggles the order details row visibility
     * Uses style.display directly to avoid Bootstrap JavaScript conflicts
     */
    function toggleOrder(orderId) {
        const detailRow = document.getElementById('details-' + orderId);
        const mainRow = document.getElementById('order-row-' + orderId);

        if (detailRow.style.display === 'none' || detailRow.style.display === '') {
            // Open the row
            detailRow.style.display = 'table-row';
            mainRow.classList.add('is-active');
        } else {
            // Close the row
            detailRow.style.display = 'none';
            mainRow.classList.remove('is-active');
        }
    }

    function printSpecificOrder(event, id, session, total, paid, change, items) {
        // 1. Prevent the table row from closing
        event.stopPropagation();

        // 2. Create a hidden printing div if it doesn't exist
        let printDiv = document.getElementById('print-receipt-section');
        if (!printDiv) {
            printDiv = document.createElement('div');
            printDiv.id = 'print-receipt-section';
            document.body.appendChild(printDiv);
        }

        // 3. Build the receipt HTML
        let itemsHtml = items.map(item => `
            <div style="display: flex; justify-content: space-between; font-size: 12px;">
                <span>${item.qty}x ${item.name}</span>
                <span>₱${parseFloat(item.price * item.qty).toFixed(2)}</span>
            </div>
        `).join('');

        printDiv.innerHTML = `
            <div style="text-align: center; font-family: 'Courier New', Courier, monospace;">
                <h3>EN.CAFE</h3>
                <p>Order ID: #${id}<br>Session: ${session}</p>
                <hr style="border-top: 1px dashed #000;">
                ${itemsHtml}
                <hr style="border-top: 1px dashed #000;">
                <div style="display: flex; justify-content: space-between; font-weight: bold;">
                    <span>TOTAL:</span>
                    <span>₱${parseFloat(total).toFixed(2)}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Paid:</span>
                    <span>₱${parseFloat(paid).toFixed(2)}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Change:</span>
                    <span>₱${parseFloat(change).toFixed(2)}</span>
                </div>
                <p style="margin-top: 20px;">Thank you for visiting!</p>
                <p>${new Date().toLocaleString()}</p>
            </div>
        `;

        // 4. Trigger the print dialog
        window.print();
        
        // 5. Clean up after print dialog closes
        printDiv.innerHTML = '';
    }
</script>
@endsection