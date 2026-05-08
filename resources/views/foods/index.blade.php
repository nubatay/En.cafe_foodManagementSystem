@extends('layouts.admin')

@section('content')
<style>
    :root {
        --primary: #2563EB;
        --secondary: #1E2937;
        --accent: #22C55E;
        --background: #F8FAFC;
        --text-main: #0F172A;
        --danger: #EF4444;
        --warning: #F59E0B;
    }

    body { background-color: var(--background); color: var(--text-main); }

    /* Card Wrapper */
    .inventory-card {
        background: #ffffff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* --- Refined Search & Action Bar --- */
    .action-header {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .search-group {
        max-width: 450px;
    }

    .search-input {
        border-radius: 10px !important;
        border: 1px solid #E2E8F0;
        padding: 0.6rem 1rem 0.6rem 2.5rem !important;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .search-container {
        position: relative;
    }

    .search-icon-fixed {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 5;
        color: #94A3B8;
        display: flex;
        align-items: center;
    }

    /* Modern Add Button */
    .btn-add-food {
        background-color: var(--primary);
        color: white;
        font-weight: 700;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-add-food:hover {
        background-color: #1D4ED8;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(37, 99, 235, 0.2);
    }

    /* Table Styling */
    .table-modern thead th {
        background-color: #F8FAFC;
        color: #64748B;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 1rem;
        border-bottom: 1px solid #EDF2F7;
    }

    .table-modern tbody td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.95rem;
    }

    .food-name { font-weight: 700; color: var(--secondary); display: block; }
    .food-category { font-size: 0.8rem; color: #475569; background: #F1F5F9; padding: 3px 10px; border-radius: 6px; }

    /* Action Icons */
    .btn-action {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: 0.2s;
        border: none;
    }
    .btn-edit { background: #EFF6FF; color: var(--primary); }
    .btn-edit:hover { background: var(--primary); color: white; }
    
    .btn-delete { background: #FFF1F2; color: var(--danger); }
    .btn-delete:hover { background: var(--danger); color: white; }

    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    .status-active { background: #DCFCE7; color: #166534; }
    .status-inactive { background: #FEE2E2; color: #991B1B; }
</style>

<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Manage Menu Items</h1>
        <p class="text-muted">Update food offerings and track stock levels.</p>
    </div>

    <div class="action-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="search-container flex-grow-1" style="max-width: 400px;">
            <div class="search-icon-fixed">
                <i class="bi bi-search"></i>
            </div>
            <input type="text" id="foodSearch" class="form-control search-input" placeholder="Search by name or category...">
        </div>

        <a href="{{ route('foods.create') }}" class="btn-add-food shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>Add New Food</span>
        </a>
    </div>

    <div class="inventory-card">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0" id="foodTable">
                <thead>
                    <tr>
                        <th class="ps-4">Item Details</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($foods as $food)
                        <tr class="food-row">
                            <td class="ps-4">
                                <div>
                                    <span class="food-name">{{ $food->name }}</span>
                                    <small class="text-muted">{{ Str::limit($food->description, 40) }}</small>
                                </div>
                            </td>
                            <td><span class="food-category">{{ $food->category }}</span></td>
                            <td class="fw-bold text-dark">₱{{ number_format($food->price, 2) }}</td>
                            <td>
                                <span class="fw-semibold {{ $food->stock <= 5 ? 'text-danger' : '' }}">
                                    {{ $food->stock }}
                                </span>
                                @if($food->stock <= 5) <small class="text-danger d-block" style="font-size: 0.7rem;">Low</small> @endif
                            </td>
                            <td>
                                <span class="badge-status {{ $food->is_available ? 'status-active' : 'status-inactive' }}">
                                    {{ $food->is_available ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('foods.edit', $food->id) }}" class="btn-action btn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('foods.destroy', $food->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" onclick="return confirm('Change status?')" title="Toggle Availability">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="text-muted mb-0">No items found in your menu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Live Search Filter
    document.getElementById('foodSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#foodTable tbody .food-row');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? "" : "none";
        });
    });
</script>
@endsection