@extends('layouts.customer')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="fw-bold mb-1">Menu</h1>
        <p class="text-muted mb-0">
            Select your food and review your order.
        </p>
    </div>

    <div class="d-flex gap-2 align-items-center">
        @if(session('table_number'))
            <span class="badge bg-primary p-2" style="font-size: 0.9rem;">
                Table {{ session('table_number') }}
            </span>
        @endif
        <a href="{{ route('orders.my') }}" class="btn btn-outline-primary">
            My Orders
        </a>
    </div>
</div>

<div class="mb-4 d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-dark category-btn active" data-category="all">All</button>
    <button type="button" class="btn btn-outline-dark category-btn" data-category="coffee">Coffee</button>
    <button type="button" class="btn btn-outline-dark category-btn" data-category="drinks">Drinks</button>
    <button type="button" class="btn btn-outline-dark category-btn" data-category="soda">Soda</button>
    <button type="button" class="btn btn-outline-dark category-btn" data-category="pasta">Pasta</button>
    <button type="button" class="btn btn-outline-dark category-btn" data-category="bread">Bread</button>
    <button type="button" class="btn btn-outline-dark category-btn" data-category="dessert">Dessert</button>
    <button type="button" class="btn btn-outline-dark category-btn" data-category="snacks">Snacks</button>
</div>

<form action="{{ route('orders.store') }}" method="POST" id="orderForm">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            <div class="row" id="menuItems">
                @foreach($foods as $index => $food)
                    <div class="col-md-6 mb-4 food-card" data-category="{{ strtolower($food->category) }}">
                        <div class="card-clean h-100 overflow-hidden">
                            <img
                                src="{{ $food->image ? asset('storage/' . $food->image) : 'https://via.placeholder.com/600x350?text=En.cafe+Food' }}"
                                alt="{{ $food->name }}"
                                style="height:220px; width:100%; object-fit:cover;"
                            >

                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-2">{{ $food->name }}</h5>
                                <p class="soft-text small mb-2">{{ $food->description }}</p>

                                <p class="mb-1"><strong>Category:</strong> {{ ucfirst($food->category) }}</p>
                                <p class="mb-1"><strong>Base Price:</strong> ₱{{ number_format($food->price, 2) }}</p>
                                <p class="mb-3"><strong>Stock:</strong> {{ $food->stock }}</p>

                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $food->id }}">

                                @if(!empty($food->options))
                                    <label class="form-label fw-semibold mt-2">Size / Option</label>

                                    <div class="d-flex flex-wrap gap-2 mb-3">

                                        @php
                                            $options = is_array($food->options)
                                                ? $food->options
                                                : explode(',', $food->options);
                                        @endphp

                                        @foreach($options as $optIndex => $opt)

                                            @php
                                                // HANDLE BOTH FORMATS
                                                if (is_array($opt)) {
                                                    $optName = $opt['name'] ?? '';
                                                    $optPrice = $opt['price'] ?? 0;
                                                } else {
                                                    $optName = trim($opt);
                                                    $optPrice = 0;
                                                }
                                            @endphp

                                            <input
                                                type="radio"
                                                class="btn-check item-option"
                                                name="items[{{ $index }}][option]"
                                                id="option_{{ $food->id }}_{{ $optIndex }}"
                                                value="{{ $optName }}"
                                                data-extra="{{ $optPrice }}"
                                                autocomplete="off"
                                                {{ $optIndex === 0 ? 'checked' : '' }}
                                            >

                                            <label
                                                class="btn btn-outline-dark btn-sm"
                                                for="option_{{ $food->id }}_{{ $optIndex }}"
                                            >
                                                {{ $optName }}
                                                @if($optPrice > 0)
                                                    (+₱{{ number_format($optPrice, 2) }})
                                                @endif
                                            </label>

                                        @endforeach
                                    </div>
                                @endif

                                <label for="qty_{{ $food->id }}" class="form-label fw-semibold">Quantity</label>
                                <input
                                    type="number"
                                    id="qty_{{ $food->id }}"
                                    name="items[{{ $index }}][qty]"
                                    class="form-control item-qty"
                                    min="0"
                                    max="{{ $food->stock }}"
                                    value="0"
                                    data-price="{{ $food->price }}"
                                    data-name="{{ $food->name }}"
                                >
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-clean sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3">Selected Order</h4>

                    <div id="selectedItems">
                        <p class="text-muted mb-3">No items selected yet.</p>
                    </div>

                    <hr>

                    <p class="mb-3">
                        <strong>Total:</strong>
                        <span class="fs-5 text-primary">₱<span id="grandTotal">0.00</span></span>
                    </p>

                    <button type="submit" class="btn btn-primary w-100">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInputs = document.querySelectorAll('.item-qty');
    const selectedItemsEl = document.getElementById('selectedItems');
    const grandTotalEl = document.getElementById('grandTotal');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const foodCards = document.querySelectorAll('.food-card');

    function updateOrderPreview() {
        let total = 0;
        let selectedHtml = '';
        let hasSelected = false;

        qtyInputs.forEach(function (input) {
            const qty = parseInt(input.value) || 0;
            const basePrice = parseFloat(input.dataset.price) || 0;
            const name = input.dataset.name || '';

            const cardBody = input.closest('.card-body');
            const selectedOption = cardBody.querySelector('.item-option:checked');

            let optionText = '';
            let extraPrice = 0;

            if (selectedOption) {
                optionText = selectedOption.value;
                extraPrice = parseFloat(selectedOption.dataset.extra) || 0;
            }

            const finalPrice = basePrice + extraPrice;
            const subtotal = qty * finalPrice;

            if (qty > 0) {
                hasSelected = true;
                total += subtotal;

                selectedHtml += `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold">${name}</div>
                            ${optionText ? `<div class="text-muted small">Option: ${optionText}</div>` : ''}
                            <div class="text-muted small">Qty: ${qty}</div>
                            <div class="text-muted small">Unit Price: ₱${finalPrice.toFixed(2)}</div>
                        </div>
                        <div class="fw-semibold">₱${subtotal.toFixed(2)}</div>
                    </div>
                `;
            }
        });

        selectedItemsEl.innerHTML = hasSelected
            ? selectedHtml
            : '<p class="text-muted mb-3">No items selected yet.</p>';

        grandTotalEl.textContent = total.toFixed(2);
    }

    qtyInputs.forEach(function (input) {
        input.addEventListener('input', updateOrderPreview);
    });

    document.querySelectorAll('.item-option').forEach(function (option) {
        option.addEventListener('change', updateOrderPreview);
    });

    categoryButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedCategory = this.dataset.category;

            categoryButtons.forEach(btn => {
                btn.classList.remove('btn-dark', 'active');
                btn.classList.add('btn-outline-dark');
            });

            this.classList.remove('btn-outline-dark');
            this.classList.add('btn-dark', 'active');

            foodCards.forEach(function (card) {
                if (selectedCategory === 'all' || card.dataset.category === selectedCategory) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    updateOrderPreview();
});
</script>
@endsection