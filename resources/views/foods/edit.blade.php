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

    /* Category */
    .category-radio-group { display: flex; flex-wrap: wrap; gap: 10px; }
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

    /* Update button */
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

    /* Image Picker */
    .image-picker-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        max-height: 280px;
        overflow-y: auto;
        padding: 4px;
    }

    .image-picker-grid::-webkit-scrollbar { width: 5px; }
    .image-picker-grid::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .image-picker-grid::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }

    .img-pick-item input[type="radio"] { display: none; }

    .img-pick-item label {
        display: block;
        border: 2.5px solid #E2E8F0;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s;
        aspect-ratio: 1;
        position: relative;
    }

    .img-pick-item label img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .img-pick-item input[type="radio"]:checked + label {
        border-color: var(--warning);
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25);
    }

    .img-pick-item input[type="radio"]:checked + label::after {
        content: '✓';
        position: absolute;
        top: 5px;
        right: 5px;
        background: var(--warning);
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .img-pick-none label {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background: #F8FAFC;
        color: #94A3B8;
        font-size: 0.75rem;
        font-weight: 500;
        gap: 4px;
        border: 2px dashed #CBD5E1;
        border-radius: 10px;
        cursor: pointer;
        aspect-ratio: 1;
        transition: all 0.2s;
    }

    .img-pick-none input[type="radio"]:checked + label {
        border-color: #94A3B8;
        background: #F1F5F9;
        color: #475569;
    }

    .selected-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #FFFBEB;
        border-radius: 10px;
        margin-top: 10px;
        font-size: 0.82rem;
        color: var(--secondary);
        font-weight: 500;
        min-height: 48px;
    }

    .selected-preview img {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        object-fit: cover;
        border: 1.5px solid #FDE68A;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Edit Item: <span class="text-primary">{{ $food->name }}</span></h2>
                    <p class="text-muted">Modify the details, price, or stock for this menu item.</p>
                </div>
                <a href="{{ route('foods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
            @endif

            {{-- No enctype needed — no file upload --}}
            <form action="{{ route('foods.update', $food->id) }}" method="POST">
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
                                    <span class="d-block fw-bold small mb-1">Format Guide:</span>
                                    <code>Size:0,Large:20</code> (0 = no extra charge)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">

                        {{-- IMAGE PICKER --}}
                        <div class="form-card p-4 mb-4">
                            <h5 class="mb-3 fw-bold border-bottom pb-2">Food Image</h5>

                            @php
                                $currentImage = old('image', $food->image);
                            @endphp

                            @if(count($images) > 0)
                                <div class="image-picker-grid">
                                    {{-- Keep current / no image option --}}
                                    <div class="img-pick-item img-pick-none">
                                        <input type="radio" name="image" id="img_none" value=""
                                            {{ $currentImage === '' || $currentImage === null ? 'checked' : '' }}>
                                        <label for="img_none">
                                            <span style="font-size:1.4rem;">🚫</span>
                                            <span>No Image</span>
                                        </label>
                                    </div>

                                    @foreach($images as $img)
                                        <div class="img-pick-item">
                                            <input type="radio" name="image" id="img_{{ $loop->index }}"
                                                value="{{ $img }}"
                                                {{ $currentImage === $img ? 'checked' : '' }}
                                                onchange="updatePreview('{{ $img }}')">
                                            <label for="img_{{ $loop->index }}" title="{{ $img }}">
                                                <img src="{{ asset('images/' . $img) }}" alt="{{ $img }}">
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Selected preview --}}
                                <div class="selected-preview" id="selectedPreview">
                                    @if($currentImage)
                                        <img src="{{ asset('images/' . $currentImage) }}" id="previewImg">
                                        <span id="previewName">{{ $currentImage }}</span>
                                    @else
                                        <span class="text-muted" id="previewName">No image selected</span>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning rounded-3 mb-0 py-2 small">
                                    No images found in <code>public/images</code>. Add image files there to use the picker.
                                </div>
                            @endif
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
                            <button type="submit" class="btn btn-update shadow-sm">Update Menu Item</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePreview(filename) {
    const preview = document.getElementById('selectedPreview');
    const name    = document.getElementById('previewName');

    const oldImg = document.getElementById('previewImg');
    if (oldImg) oldImg.remove();

    if (filename) {
        const img  = document.createElement('img');
        img.src    = '{{ asset("images/") }}/' + filename;
        img.id     = 'previewImg';
        preview.prepend(img);
        name.textContent = filename;
    } else {
        name.textContent = 'No image selected';
    }
}

document.getElementById('img_none').addEventListener('change', function() {
    updatePreview('');
});
</script>
@endsection