@extends('layouts.customer')

@section('content')
<div class="hero-card p-5 mb-4 text-center">
    <h1 class="display-5 fw-bold mb-3">Welcome to En.cafe</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card-clean">
            <div class="card-body p-4 text-center">
                @if(session('table_number'))
                    <div class="mb-4">
                        <h3 class="fw-bold mb-3">You are ordering for</h3>
                        <div class="badge bg-primary p-3" style="font-size: 1.5rem;">
                            Table {{ session('table_number') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg w-100">
                            Start Ordering
                        </a>
                    </div>

                    <div>
                        <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary w-100">
                            View My Orders
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning rounded-4" role="alert">
                        <h5 class="fw-bold mb-2">No Table Assigned</h5>
                        <p class="mb-0">Please ask staff to provide you with a table QR code or table URL.</p>
                    </div>

                    <p class="text-muted mt-4">
                        Once your table is assigned, you'll be able to start ordering.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection