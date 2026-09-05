@extends('layouts.app')

@section('title', 'Оформление заказа | Dutch Seeds')

@section('seo')

    <meta name="description" content="">
    <meta name="keywords" content="Dutch Seeds">
    <link rel="canonical" href="{{ url('/news') }}">

@endsection



@section('content')

    <section class="cart-steps">
        <div class="cart-steps-inner">

            <div class="cart-step cart-step--active">
                <div class="cart-step-icon">

                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="50" height="50" rx="25" fill="#EF8B2D"/>
                        <path d="M21.9064 31.9951C21.7035 31.9951 21.5055 31.9125 21.3607 31.7667L16.7246 27.1006C16.4251 26.7993 16.4251 26.3035 16.7246 26.0021C17.024 25.7008 17.5166 25.7008 17.816 26.0021L21.8677 30.0801L32.1444 18.2642C32.4245 17.9435 32.9123 17.9094 33.2358 18.1913C33.5594 18.4732 33.5883 18.9642 33.3083 19.2898L22.4907 31.7327C22.3507 31.8931 22.1478 31.9903 21.9353 32C21.9257 31.9951 21.916 31.9951 21.9064 31.9951Z" fill="white"/>
                    </svg>
                </div>

                <span>Корзина</span>
            </div>

            <div class="cart-step-line"></div>

            <div class="cart-step">
                <div class="cart-step-icon">
                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="50" height="50" rx="25" fill="#277423"/>
                        <path d="M35 25V30C35 33 33 35 30 35H20C17 35 15 33 15 30V25C15 22.28 16.64 20.38 19.19 20.06C19.45 20.02 19.72 20 20 20H30C30.26 20 30.51 20.01 30.75 20.05C33.33 20.35 35 22.26 35 25Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M30.7514 20.05C30.5114 20.01 30.2614 20 30.0014 20H20.0014C19.7214 20 19.4514 20.02 19.1914 20.06C19.3314 19.78 19.5314 19.52 19.7714 19.28L23.0214 16.02C24.3914 14.66 26.6114 14.66 27.9814 16.02L29.7314 17.79C30.3714 18.42 30.7114 19.22 30.7514 20.05Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M35 25.5H32C30.9 25.5 30 26.4 30 27.5C30 28.6 30.9 29.5 32 29.5H35" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <span>Оформление заказа</span>
            </div>

            <div class="cart-step-line"></div>

            <div class="cart-step">
                <div class="cart-step-icon">
                    <svg width="" height="" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="0.5" y="0.5" width="31" height="31" rx="15.5" fill="white"/>
                        <rect x="0.5" y="0.5" width="31" height="31" rx="15.5" stroke="#D8D8D8"/>
                        <path d="M20.6065 17.7948C20.6065 18.6548 21.3132 19.3548 22.1732 19.3548C22.1732 21.8548 21.5465 22.4814 19.0465 22.4814H12.7932C10.2932 22.4814 9.6665 21.8548 9.6665 19.3548V19.0481C10.5265 19.0481 11.2332 18.3414 11.2332 17.4814C11.2332 16.6214 10.5265 15.9148 9.6665 15.9148V15.6081C9.67317 13.1081 10.2932 12.4814 12.7932 12.4814H19.0398C21.5398 12.4814 22.1665 13.1081 22.1665 15.6081V16.2348C21.3065 16.2348 20.6065 16.9281 20.6065 17.7948Z" stroke="#585858" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.8076 12.4818H12.7476L14.7009 10.5285C16.2942 8.93516 17.0942 8.93516 18.6876 10.5285L19.0876 10.9285C18.6676 11.3485 18.5676 11.9685 18.8076 12.4818Z" stroke="#585858" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14.5859 12.4819L14.5859 22.4819" stroke="#585858" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="5 5"/>
                    </svg>
                </div>

                <span>Заказ завершён</span>
            </div>

        </div>
    </section>




    <form
        action="{{ route('checkout.store') }}"
        method="POST"
    >
        @csrf

        <div class="container">

            <!-- Заголовок -->
            <div class="checkout__title">
                <h1>
                    Оформление заказа,
                    <span>{{ count($items) }} {{ count($items) === 1 ? 'товар' : 'товара' }}</span>
                </h1>
            </div>


            <div class="checkout__layout">

                <!-- =========================
                     ЛЕВАЯ ЧАСТЬ
                ========================== -->
                <main class="checkout__left">


                    <!-- Регион доставки -->
                    <section class="checkout-section">

                        <h2 class="section-title">1. Регион доставки</h2>

                        <div class="region-fields">

                            <div class="form-field">

                                <label for="country">
                                    Страна
                                </label>

                                <select id="country" name="country" required>
                                    <option
                                        value="ru"
                                        @selected(old('country', 'ru') === 'ru')
                                    >
                                        Россия
                                    </option>

                                    <option
                                        value="kz"
                                        @selected(old('country') === 'kz')
                                    >
                                        Казахстан
                                    </option>

                                    <option
                                        value="ua"
                                        @selected(old('country') === 'ua')
                                    >
                                        Украина
                                    </option>

                                    <option
                                        value="by"
                                        @selected(old('country') === 'by')
                                    >
                                        Беларусь
                                    </option>
                                </select>

                                @error('country')
                                <div class="checkout-error">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>


                            <div class="form-field">

                                <label for="city">
                                    Город/населенный пункт
                                </label>

                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    value="{{ old('city') }}"
                                    placeholder="Введите ваш город, населенный пункт"
                                    required
                                >

                                @error('city')
                                <div class="checkout-error">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>

                    </section>


                    <!-- Способ доставки -->
                    <section class="checkout-section delivery-section">

                        <h2 class="section-title">
                            Способ доставки
                        </h2>

                        <div class="delivery-grid">


                            <!-- Почта России -->
                            <label class="choice-card">

                                <input
                                    type="radio"
                                    name="delivery"
                                    value="russian-post"
                                    @checked(old('delivery', 'russian-post') === 'russian-post')
                                    required
                                >

                                <span class="choice-card__content">

                                <span class="choice-card__image">
                                    <img
                                        src="{{ asset('images/icon/russian-post.png') }}"
                                        alt="Почта России"
                                    >
                                </span>

                                <span class="choice-card__info">

                                    <span class="choice-card__title">
                                        Почта России<br>
                                        отделение
                                    </span>

                                    <span class="choice-card__description">
                                        1–3 дня от 252 рублей
                                    </span>

                                </span>

                            </span>

                            </label>


                            <!-- Почта России курьер -->
                            <label class="choice-card">

                                <input
                                    type="radio"
                                    name="delivery"
                                    value="russian-post-courier"
                                    @checked(old('delivery') === 'russian-post-courier')
                                >

                                <span class="choice-card__content">

                                <span class="choice-card__image">
                                    <img
                                        src="{{ asset('images/icon/card__image.png') }}"
                                        alt="Почта России"
                                    >
                                </span>

                                <span class="choice-card__info">

                                    <span class="choice-card__title">
                                        Почта России<br>
                                        курьер
                                    </span>

                                    <span class="choice-card__description">
                                        1–3 дня от 350 рублей
                                    </span>

                                </span>

                            </span>

                            </label>


                            <!-- СДЭК -->
                            <label class="choice-card">

                                <input
                                    type="radio"
                                    name="delivery"
                                    value="cdek"
                                    @checked(old('delivery') === 'cdek')
                                >

                                <span class="choice-card__content">

                                <span class="choice-card__image">
                                    <img
                                        src="{{ asset('images/icon/cdek.png') }}"
                                        alt="СДЭК"
                                    >
                                </span>

                                <span class="choice-card__info">

                                    <span class="choice-card__title">
                                        СДЭК
                                    </span>

                                    <span class="choice-card__description">
                                        1–3 дня от 300 рублей
                                    </span>

                                </span>

                            </span>

                            </label>


                            <!-- СДЭК курьер -->
                            <label class="choice-card">

                                <input
                                    type="radio"
                                    name="delivery"
                                    value="cdek-courier"
                                    @checked(old('delivery') === 'cdek-courier')
                                >

                                <span class="choice-card__content">

                                <span class="choice-card__image">
                                    <img
                                        src="{{ asset('images/icon/cdek.png') }}"
                                        alt="СДЭК"
                                    >
                                </span>

                                <span class="choice-card__info">

                                    <span class="choice-card__title">
                                        СДЭК курьер
                                    </span>

                                    <span class="choice-card__description">
                                        1–3 дня от 450 рублей
                                    </span>

                                </span>

                            </span>

                            </label>

                        </div>

                        @error('delivery')
                        <div class="checkout-error">
                            {{ $message }}
                        </div>
                        @enderror

                    </section>


                    <!-- Адрес доставки -->
                    <section class="metro-section">

                        <h2 class="section-title">
                            2. Выберите станцию метро
                        </h2>

                        <div class="search-input">

                        <span class="search-input__icon">
                            ⌕
                        </span>

                            <input
                                type="text"
                                id="address"
                                name="address"
                                value="{{ old('address') }}"
                                placeholder="Введите адрес и индекс"
                                required
                            >

                        </div>

                        @error('address')
                        <div class="checkout-error">
                            {{ $message }}
                        </div>
                        @enderror


                        <div class="map">
                            <div class="map__placeholder">
                                Карта
                            </div>
                        </div>

                    </section>


                    <!-- Способ оплаты -->
                    <section class="checkout-section payment-section">

                        <h2 class="section-title">
                            3. Способ оплаты
                        </h2>


                        <div class="payment-grid">


                            <!-- Наличными -->
                            <label class="payment-card">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="cash"
                                    @checked(old('payment_method', 'cash') === 'cash')
                                    required
                                >

                                <span class="payment-card__content">

                                <span class="payment-card__title">
                                    Наличными курьеру
                                </span>

                                <span class="payment-card__description">
                                    без комиссии
                                </span>

                            </span>

                            </label>


                            <!-- Криптовалюта -->
                            <label class="payment-card">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="crypto"
                                    @checked(old('payment_method') === 'crypto')
                                >

                                <span class="payment-card__content">

                                <span class="payment-card__title">
                                    Криптовалюты
                                </span>

                                <span class="payment-card__description">
                                    без комиссии
                                </span>

                            </span>

                            </label>


                            <!-- Карта -->
                            <label class="payment-card">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="card"
                                    @checked(old('payment_method') === 'card')
                                >

                                <span class="payment-card__content">

                                <span class="payment-card__title">
                                    Карты
                                </span>

                                <span class="payment-card__description">
                                    комиссия 1%
                                </span>

                            </span>

                            </label>

                        </div>


                        @error('payment_method')
                        <div class="checkout-error">
                            {{ $message }}
                        </div>
                        @enderror


                        <!-- Криптовалюта -->
                        <div class="crypto-box">

                            <p class="crypto-box__text">
                                Анонимный перевод средств на кошелек
                            </p>


                            <div class="crypto-options">

                                <label class="crypto-option">

                                    <input
                                        type="radio"
                                        name="crypto"
                                        value="bitcoin"
                                        @checked(old('crypto') === 'bitcoin')
                                    >

                                    <span>
                                    Bitcoin
                                </span>

                                </label>


                                <label class="crypto-option">

                                    <input
                                        type="radio"
                                        name="crypto"
                                        value="usdt"
                                        @checked(old('crypto') === 'usdt')
                                    >

                                    <span>
                                    USDT
                                </span>

                                </label>

                            </div>

                            @error('crypto')
                            <div class="checkout-error">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </section>


                    <!-- Данные покупателя -->
                    <section class="customer-section">

                        <h2 class="section-title">
                            4. Данные покупателя
                        </h2>


                        <div class="customer-form">


                            <!-- Имя -->
                            <div class="form-field form-field--small">

                                <label for="name">
                                    Как к вам обращаться?
                                    {{--<span class="required">*</span>--}}
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Михалыч"
                                    required
                                >

                                @error('name')
                                <div class="checkout-error">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>


                            <div class="customer-form__row">


                                <!-- Телефон -->
                                <div class="form-field">

                                    <label for="phone">
                                        Номер телефона
                                       {{-- <span class="required">*</span>--}}
                                    </label>

                                    <input
                                        type="tel"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="+7 (___) ___-__-__"
                                        required
                                    >

                                    @error('phone')
                                    <div class="checkout-error">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>


                                <!-- Email -->
                                <div class="form-field">

                                    <label for="email">
                                        Email
                                        {{--<span class="required">*</span>--}}
                                    </label>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="Введите ваш email"
                                        required
                                    >

                                    @error('email')
                                    <div class="checkout-error">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                            </div>


                            <!-- Комментарий -->
                            <div class="form-field">

                                <label for="comment">
                                    Комментарий
                                </label>

                                <textarea
                                    id="comment"
                                    name="comment"
                                    placeholder="Комментарий к заказу"
                                >{{ old('comment') }}</textarea>

                                @error('comment')
                                <div class="checkout-error">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>

                    </section>

                </main>



                <!-- =========================
                     ПРАВАЯ ЧАСТЬ
                ========================== -->

                <aside class="checkout__right">

                    <div class="order-summary">

                        <h2 class="order-summary__title">
                            Ваш заказ
                        </h2>


                        <!-- Товары -->
                        @php
                            $totalQuantity = 0;
                            $totalAmount = 0;

                            foreach ($items as $item) {
                                $quantity = $item['quantity'] ?? 1;
                                $price = $item['price'] ?? 0;

                                $totalQuantity += $quantity;
                                $totalAmount += $price * $quantity;
                            }
                        @endphp

                        <div class="order-product">

                            <div class="order-product__name">
                                <span>
                                    Товаров: {{ $totalQuantity }} шт.
                                </span>
                            </div>

                          {{--  <div class="order-product__price">
                                <span class="current-price">
                                    {{ number_format($totalAmount, 0, ',', ' ') }} ₽
                                </span>
                            </div>--}}

                        </div>





                        <!-- Скидка -->
                        @if(($discount ?? 0) > 0)

                            <div class="order-row order-row--discount">

                            <span>
                                Скидка
                            </span>

                                <span>
                                −{{ number_format($discount, 0, ',', ' ') }} ₽
                            </span>

                            </div>

                        @endif


                        <!-- Итог -->
                        <div class="order-total">

                            <strong>
                                Итог
                            </strong>

                            <strong>
                                {{ number_format($total ?? 0, 0, ',', ' ') }} ₽
                            </strong>

                        </div>


                        <!-- Промокод -->
                        <div class="promo">

                            <input
                                type="text"
                                id="promo_code"
                                name="promo_code"
                                value="{{ old('promo_code') }}"
                                placeholder="Промокод"
                            >

                            <button
                                type="button"
                                id="apply-promo"
                            >
                                Применить код
                            </button>

                        </div>

                        @error('promo_code')
                        <div class="checkout-error">
                            {{ $message }}
                        </div>
                        @enderror


                        <!-- Прогресс бесплатной доставки -->
                        <div class="delivery-progress">

                            <div class="delivery-progress__bar">
                                <span></span>
                            </div>

                            <p>
                                Бесплатная доставка при заказе
                                6 тысяч рублей
                            </p>

                        </div>


                        <!-- Продолжить покупки -->
                        <a
                            href="{{ route('cart.index') }}"
                            class="continue-shopping"
                        >
                            Продолжить покупки
                        </a>


                        <!-- Оформить заказ -->
                        <button
                            type="submit"
                            class="checkout-button"
                        >
                            Оформление заказа
                        </button>

                    </div>

                </aside>

            </div>

        </div>

    </form>
    <script>
        /*
         * Подсветка выбранных способов доставки и оплаты
         */

        document.querySelectorAll('.choice-card input').forEach(input => {

            input.addEventListener('change', () => {

                document
                    .querySelectorAll('.choice-card')
                    .forEach(card => {
                        card.classList.remove('is-selected');
                    });

                input
                    .closest('.choice-card')
                    .classList.add('is-selected');
            });

        });


        document.querySelectorAll('.payment-card input').forEach(input => {

            input.addEventListener('change', () => {

                document
                    .querySelectorAll('.payment-card')
                    .forEach(card => {
                        card.classList.remove('is-selected');
                    });

                input
                    .closest('.payment-card')
                    .classList.add('is-selected');

            });

        });
    </script>





@endsection

