@extends('layouts.customer')

@section('content')
<style>
    :root {
        --primary: #DC2626;
        --accent: #FACC15;
        --dark: #7F1D1D;
        --bg-warm: #FFF7ED;
    }

    body { background-color: var(--bg-warm); font-family: 'Inter', sans-serif; }

    /* Category Buttons Styling */
    .category-btn {
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid var(--dark);
        color: var(--dark);
    }
    .category-btn.active, .category-btn:hover {
        background-color: var(--dark) !important;
        color: var(--accent) !important;
        border-color: var(--dark);
    }

    /* Food Card Enhancements */
    .card-clean {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 25px rgba(127, 29, 29, 0.05);
        transition: transform 0.3s ease, border 0.3s ease;
    }
    .food-card.has-qty .card-clean {
        border: 2px solid var(--primary);
    }

    /* Stepper Controls */
    .qty-stepper {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #dee2e6;
    }
    .stepper-btn {
        background: none;
        border: none;
        padding: 8px 15px;
        color: var(--primary);
        font-weight: bold;
        transition: background 0.2s;
    }
    .stepper-btn:hover { background: #e9ecef; }
    .item-qty {
        width: 50px;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: bold;
    }

    /* Summary Sidebar */
    .order-summary-card {
        border-radius: 24px;
        background: white;
        border-top: 5px solid var(--primary);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    .badge-table {
        background-color: var(--accent);
        color: var(--dark);
        font-weight: 800;
        border-radius: 8px;
    }

    .text-primary { color: var(--primary) !important; }
    .btn-primary { background-color: var(--primary); border: none; font-weight: 700; border-radius: 12px; padding: 12px; }
    .btn-primary:hover { background-color: var(--dark); }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: var(--dark);">Our Menu</h1>
            <p class="text-muted mb-0">Handcrafted flavors, just for you.</p>
        </div>

        <div class="d-flex gap-2 align-items-center">
           
            <a href="{{ route('orders.my') }}" class="btn btn-outline-dark rounded-pill fw-bold px-4">
                My Orders
            </a>
        </div>
    </div>

    <div class="mb-5 d-flex flex-wrap gap-2 overflow-auto pb-2" style="white-space: nowrap;">
        <button type="button" class="btn category-btn active" data-category="all">All Items</button>
        @foreach(['Coffee', 'Drinks', 'Soda', 'Pasta', 'Bread', 'Dessert', 'Snacks'] as $cat)
            <button type="button" class="btn category-btn" data-category="{{ strtolower($cat) }}">{{ $cat }}</button>
        @endforeach
    </div>

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row" id="menuItems">
                    @foreach($foods as $index => $food)
                        <div class="col-md-6 mb-4 food-card" data-category="{{ strtolower($food->category) }}">
                            <div class="card-clean h-100 overflow-hidden">
                                <div class="position-relative">
                                    <img src="{{ $food->image ? asset('storage/' . $food->image) : 'https://via.placeholder.com/600x350?text=En.cafe' }}"
                                         alt="{{ $food->name }}" style="height:200px; width:100%; object-fit:cover;">
                                    <div class="position-absolute bottom-0 start-0 m-3">
                                        <span class="badge bg-white text-dark shadow-sm fw-bold">₱{{ number_format($food->price, 2) }}</span>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    <h5 class="fw-bold mb-1 text-dark">{{ $food->name }}</h5>
                                    <p class="text-muted small mb-3" style="height: 40px; overflow: hidden;">{{ $food->description }}</p>

                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $food->id }}">

                                    @if(!empty($food->options))
                                        <div class="mb-3">
                                            <small class="fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Select Option</small>
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                @php
                                                    $options = is_array($food->options) ? $food->options : explode(',', $food->options);
                                                @endphp
                                                @foreach($options as $optIndex => $opt)
                                                    @php
                                                        $optName = is_array($opt) ? ($opt['name'] ?? '') : trim($opt);
                                                        $optPrice = is_array($opt) ? ($opt['price'] ?? 0) : 0;
                                                    @endphp
                                                    <input type="radio" class="btn-check item-option" 
                                                           name="items[{{ $index }}][option]" 
                                                           id="opt_{{ $food->id }}_{{ $optIndex }}" 
                                                           value="{{ $optName }}" data-extra="{{ $optPrice }}" {{ $optIndex === 0 ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-dark btn-sm rounded-pill" for="opt_{{ $food->id }}_{{ $optIndex }}">
                                                        {{ $optName }} @if($optPrice > 0) +₱{{ $optPrice }} @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <small class="text-muted">Stock: {{ $food->stock }}</small>
                                        <div class="qty-stepper">
                                            <button type="button" class="stepper-btn minus">-</button>
                                            <input type="number" name="items[{{ $index }}][qty]" 
                                                   class="item-qty" value="0" min="0" max="{{ $food->stock }}" 
                                                   data-price="{{ $food->price }}" data-name="{{ $food->name }}">
                                            <button type="button" class="stepper-btn plus">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card order-summary-card sticky-top p-2" style="top: 20px;">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4" style="color: var(--dark);">My Basket</h4>
                        <div id="selectedItems" class="mb-4" style="max-height: 400px; overflow-y: auto;">
                            <div class="text-center py-4">
                                <i class="bi bi-cart text-muted fs-1 opacity-25"></i>
                                <p class="text-muted mt-2">Your basket is empty</p>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold h5">Total</span>
                                <span class="h5 fw-bold text-primary">₱<span id="grandTotal">0.00</span></span>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm">
                                Confirm & Place Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInputs = document.querySelectorAll('.item-qty');
    const selectedItemsEl = document.getElementById('selectedItems');
    const grandTotalEl = document.getElementById('grandTotal');

    function updateOrderPreview() {
        let total = 0;
        let selectedHtml = '';
        let hasSelected = false;

        qtyInputs.forEach(function (input) {
            const qty = parseInt(input.value) || 0;
            const card = input.closest('.food-card');
            
            // Add visual feedback class
            if(qty > 0) card.classList.add('has-qty');
            else card.classList.remove('has-qty');

            if (qty > 0) {
                const basePrice = parseFloat(input.dataset.price) || 0;
                const name = input.dataset.name || '';
                const selectedOption = card.querySelector('.item-option:checked');
                const extraPrice = selectedOption ? parseFloat(selectedOption.dataset.extra) || 0 : 0;
                
                const finalPrice = basePrice + extraPrice;
                const subtotal = qty * finalPrice;
                total += subtotal;
                hasSelected = true;

                selectedHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded-3 bg-light">
                        <div style="flex: 1;">
                            <div class="fw-bold small text-dark">${name}</div>
                            <div class="text-muted small">${qty}x @ ₱${finalPrice.toFixed(2)}</div>
                        </div>
                        <div class="fw-bold text-primary">₱${subtotal.toFixed(2)}</div>
                    </div>`;
            }
        });

        selectedItemsEl.innerHTML = hasSelected ? selectedHtml : '<div class="text-center py-4"><p class="text-muted mt-2">Your basket is empty</p></div>';
        grandTotalEl.textContent = total.toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    // Stepper Logic
    document.querySelectorAll('.qty-stepper').forEach(stepper => {
        const input = stepper.querySelector('.item-qty');
        stepper.querySelector('.plus').addEventListener('click', () => {
            if(parseInt(input.value) < parseInt(input.max)) {
                input.value = parseInt(input.value) + 1;
                updateOrderPreview();
            }
        });
        stepper.querySelector('.minus').addEventListener('click', () => {
            if(parseInt(input.value) > 0) {
                input.value = parseInt(input.value) - 1;
                updateOrderPreview();
            }
        });
    });

    // Option Change Logic
    document.querySelectorAll('.item-option').forEach(opt => {
        opt.addEventListener('change', updateOrderPreview);
    });

    // Category Filtering
    document.querySelectorAll('.category-btn').forEach(button => {
        button.addEventListener('click', function () {
            const cat = this.dataset.category;
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.food-card').forEach(card => {
                card.style.display = (cat === 'all' || card.dataset.category === cat) ? '' : 'none';
            });
        });
    });

    updateOrderPreview();
});
</script>
@endsection