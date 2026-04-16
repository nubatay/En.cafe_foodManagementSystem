@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Food</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('foods.update', $food->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Food Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $food->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $food->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $food->price) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock', $food->stock) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $food->category) }}" required>
                </div>

                <button type="submit" class="btn btn-warning">Update Food</button>
                <a href="{{ route('foods.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection