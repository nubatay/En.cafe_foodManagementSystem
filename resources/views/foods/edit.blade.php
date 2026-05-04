@extends('layouts.admin')

@section('content')
<style>
    :root {
        --primary: #2563EB;
        --secondary: #1E293B;
        --warning: #F59E0B;
        --background: #F8FAFC;
    }

    .form-card {
        background: #ffffff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .form-label {
        font-weight: 600;
        color: var(--secondary);
        font-size: 0.9rem;
    }

    .form-control {
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
    }

    .form-control:focus {
        border-color: var(--warning);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
    }

    /* Category Selection Grid */
    .category-radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .category-item { flex: 1 1 calc(33.333% - 10px); min-width: 110px; }
    .category-item input { display: none; }
    .category-label {
        display: block;
        padding: 10px;
        text-align: center;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .category-item input:checked + .category-label {
        background-color: var(--secondary);
        border-color: var(--secondary);
        color: white;
        font-weight: 600;
    }

    /* Image Preview */
    .current-image-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        border-radius: 15px;
        overflow: hidden;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .btn-update {
        background-color: var(--warning);
        color: #000;
        font-weight: 700;
        border: none;
        padding: 12px 40px;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .btn-update:hover {
        background-color: #D97706;
        transform: translateY(-2px);
    }

    .helper-text { font-size: 0.75rem; color: #64748B; margin-top: 4px; }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Edit Item: <span class="text-primary">{{ $food->name }}</span></h2>
                    <p class="text-muted">Modify the details, price, or stock for this menu item.</p>
                </div>
                <a href="{{ route('foods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Cancel
                </a>
            </div>

            <form action="{{ route('foods.update', $food->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="form-card p-4 mb-4">
                            <h5 class="mb-4 fw-bold border-bottom pb-2">Item Details</h5>
                            
                            <div class="mb-4">
                                <label class="form-label">Food Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $food->name) }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $food->description) }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Customization Options</label>
                                <input type="text" name="options" class="form-control" 
                                    value="{{ old('options', isset($food->options) ? collect($food->options)->map(function ($opt) {
                                        return is_array($opt) ? ($opt['name'] ?? '') . ':' . ($opt['price'] ?? 0) : $opt;
                                    })->implode(',') : '') }}">
                                <div class="p-3 bg-light rounded-3 mt-2">
                                    <span class="d-block fw-bold small mb-1">Current Format Guide:</span>
                                    <code>Size:0,Large:20</code> (0 means no extra charge)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-card p-4 mb-4">
                            <h5 class="mb-4 fw-bold border-bottom pb-2">Product Media</h5>
                            <div class="d-flex align-items-start gap-4 mb-4">
                                @if($food->image)
                                    <div>
                                        <label class="form-label d-block">Current Image</label>
                                        <div class="current-image-wrapper">
                                            <img src="{{ asset('storage/' . $food->image) }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <label class="form-label">Upload New</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <p class="helper-text">Leave blank to keep current photo.</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-card p-4">
                            <h5 class="mb-4 fw-bold border-bottom pb-2">Logistics</h5>
                            
                            <div class="row">
                                <div class="col-6 mb-4">
                                    <label class="form-label">Base Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">₱</span>
                                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $food->price) }}" required>
                                    </div>
                                </div>
                                <div class="col-6 mb-4">
                                    <label class="form-label">Current Stock</label>
                                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $food->stock) }}" required>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Category</label>
                                <div class="category-radio-group">
                                    @php $categories = ['Coffee', 'Drinks', 'Soda', 'Pasta', 'Bread', 'Dessert', 'Snacks']; @endphp
                                    @foreach($categories as $cat)
                                        <div class="category-item">
                                            <input type="radio" name="category" id="cat_{{ $cat }}" value="{{ $cat }}" 
                                                {{ old('category', $food->category) == $cat ? 'checked' : '' }} required>
                                            <label for="cat_{{ $cat }}" class="category-label">{{ $cat }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="form-card p-4 d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i> Last updated: {{ $food->updated_at->diffForHumans() }}
                            </div>
                            <button type="submit" class="btn btn-update shadow-sm">
                                Update Menu Item
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection