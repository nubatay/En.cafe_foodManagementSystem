@extends('layouts.admin')

@section('content')
<style>
    :root {
        --primary: #2563EB;
        --secondary: #1E293B;
        --background: #F8FAFC;
    }

    .form-card {
        background: #ffffff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .form-label {
        font-weight: 600;
        color: var(--secondary);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .input-group-text {
        background-color: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-right: none;
        border-radius: 12px 0 0 12px;
        color: var(--secondary);
        font-weight: 600;
    }

    .input-group .form-control {
        border-radius: 0 12px 12px 0;
    }

    .category-radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .category-item {
        flex: 1 1 calc(33.333% - 10px);
        min-width: 120px;
    }

    .category-item input {
        display: none;
    }

    .category-label {
        display: block;
        padding: 10px;
        text-align: center;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
        background: white;
    }

    .category-item input:checked + .category-label {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .btn-save {
        background-color: var(--primary);
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s;
    }

    .btn-save:hover {
        background-color: #1D4ED8;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
    }

    .helper-box {
        background-color: #EFF6FF;
        border-left: 4px solid var(--primary);
        padding: 15px;
        border-radius: 0 8px 8px 0;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('foods.index') }}" class="text-decoration-none">Menu</a></li>
                            <li class="breadcrumb-item active">Add New Item</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold text-dark">Create Food Item</h2>
                </div>
                <a href="{{ route('foods.index') }}" class="btn btn-light border-0 px-4 py-2 rounded-pill fw-bold text-muted">
                    ← Back to Menu
                </a>
            </div>

            <form action="{{ route('foods.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="form-card p-4 h-100">
                            <h5 class="mb-4 fw-bold">Basic Information</h5>
                            
                            <div class="mb-4">
                                <label class="form-label">Food Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Spanish Latte" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Briefly describe this item for the customers...">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Options & Customization</label>
                                <input type="text" name="options" class="form-control" placeholder="Hot:0,Iced:10" value="{{ old('options') }}">
                                <div class="helper-box mt-2">
                                    <strong>Format:</strong> Label:AddonPrice, Label:AddonPrice<br>
                                    <span class="text-muted">Example: <em>Small:0,Large:20</em> or <em>Hot:0,Iced:10</em></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-card p-4 mb-4">
                            <h5 class="mb-4 fw-bold">Pricing & Stock</h5>
                            
                            <div class="mb-4">
                                <label class="form-label">Base Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="0.00" value="{{ old('price') }}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Initial Stock Level</label>
                                <input type="number" min="0" name="stock" class="form-control" placeholder="Quantity in kitchen" value="{{ old('stock') }}" required>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Food Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1">High-quality JPG/PNG (Recommended 800x800px)</small>
                            </div>
                        </div>

                        <div class="form-card p-4">
                            <h5 class="mb-4 fw-bold">Category</h5>
                            <div class="category-radio-group">
                                @php $categories = ['Coffee', 'Drinks', 'Soda', 'Pasta', 'Bread', 'Dessert', 'Snacks']; @endphp
                                @foreach($categories as $cat)
                                    <div class="category-item">
                                        <input type="radio" name="category" id="cat_{{ $cat }}" value="{{ $cat }}" {{ old('category') == $cat ? 'checked' : '' }} required>
                                        <label for="cat_{{ $cat }}" class="category-label">{{ $cat }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="form-card p-3 d-flex justify-content-end gap-2 bg-light">
                            <a href="{{ route('foods.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold px-4">Discard Changes</a>
                            <button type="submit" class="btn btn-primary btn-save px-5 text-white">Save Item to Menu</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection