<div class="add-to-cart-block">



    <div class="variant-out-of-stock"
         style="{{ $selectedVariantId ? 'display:none;' : 'display:block;' }}">
        Нет в наличии
    </div>


    @php
        $selectedVariant = $product->variants
            ->firstWhere('id', $selectedVariantId);
    @endphp


    <div class="product-price">

           {{-- <span class="product-old-price">

                  @if($selectedVariant && $selectedVariant->old_price)
                    $ {{ $selectedVariant->old_price }}
                @endif

            </span>--}}

            <span class="product-current-price">
                {{ $selectedVariant ? $selectedVariant->price : $product->price }} р
            </span>

    </div>

    <div class="products-pagination">

        @foreach($product->variants as $variant)

            <a
                href="#"
                wire:click.prevent="selectVariant({{ $variant->id }})"
                class="pagination-button
                {{ $variant->stock <= 0 ? 'disabled' : '' }}
                {{ $selectedVariantId === $variant->id ? 'active' : '' }}"
            >
                {{ $variant->package_size }}
            </a>

        @endforeach

    </div>


    <div class="product-actions">

        <div class="quantity">

            <button
                type="button"
                class="quantity-button quantity-minus"
                wire:click="decrement"
            >
                −
            </button>

            <span class="quantity-value">
             {{ $quantity }}
        </span>

            <button
                type="button"
                class="quantity-button quantity-plus"
                wire:click="increment"
            >
                +
            </button>

        </div>


        <button
            type="button"
            class="add-to-cart {{ $addedToCart ? 'added' : '' }}"
            wire:click="addToCart"
            x-data
            x-on:reset-add-button.window="
        setTimeout(() => {
            $wire.set('addedToCart', false)
        }, 1500)
    "
        >
            {{ $addedToCart ? 'Добавлено' : 'В корзину' }}
        </button>

    </div>

</div>

