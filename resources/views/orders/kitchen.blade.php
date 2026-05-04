@extends('layouts.kitchen')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <h5 class="fw-bold mb-3 d-flex align-items-center" style="color: var(--accent-orange);">
            <span class="p-2 rounded-circle me-2" style="background: #FEF3C7;"></span> 
            Pending Orders
        </h5>
        @forelse($pendingOrders as $order)
            <div class="card border-0 shadow-sm rounded-4 mb-3" style="border-top: 4px solid var(--accent-orange) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="h5 fw-bold">Table #{{ $order->table_number }}</span>
                        <span class="badge bg-light text-dark border">{{ $order->created_at->diffForHumans(null, true) }}</span>
                    </div>
                    <ul class="list-unstyled mb-3">
                        @foreach($order->details as $item)
                            <li class="py-1 border-bottom border-light">
                                <span class="fw-bold" style="color: var(--primary-red);">{{ $item->qty }}x</span> {{ $item->food->name }}
                                @if($item->option)
                                    <br><small class="text-muted">({{ $item->option }})</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Preparing">
                        <button class="btn w-100 fw-bold text-white shadow-sm" style="background: var(--accent-orange);">
                            Start Preparing
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center p-5 border rounded-4 border-2" style="border-style: dashed !important; border-color: #FECACA !important;">
                <i class="bi bi-inbox" style="font-size: 2rem; color: #FCA5A5;"></i>
                <p class="text-muted mt-2 mb-0">No pending orders</p>
            </div>
        @endforelse
    </div>

    <div class="col-md-4">
        <h5 class="fw-bold mb-3 d-flex align-items-center" style="color: var(--primary-red);">
            <span class="p-2 rounded-circle me-2" style="background: #FEE2E2;"></span> 
            In Preparation
        </h5>
        @forelse($preparingOrders as $order)
            <div class="card border-0 shadow-sm rounded-4 mb-3" style="border-left: 4px solid var(--primary-red) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="h5 fw-bold">Table #{{ $order->table_number }}</span>
                        <span class="badge bg-light text-dark border">Order #{{ $order->id }}</span>
                    </div>
                    <ul class="list-unstyled mb-3">
                        @foreach($order->details as $item)
                            <li class="py-1 border-bottom border-light">
                                <span class="fw-bold" style="color: var(--primary-red);">{{ $item->qty }}x</span> {{ $item->food->name }}
                                @if($item->option)
                                    <br><small class="text-muted">({{ $item->option }})</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Delivered">
                        <button class="btn w-100 fw-bold text-white shadow-sm" style="background: var(--primary-red);">
                            <i class="bi bi-check-lg me-1"></i> Mark as Ready
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center p-5 border rounded-4 border-2" style="border-style: dashed !important; border-color: #FECACA !important;">
                <i class="bi bi-cup-straw" style="font-size: 2rem; color: #FCA5A5;"></i>
                <p class="text-muted mt-2 mb-0">Nothing cooking</p>
            </div>
        @endforelse
    </div>

    <div class="col-md-4">
        <h5 class="fw-bold mb-3 d-flex align-items-center" style="color: var(--success-green);">
            <span class="p-2 rounded-circle me-2" style="background: #DCFCE7;"></span> 
            Ready for Pickup
        </h5>
        @forelse($readyTables as $table)
            <div class="card border-0 shadow-sm rounded-4 mb-3 text-center" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                <div class="card-body">
                    <h2 class="fw-black mb-1" style="color: var(--success-green);">{{ $table['table_number'] }}</h2>
                    <p class="small fw-bold text-uppercase mb-3" style="letter-spacing: 0.5px;">✓ Order Ready</p>
                    <ul class="list-unstyled text-start mb-3 small">
                        @foreach($table['items'] as $detail)
                            <li class="py-1">
                                <span class="fw-bold" style="color: var(--primary-red);">{{ $detail->qty }}x</span> {{ $detail->food->name }}
                                @if($detail->option)
                                    <br><small class="text-muted">({{ $detail->option }})</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <form method="POST" action="{{ route('orders.updateStatus', $table['table_number']) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Completed">
                        <button class="btn w-100 fw-bold text-white" style="background: var(--success-green);">
                            <i class="bi bi-hand-thumbs-up me-1"></i> Order Served
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center p-5 border rounded-4 border-2" style="border-style: dashed !important; border-color: #BBF7D0 !important;">
                <i class="bi bi-bag-check" style="font-size: 2rem; color: #86EFAC;"></i>
                <p class="text-muted mt-2 mb-0">No items ready</p>
            </div>
        @endforelse
    </div>
</div>
@endsection