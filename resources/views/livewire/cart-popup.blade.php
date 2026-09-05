<div>

    <div class="cart-popup-overlay" id="cartPopup" wire:ignore.self>
        <div class="cart-popup">

            <button class="cart-popup-close" type="button" id="cartPopupClose">
                &times;
            </button>

            <h2 class="cart-popup-title">Корзина</h2>

            <div class="cart-items" id="cartItems">

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

                            {{-- Изображение --}}
                            <img
                                src="{{ $product->image
                                ? asset('storage/' . $product->image)
                                : asset('images/product/product-1.jpg') }}"
                                alt="{{ $product->name ?? $product->title }}"
                                class="cart-item-image"
                            >

                            <div class="cart-item-content">

                                {{-- Заголовок --}}
                                <div class="cart-item-header">

                                    <h3>
                                        {{ $product->name ?? $product->title }}
                                    </h3>

                                    <button
                                        class="cart-item-remove"
                                        type="button"
                                        wire:click="remove({{ $variant->id }})"
                                        aria-label="Удалить товар"
                                    >

                                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 18C2.45 18 1.97917 17.8042 1.5875 17.4125C1.19583 17.0208 1 16.55 1 16V3H0V1H5V0H11V1H16V3H15V16C15 16.55 14.8042 17.0208 14.4125 17.4125C14.0208 17.8042 13.55 18 13 18H3ZM13 3H3V16H13V3ZM5 14H7V5H5V14ZM9 14H11V5H9V14Z" fill="#585858"/>
                                        </svg>
                                    </button>

                                </div>

                                {{-- Информация --}}
                                <div class="cart-item-info">

                                    <div class="cart-item-variant">

                                        @if(!empty($variant->package_size))
                                            <span>
                                                Упаковка: {{ $variant->package_size }} шт
                                            </span>
                                        @endif

                                        @if(!empty($variant->sku))
                                            <span>
                                                Артикул: {{ $variant->sku }}
                                            </span>
                                        @endif

                                    </div>

                                    <strong class="cart-item-price">
                                        {{ number_format($itemTotal, 0, ',', ' ') }} ₽
                                    </strong>

                                </div>

                                {{-- Количество --}}
                                <div class="cart-item-bottom">

                                    <div class="cart-quantity">

                                        <button
                                            class="cart-quantity-minus"
                                            type="button"
                                            wire:click="decrement({{ $variant->id }})"
                                        >
                                            −
                                        </button>

                                        <span class="cart-quantity-value">
                                        {{ $quantity }}
                                    </span>

                                        <button
                                            class="cart-quantity-plus"
                                            type="button"
                                            wire:click="increment({{ $variant->id }})"
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

            </div>


            {{-- Бесплатно --}}
            <div class="cart-free">
                <span>Бесплатно</span>
            </div>


            {{-- Бонусы --}}
            <div class="cart-bonus">

                <div class="cart-bonus-icon">

                    <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="34" height="34" rx="17" fill="#F4F4F4"/>
                        <path d="M20.6263 14.3421L16.7585 10.4743C16.5871 10.3028 16.2835 10.3028 16.112 10.4743L12.7384 13.8484C12.5599 14.0269 12.5599 14.3162 12.7384 14.4948L16.6058 18.3622C16.8237 18.5805 17.1139 18.7006 17.4219 18.7011C17.7304 18.7006 18.0206 18.5805 18.238 18.3627L20.6264 15.9743C21.0763 15.5242 21.0763 14.7921 20.6263 14.3421ZM16.4353 11.4439L17.8248 12.8334L15.0976 15.5611L13.7081 14.1716L16.4353 11.4439ZM19.9799 15.3278L17.5911 17.7167C17.5009 17.8059 17.3438 17.8073 17.2527 17.7162L15.7849 16.2484L18.5121 13.5207L19.9799 14.9885C20.0732 15.0823 20.0732 15.234 19.9799 15.3278Z" fill="#717378"/>
                        <path d="M23.413 19.9453H19.3018C19.0491 19.9453 18.8446 20.1498 18.8446 20.4025C18.8446 21.6694 18.0995 22.74 17.2174 22.74C16.3344 22.74 15.5884 21.6694 15.5884 20.4025C15.5884 20.1498 15.3839 19.9453 15.1312 19.9453H10.588C10.3353 19.9453 10.1309 20.1498 10.1309 20.4025C10.1309 20.6551 10.3353 20.8596 10.588 20.8596H14.7023C14.8771 22.4363 15.9369 23.6542 17.2175 23.6542C18.4973 23.6542 19.5563 22.4363 19.7309 20.8596H23.413C23.6657 20.8596 23.8702 20.6551 23.8702 20.4025C23.8702 20.1498 23.6657 19.9453 23.413 19.9453Z" fill="#717378"/>
                        <path d="M22.6615 16.125C23.0307 16.125 23.3312 15.8246 23.3312 15.4201C23.3312 15.0509 23.0307 14.7505 22.6481 14.7505C22.2789 14.7505 21.9785 15.0509 21.9785 15.4554C21.9785 15.8246 22.2789 16.125 22.6481 16.125H22.6615ZM22.4356 15.4202C22.4356 15.3032 22.5312 15.2077 22.6615 15.2077C22.7785 15.2077 22.874 15.3032 22.874 15.4554C22.874 15.5724 22.7784 15.6679 22.6615 15.6679H22.6481C22.5312 15.6679 22.4356 15.5724 22.4356 15.4202Z" fill="#717378"/>
                        <path d="M13.4512 18.3075C13.4512 18.6766 13.7516 18.9771 14.1208 18.9771H14.1346C14.5038 18.9771 14.8043 18.6766 14.8043 18.2722C14.8043 17.903 14.5038 17.6025 14.1208 17.6025C13.7516 17.6025 13.4512 17.903 13.4512 18.3075ZM14.3471 18.3075C14.3471 18.4227 14.2498 18.5199 14.1346 18.5199H14.1208C14.0038 18.5199 13.9083 18.4244 13.9083 18.2722C13.9083 18.1552 14.0039 18.0597 14.1346 18.0597C14.2498 18.0597 14.3471 18.157 14.3471 18.3075Z" fill="#717378"/>
                        <path d="M19.0732 18.766C19.0732 19.1356 19.3737 19.4361 19.7429 19.4361H19.7563C20.1259 19.4361 20.4263 19.1356 20.4263 18.7311C20.4263 18.362 20.1259 18.0615 19.7429 18.0615C19.3737 18.0615 19.0732 18.362 19.0732 18.766ZM19.9692 18.766C19.9692 18.8834 19.8737 18.9789 19.7563 18.9789H19.7429C19.6259 18.9789 19.5304 18.8834 19.5304 18.7311C19.5304 18.6142 19.6259 18.5187 19.7563 18.5187C19.8737 18.5187 19.9692 18.6142 19.9692 18.766Z" fill="#717378"/>
                        <path d="M21.5303 17.703C21.5303 18.0721 21.8307 18.3726 22.1999 18.3726H22.2137C22.5829 18.3726 22.8834 18.0721 22.8834 17.6677C22.8834 17.2985 22.5829 16.998 22.1999 16.998C21.8307 16.998 21.5303 17.2985 21.5303 17.703ZM22.2137 17.4552C22.3289 17.4552 22.4262 17.5525 22.4262 17.703C22.4262 17.8199 22.3307 17.9154 22.2137 17.9154H22.1999C22.0829 17.9154 21.9874 17.8199 21.9874 17.6677C21.9874 17.5525 22.0847 17.4552 22.2137 17.4552Z" fill="#717378"/>
                        <path d="M16.54 20.6043C16.54 20.9735 16.8405 21.274 17.2097 21.274H17.2235C17.5927 21.274 17.8931 20.9735 17.8931 20.569C17.8931 20.1998 17.5927 19.8994 17.2097 19.8994C16.8405 19.8994 16.54 20.1998 16.54 20.6043ZM17.436 20.6043C17.436 20.7213 17.3405 20.8168 17.2235 20.8168H17.2097C17.0945 20.8168 16.9972 20.7195 16.9972 20.569C16.9972 20.4521 17.0927 20.3566 17.2235 20.3566C17.3405 20.3566 17.436 20.4521 17.436 20.6043Z" fill="#717378"/>
                    </svg>
                </div>

                <div>
                    <span>Бонусные семена</span>
                </div>

                <div>
                    <strong>{{$cartQuantity}} шт</strong>
                </div>

            </div>


            {{-- Итоги --}}
            <div class="cart-summary">

                <div class="cart-summary-row sum">

                <span>
                    Заказ на сумму
                </span>

                    <strong id="cartSubtotal">
                        {{ number_format($cartTotal, 0, ',', ' ') }} ₽
                    </strong>

                </div>


                <div class="cart-summary-row">

                <span>
                    Скидка
                </span>

                    <strong id="cartDiscount">
                        0 ₽
                    </strong>

                </div>


                <div class="cart-summary-row">

                <span>
                    Стоимость доставки
                </span>

                    <strong id="cartDelivery">
                        100 ₽
                    </strong>

                </div>


                <div class="cart-summary-total">

                <span>
                    Итого
                </span>

                    <strong id="cartTotal">
                        {{ number_format($cartTotal + 100, 0, ',', ' ') }} ₽
                    </strong>

                </div>

            </div>


            {{-- Кнопка перехода в полную корзину --}}
            <a
                href="{{ route('cart.index') }}"
                class="cart-popup-button"
            >
                В корзину
            </a>

        </div>
    </div>
</div>
