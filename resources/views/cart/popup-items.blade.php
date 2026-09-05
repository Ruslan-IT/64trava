@forelse($cart as $variantId => $item)

    @php
        $variant = $variants[$variantId] ?? null;
    @endphp

    @if($variant)

        @php
            $product = $variant->product;
            $quantity = $item['quantity'];
            $price = $item['price'];
            $itemTotal = $price * $quantity;
        @endphp

        <div
            class="cart-item"
            data-variant-id="{{ $variant->id }}"
            data-price="{{ $price }}"
            data-stock="{{ $variant->stock }}"
        >

            <img
                src="{{ $product->image
                    ? asset('storage/' . $product->image)
                    : asset('images/product/product-1.jpg') }}"
                alt="{{ $product->name ?? $product->title }}"
                class="cart-item-image"
            >

            <div class="cart-item-content">

                <div class="cart-item-header">

                    <h3>
                        {{ $product->name ?? $product->title }}
                    </h3>

                    <button
                        class="cart-item-remove"
                        type="button"
                        data-variant-id="{{ $variant->id }}"
                        aria-label="Удалить товар"
                    ></button>

                </div>

                <div class="cart-item-info">

                    @if(!empty($variant->name))

                        <span>
                            {{ $variant->name }}
                        </span>

                    @elseif(!empty($variant->title))

                        <span>
                            {{ $variant->title }}
                        </span>

                    @endif

                    <strong class="cart-item-price">
                        {{ number_format($itemTotal, 0, ',', ' ') }} ₽
                    </strong>

                </div>

                <div class="cart-item-bottom">

                    <div class="cart-quantity">

                        <button
                            class="cart-quantity-minus"
                            type="button"
                            data-variant-id="{{ $variant->id }}"
                        >
                            −
                        </button>

                        <span class="cart-quantity-value">
                            {{ $quantity }}
                        </span>

                        <button
                            class="cart-quantity-plus"
                            type="button"
                            data-variant-id="{{ $variant->id }}"
                        >
                            +
                        </button>

                    </div>

                </div>

            </div>

        </div>

    @endif

@empty

    <div class="cart-empty">
        Корзина пуста
    </div>
@endforelse
