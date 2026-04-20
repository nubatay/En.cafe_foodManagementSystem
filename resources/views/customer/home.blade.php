@extends('layouts.customer')

@section('content')
<div class="hero-card p-5 mb-4 text-center">
    <h1 class="display-5 fw-bold mb-3">Welcome to En.cafe</h1>
    <p class="fs-5 mb-0" style="color: rgba(255,255,255,0.85);">
        Please enter your table number to start ordering.
    </p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card-clean">
            <div class="card-body p-4">
                @if(session('table_number'))
                    <div class="alert alert-info rounded-4">
                        Current Table Number:
                        <strong>{{ session('table_number') }}</strong>
                    </div>
                @endif

                <form action="{{ route('table.setup.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="table_number" class="form-label fw-semibold">Table Number</label>
                        <input
                            type="number"
                            name="table_number"
                            id="table_number"
                            class="form-control"
                            min="1"
                            value="{{ session('table_number') }}"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Confirm Table and Start Ordering
                    </button>
                </form>

                @if(session('table_number'))
                    <div class="mt-3 text-center">
                        <a href="{{ route('menu.index') }}" class="btn btn-outline-dark">
                            Continue to Menu
                        </a>
                    </div>
                @endif

                <div class="mt-3 text-center">
                    <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary">
                        View My Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection