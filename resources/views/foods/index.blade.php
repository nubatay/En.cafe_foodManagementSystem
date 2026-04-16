@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Foods</h1>
        <a href="{{ route('foods.create') }}" class="btn btn-primary">Add Food</a>
    </div>

    @if($foods->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th width="220">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($foods as $food)
                        <tr>
                            <td>{{ $food->id }}</td>
                            <td>{{ $food->name }}</td>
                            <td>{{ $food->description }}</td>
                            <td>₱{{ number_format($food->price, 2) }}</td>
                            <td>{{ $food->stock }}</td>
                            <td>{{ $food->category }}</td>
                            <td>
                                @if(!$food->is_available)
                                    <span class="badge bg-secondary">Inactive</span>
                                @elseif($food->stock <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @else
                                    <span class="badge bg-success">Available</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('foods.edit', $food->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                @if($food->is_available)
                                    <form action="{{ route('foods.destroy', $food->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Deactivate this food item?')">
                                            Deactivate
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>Inactive</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            No food items found.
        </div>
    @endif
</div>
@endsection