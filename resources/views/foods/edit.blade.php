@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Food</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('foods.update', $food->id) }}" method="POST" enctype="multipart/form-data">
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
                    <label class="form-label">Options</label>
                    <input
                        type="text"
                        name="options"
                        class="form-control"
                        placeholder="Hot,Iced or Small,Large"
                        value="{{ old('options',
                            isset($food->options)
                                ? collect($food->options)->map(function ($opt) {
                                    if (is_array($opt)) {
                                        return ($opt['name'] ?? '') . ':' . ($opt['price'] ?? 0);
                                    } else {
                                        return $opt; // for old format (Hot, Iced)
                                    }
                                })->implode(',')
                                : ''
                        ) }}"
                    <small class="text-muted">
                        Separate options using commas.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Food Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">

                    @if($food->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}" width="120" class="rounded">
                        </div>
                    @endif
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