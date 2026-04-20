@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 500px;">
        <div class="card-body p-4">
            <h2 class="mb-4 fw-bold">Set Table Number</h2>

            <form action="{{ route('table.setup.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="table_number" class="form-label">Table Number</label>
                    <input
                        type="number"
                        name="table_number"
                        id="table_number"
                        class="form-control"
                        min="1"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Continue to Menu
                </button>
            </form>
        </div>
    </div>
</div>
@endsection