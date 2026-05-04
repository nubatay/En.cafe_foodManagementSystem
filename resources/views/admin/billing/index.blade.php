@extends('layouts.admin')

@section('content')
<style>
    :root {
        --request-bg: #FFFFFF;
        --table-number: #1E293B;
        --accent-blue: #2563EB;
        --border-color: #E2E8F0;
    }

    .billing-container {
        padding: 1.5rem;
        background: #F8FAFC;
        min-height: 100vh;
    }

    /* Live Pulse Animation */
    .pulse-indicator {
        width: 10px; height: 10px;
        background: #EF4444;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        box-shadow: 0 0 0 rgba(239, 68, 68, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .request-card {
        background: var(--request-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
    }

    .request-card:hover {
        transform: translateY(-3px);
        border-color: var(--accent-blue);
    }

    .table-badge {
        background: var(--table-number);
        color: white;
        width: 45px; height: 45px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px;
        font-weight: 800; font-size: 1.2rem;
    }

    .session-code {
        font-family: 'Monaco', 'Consolas', monospace;
        background: #F1F5F9;
        padding: 2px 8px;
        border-radius: 5px;
        color: #475569; font-size: 0.85rem;
    }

    .total-amount {
        font-size: 1.4rem; font-weight: 800; color: #0F172A;
    }

    .btn-process {
        background: var(--accent-blue);
        color: white; border-radius: 10px;
        font-weight: 700; padding: 0.6rem;
        transition: all 0.3s;
    }

    .btn-process:hover {
        background: #1D4ED8; color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
</style>

<div class="billing-container">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <span class="pulse-indicator"></span>Billing Requests
            </h2>
            <p class="text-muted small mb-0">Active payment requests from tables.</p>
        </div>
        <button onclick="window.location.reload()" class="btn btn-white border shadow-sm rounded-pill px-3 py-2 small">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
    </div>

    @if($sessions->count() > 0)
        <div class="row g-4">
            @foreach($sessions as $s)
                <div class="col-md-6 col-lg-4">
                    <div class="request-card p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="table-badge">
                                    {{ $s->table_number }}
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Session Code</small>
                                    <span class="session-code">{{ $s->session_code }}</span>
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">PENDING</span>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted d-block mb-1">Statement Total</small>
                            <div class="total-amount">₱{{ number_format($s->total, 2) }}</div>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('admin.billing.show', [$s->table_number, $s->session_code]) }}"
                               class="btn btn-process text-center text-decoration-none">
                                <i class="bi bi-cash-stack me-1"></i> Process Payment
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 border border-dashed">
            <i class="bi bi-receipt fs-1 text-muted"></i>
            <h5 class="mt-3 fw-bold text-dark">No pending requests</h5>
            <p class="text-muted">When a customer clicks "Request Bill", it will appear here.</p>
        </div>
    @endif
</div>
@endsection