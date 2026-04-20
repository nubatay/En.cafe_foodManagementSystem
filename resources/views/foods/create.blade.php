@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Add Food</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('foods.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Food Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Options</label>
                    <input
                        type="text"
                        name="options"
                        class="form-control"
                        placeholder="Hot,Iced or Small,Large"
                        value="{{ old('options') }}"
                    >
                    <small class="text-muted">
                        Format: Hot:0,Iced:10 or Small:0,Large:20
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Food Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>


                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Save Food</button>
                <a href="{{ route('foods.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection