@extends('layouts.kitchen')

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold mb-4">Kitchen Board</h2>

    <div class="row">
        {{-- PENDING --}}
        <div class="col-md-4">
            <h4 class="text-warning mb-3">Pending</h4>

            @forelse($pendingOrders as $order)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <strong>Table {{ $order->table_number }}</strong>
                        <div class="small text-muted mb-2">
                            Order #{{ $order->id }}
                        </div>

                        <ul class="mb-3">
                            @foreach($order->details as $detail)
                                <li>
                                    {{ $detail->food->name }}
                                    @if($detail->option)
                                        ({{ $detail->option }})
                                    @endif
                                    × {{ $detail->qty }}
                                </li>
                            @endforeach
                        </ul>

                        <form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
                            @csrf
                            @method('PATCH')
                            <button name="status" value="Preparing" class="btn btn-warning btn-sm w-100">
                                Start Preparing
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-muted">No pending orders.</div>
            @endforelse
        </div>

        {{-- PREPARING --}}
        <div class="col-md-4">
            <h4 class="text-primary mb-3">Preparing</h4>

            @forelse($preparingOrders as $order)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <strong>Table {{ $order->table_number }}</strong>
                        <div class="small text-muted mb-2">
                            Order #{{ $order->id }}
                        </div>

                        <ul class="mb-3">
                            @foreach($order->details as $detail)
                                <li>
                                    {{ $detail->food->name }}
                                    @if($detail->option)
                                        ({{ $detail->option }})
                                    @endif
                                    × {{ $detail->qty }}
                                </li>
                            @endforeach
                        </ul>

                        <form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
                            @csrf
                            @method('PATCH')
                            <button name="status" value="Delivered" class="btn btn-primary btn-sm w-100">
                                Mark Delivered
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-muted">No preparing orders.</div>
            @endforelse
        </div>

        {{-- READY --}}
        <div class="col-md-4">
            <h4 class="text-success mb-3">Ready</h4>

            @forelse($readyTables as $table)
                <div class="card mb-3 shadow-sm bg-light">
                    <div class="card-body">
                        <strong>Table {{ $table['table_number'] }}</strong>
                        <div class="small text-muted mb-2">
                            {{ $table['session_code'] }}
                        </div>

                        <ul class="mb-0">
                            @foreach($table['items'] as $detail)
                                <li>
                                    {{ $detail->food->name }}
                                    @if($detail->option)
                                        ({{ $detail->option }})
                                    @endif
                                    × {{ $detail->qty }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="text-muted">No ready tables.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection