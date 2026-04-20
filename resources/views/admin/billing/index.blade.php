@extends('layouts.admin')

@section('content')
<h2 class="mb-4">Billing Requests</h2>

@if($sessions->count() > 0)
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Table</th>
                <th>Session</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $s)
                <tr>
                    <td>{{ $s->table_number }}</td>
                    <td>{{ $s->session_code }}</td>
                    <td>₱{{ number_format($s->total, 2) }}</td>
                    <td>
                        <a href="{{ route('admin.billing.show', [$s->table_number, $s->session_code]) }}"
                           class="btn btn-primary btn-sm">
                            View
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info">No billing requests yet.</div>
@endif
@endsection