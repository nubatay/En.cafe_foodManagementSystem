@extends('layouts.admin')

@section('content')
<style>
    :root {
        --primary: #2563EB;
        --secondary: #1E293B;
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
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Modern Table Styling */
    .table-modern thead th {
        background-color: #F1F5F9;
        color: #64748B;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 1rem;
        border: none;
    }

    .table-modern tbody td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #F1F5F9;
        color: var(--text-main);
        font-size: 0.9rem;
    }

    .table-modern tbody tr:hover {
        background-color: #F8FAFC;
    }

    /* Food Name Styling */
    .food-name { font-weight: 700; color: var(--secondary); }
    .food-category { font-size: 0.8rem; color: #64748B; background: #F1F5F9; padding: 2px 8px; border-radius: 4px; }

    /* Custom Badges */
    .badge-pill {
        padding: 6px 12px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .bg-soft-success { background-color: #DCFCE7; color: #166534; }
    .bg-soft-danger { background-color: #FEE2E2; color: #991B1B; }
    .bg-soft-warning { background-color: #FEF3C7; color: #92400E; }

    /* Action Buttons */
    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: none;
    }
    .btn-edit { background-color: #DBEAFE; color: var(--primary); }
    .btn-edit:hover { background-color: var(--primary); color: white; }
    
    .btn-toggle-on { background-color: #DCFCE7; color: var(--accent); }
    .btn-toggle-on:hover { background-color: var(--accent); color: white; }

    .btn-toggle-off { background-color: #FEE2E2; color: var(--danger); }
    .btn-toggle-off:hover { background-color: var(--danger); color: white; }

    .add-food-btn {
        background-color: var(--primary);
        color: white;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }
    .add-food-btn:hover {
        background-color: #1D4ED8;
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--secondary);">Manage Menu Items</h1>
            <p class="text-muted mb-0">Update your food offerings, adjust stock, and manage availability.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('foods.create') }}" class="btn add-food-btn d-inline-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add New Food
            </a>
        </div>
    </div>

    <div class="inventory-card">
        <div class="card-body p-0">
            @if($foods->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Item Details</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock Level</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($foods as $food)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="food-name">{{ $food->name }}</span>
                                            <small class="text-muted text-truncate" style="max-width: 250px;">{{ $food->description }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="food-category">{{ $food->category }}</span>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        ₱{{ number_format($food->price, 2) }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 fw-semibold">{{ $food->stock }}</span>
                                            @if($food->stock <= 5 && $food->stock > 0)
                                                <span class="text-warning small fw-bold">Low Stock</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if(!$food->is_available)
                                            <span class="badge-pill bg-soft-danger">Inactive</span>
                                        @elseif($food->stock <= 0)
                                            <span class="badge-pill bg-soft-warning">Out of Stock</span>
                                        @else
                                            <span class="badge-pill bg-soft-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('foods.edit', $food->id) }}" class="btn-action btn-edit" title="Edit Item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>

                                            <form action="{{ route('foods.destroy', $food->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                @if($food->is_available)
                                                    <button type="submit" class="btn-action btn-toggle-off" title="Deactivate" onclick="return confirm('Disable this item from the menu?')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn-action btn-toggle-on" title="Activate" onclick="return confirm('Show this item on the menu?')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="opacity-25"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                    </div>
                    <h5 class="fw-bold">No menu items found</h5>
                    <p class="text-muted">Start by adding your first coffee or meal to the menu.</p>
                    <a href="{{ route('foods.create') }}" class="btn btn-primary px-4">Add Food Item</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection