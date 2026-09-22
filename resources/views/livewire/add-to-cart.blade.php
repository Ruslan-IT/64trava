{{--div class="add-to-cart-block">



    <div class="variant-out-of-stock"
         style="{{ $selectedVariantId ? 'display:none;' : 'display:block;' }}">
        Нет в наличии
    </div>


    @php
        $selectedVariant = $product->variants
            ->firstWhere('id', $selectedVariantId);
    @endphp


    <div class="product-price">

           --}}{{-- <span class="product-old-price">

                  @if($selectedVariant && $selectedVariant->old_price)
                    $ {{ $selectedVariant->old_price }}
                @endif

            </span>--}}{{--

            <span class="product-current-price">
                {{ number_format($selectedVariant ? $selectedVariant->price : $product->price, 0, '.', '') }} р
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
                data-price="{{ $variant->price }}"
                data-old-price="{{ $variant->old_price ?? '' }}"
                data-stock="{{ $variant->stock }}"
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

</div>--}}
@if($type === 'packs')

    {{-- ================================================= --}}
    {{-- НОВЫЙ ВАРИАНТ С SVG --}}
    {{-- ================================================= --}}

    @php
        $selectedVariant = $product->variants
            ->firstWhere('id', $selectedVariantId);
    @endphp

    <div>
        <div class="product-order-packs">


            <div class="product-order-block-title">
                <div class="product-order-title">
                    Количество семян в пачке
                </div>
                <div class="product-order-title">
                    Количество пачек
                </div>
            </div>

            <div class="product-pack-list">

                <div class="product-order--block-left">
                    @foreach($product->variants as $variant)

                        <div
                            class="product-pack
                        {{ $selectedVariantId === $variant->id ? 'active' : '' }}
                        {{ $variant->stock <= 0 ? 'disabled' : '' }}"
                            wire:click="selectVariant({{ $variant->id }})"
                        >
                            <div class="product-pack__icon">
                                {{-- цифра сверху по центру --}}
                                <span class="product-pack__number">
                            {{ $variant->package_size }}
                        </span>

                                {{-- цифра слева сверху --}}
                                <span class="product-pack__plus">
                            +{{ $variant->package_size }}
                        </span>
                                <svg width="80" height="86" viewBox="0 0 80 86" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="6.5" width="79" height="79" rx="7.5" fill="white"></rect>
                                    <rect x="0.5" y="6.5" width="79" height="79" rx="7.5" stroke="#EDEDED"></rect>

                                    <path d="M56.023 77L55.66 76.098L58.014 73.447C58.1387 73.315 58.267 73.1647 58.399 72.996C58.5383 72.82 58.6703 72.6403 58.795 72.457C58.9197 72.2737 59.0187 72.094 59.092 71.918C59.1727 71.7347 59.213 71.5623 59.213 71.401C59.213 71.1663 59.1653 70.9683 59.07 70.807C58.982 70.6383 58.85 70.51 58.674 70.422C58.5053 70.334 58.3 70.29 58.058 70.29C57.8453 70.29 57.6363 70.3523 57.431 70.477C57.2257 70.6017 57.0387 70.7777 56.87 71.005C56.7013 71.2323 56.5693 71.4963 56.474 71.797L55.561 71.225C55.6857 70.7997 55.8727 70.4367 56.122 70.136C56.3787 69.828 56.683 69.5933 57.035 69.432C57.3943 69.2707 57.783 69.19 58.201 69.19C58.6337 69.19 59.0187 69.2817 59.356 69.465C59.6933 69.641 59.9573 69.8903 60.148 70.213C60.3387 70.5283 60.434 70.895 60.434 71.313C60.434 71.4817 60.4157 71.654 60.379 71.83C60.3423 72.006 60.2837 72.1893 60.203 72.38C60.1223 72.5633 60.0197 72.7577 59.895 72.963C59.7703 73.161 59.62 73.37 59.444 73.59C59.2753 73.81 59.081 74.041 58.861 74.283L57.112 76.186L56.892 75.856H60.676V77H56.023ZM64.3594 77.11C63.7727 77.11 63.2667 76.9487 62.8414 76.626C62.4234 76.3033 62.1007 75.8487 61.8734 75.262C61.646 74.668 61.5324 73.964 61.5324 73.15C61.5324 72.336 61.646 71.6357 61.8734 71.049C62.1007 70.455 62.4234 69.9967 62.8414 69.674C63.2667 69.3513 63.7727 69.19 64.3594 69.19C64.946 69.19 65.452 69.3513 65.8774 69.674C66.3027 69.9967 66.629 70.455 66.8564 71.049C67.0837 71.6357 67.1974 72.336 67.1974 73.15C67.1974 73.964 67.0837 74.668 66.8564 75.262C66.629 75.8487 66.3027 76.3033 65.8774 76.626C65.452 76.9487 64.946 77.11 64.3594 77.11ZM64.3594 76.032C64.8947 76.032 65.309 75.7827 65.6024 75.284C65.8957 74.778 66.0424 74.0667 66.0424 73.15C66.0424 72.2333 65.8957 71.5257 65.6024 71.027C65.309 70.521 64.8947 70.268 64.3594 70.268C63.824 70.268 63.4097 70.521 63.1164 71.027C62.823 71.5257 62.6764 72.2333 62.6764 73.15C62.6764 74.0667 62.823 74.778 63.1164 75.284C63.4097 75.7827 63.824 76.032 64.3594 76.032ZM69.127 77L74.22 69.3H75.353L70.205 77H69.127ZM69.831 73.381C69.501 73.381 69.2077 73.293 68.951 73.117C68.7017 72.9337 68.5037 72.688 68.357 72.38C68.2177 72.0647 68.148 71.7053 68.148 71.302C68.148 70.8987 68.2177 70.5393 68.357 70.224C68.5037 69.9013 68.7053 69.652 68.962 69.476C69.2187 69.2927 69.512 69.201 69.842 69.201C70.172 69.201 70.4617 69.2927 70.711 69.476C70.9677 69.652 71.1657 69.9013 71.305 70.224C71.4517 70.5393 71.525 70.8987 71.525 71.302C71.525 71.6907 71.4517 72.0427 71.305 72.358C71.1583 72.6733 70.9567 72.9227 70.7 73.106C70.4433 73.2893 70.1537 73.381 69.831 73.381ZM69.842 72.556C69.996 72.556 70.128 72.5047 70.238 72.402C70.3553 72.2993 70.4433 72.1563 70.502 71.973C70.568 71.7823 70.601 71.5587 70.601 71.302C70.601 71.0453 70.568 70.8217 70.502 70.631C70.4433 70.4403 70.3553 70.2937 70.238 70.191C70.128 70.0883 69.9923 70.037 69.831 70.037C69.677 70.037 69.5413 70.092 69.424 70.202C69.3067 70.3047 69.215 70.4513 69.149 70.642C69.0903 70.8253 69.061 71.0453 69.061 71.302C69.061 71.5587 69.0903 71.7823 69.149 71.973C69.215 72.1563 69.3067 72.2993 69.424 72.402C69.5413 72.5047 69.6807 72.556 69.842 72.556ZM74.704 77.099C74.374 77.099 74.0807 77.0073 73.824 76.824C73.5747 76.6407 73.3767 76.395 73.23 76.087C73.0833 75.7717 73.01 75.416 73.01 75.02C73.01 74.6167 73.0797 74.2573 73.219 73.942C73.3657 73.6193 73.5673 73.37 73.824 73.194C74.0807 73.0107 74.374 72.919 74.704 72.919C75.034 72.919 75.3237 73.0107 75.573 73.194C75.8297 73.37 76.0277 73.6193 76.167 73.942C76.3137 74.2573 76.387 74.6167 76.387 75.02C76.387 75.4087 76.3137 75.7607 76.167 76.076C76.0203 76.3913 75.8187 76.6407 75.562 76.824C75.3127 77.0073 75.0267 77.099 74.704 77.099ZM74.704 76.285C74.858 76.285 74.9937 76.2337 75.111 76.131C75.2283 76.021 75.3163 75.8743 75.375 75.691C75.441 75.5003 75.474 75.2767 75.474 75.02C75.474 74.7633 75.441 74.5433 75.375 74.36C75.3163 74.1693 75.2283 74.0227 75.111 73.92C74.9937 73.81 74.858 73.755 74.704 73.755C74.5427 73.755 74.4033 73.8063 74.286 73.909C74.1687 74.0117 74.077 74.1583 74.011 74.349C73.9523 74.5397 73.923 74.7633 73.923 75.02C73.923 75.2767 73.9523 75.5003 74.011 75.691C74.077 75.8743 74.1687 76.021 74.286 76.131C74.4033 76.2337 74.5427 76.285 74.704 76.285Z" fill="white"></path>
                                    <path d="M80 32L80 78C80 82.4183 76.4183 86 72 86L26 86L29.9936 85.3258C34.909 84.4959 39.4439 82.1561 42.9688 78.6312L72.6312 48.9688C76.1561 45.4439 78.4959 40.909 79.3258 35.9936L80 32Z" fill="#9A4836"></path>
                                    <g clip-path="url(#clip0_16324_10892)">
                                        <path d="M7 0.999999C7 0.447714 7.44772 0 8 0H37C37.5523 0 38 0.447715 38 1V31.6309C38 32.3081 37.3409 32.7895 36.6958 32.5835L22.8042 28.1472C22.6063 28.084 22.3937 28.084 22.1958 28.1472L8.30422 32.5835C7.65907 32.7895 7 32.3081 7 31.6309V0.999999Z" fill="#1E6F98"></path>

                                    </g>
                                    <defs>
                                        <clipPath id="clip0_16324_10892">
                                            <rect width="31" height="33" fill="white" transform="translate(7)"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>


                                <div class="product-pack-price">
                                    {{ number_format($variant->price, 0, '.', '') }}р
                                </div>
                                <div class="product-pack-price-old-price">
                                    {{ number_format($variant->old_price, 0, '.', '') }} р
                                </div>

                                @if($variant->old_price)
                                    <div class="product-pack-old-price">
                                        {{ number_format($variant->old_price, 0, '.', '') }}р
                                    </div>
                                @endif

                            </div>
                        </div>

                    @endforeach
                </div>


                <div class="product-order-block-right">




                        <div class="product-quantity-stock">

                            <div class="product-quantity">

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

                                    <div class="product-stock-separator">
                                        |
                                    </div>

                                    <div class="product-stock">
                                        <span>В наличии</span>
                                    </div>

                                </div>



                            </div>



                        </div>

                    </div>

            </div>

            <div class="product-cart-row">

                <button
                    type="button"
                    class="product-add-to-cart {{ $addedToCart ? 'added' : '' }}"
                    wire:click="addToCart"
                    wire:loading.attr="disabled"
                >
                    <span>
                        {{ $addedToCart ? 'Добавлено' : 'Добавить в корзину' }}
                    </span>

                    <strong>
                        |
                        {{ $selectedVariant
                            ? number_format($selectedVariant->price * $quantity, 0, '.', '')
                            : 0
                        }}р
                    </strong>
                </button>

                <div class="product-actions">
                   {{-- <div class="quantity">

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

                    </div>--}}

                </div>

            </div>

        </div>


    </div>




@else

    {{-- ================================================= --}}
    {{-- СТАРЫЙ ВАРИАНТ --}}
    {{-- ================================================= --}}

    <div class="add-to-cart-block">

        <div
            class="variant-out-of-stock"
            style="{{ $selectedVariantId ? 'display:none;' : 'display:block;' }}"
        >
            Нет в наличии
        </div>

        @php
            $selectedVariant = $product->variants
                ->firstWhere('id', $selectedVariantId);
        @endphp

        <div class="product-price">

            {{--
            <span class="product-old-price">

                @if($selectedVariant && $selectedVariant->old_price)
                    {{ $selectedVariant->old_price }}
                @endif

            </span>
            --}}

            <span class="product-current-price">
                {{ number_format(
                    $selectedVariant
                        ? $selectedVariant->price
                        : $product->price,
                    0,
                    '.',
                    ''
                ) }} р
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
                    data-price="{{ $variant->price }}"
                    data-old-price="{{ $variant->old_price ?? '' }}"
                    data-stock="{{ $variant->stock }}"
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

@endif
