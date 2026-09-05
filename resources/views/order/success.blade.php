@extends('layouts.app')

@section('title', ' Корзина-заказ завершен | Dutch Seeds')

@section('seo')

    <meta name="description" content="Dutch Seeds.">
    <meta name="keywords" content="Dutch Seeds">
    <link rel="canonical" href="{{ url('/news') }}">

@endsection

@push('schema')



@endpush

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
                        <rect width="50" height="50" rx="25" fill="#EF8B2D"/>
                        <path d="M21.9064 31.9951C21.7035 31.9951 21.5055 31.9125 21.3607 31.7667L16.7246 27.1006C16.4251 26.7993 16.4251 26.3035 16.7246 26.0021C17.024 25.7008 17.5166 25.7008 17.816 26.0021L21.8677 30.0801L32.1444 18.2642C32.4245 17.9435 32.9123 17.9094 33.2358 18.1913C33.5594 18.4732 33.5883 18.9642 33.3083 19.2898L22.4907 31.7327C22.3507 31.8931 22.1478 31.9903 21.9353 32C21.9257 31.9951 21.916 31.9951 21.9064 31.9951Z" fill="white"/>
                    </svg>
                </div>

                <span>Оформление заказа</span>
            </div>

            <div class="cart-step-line"></div>

            <div class="cart-step">
                <div class="cart-step-icon">

                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="50" height="50" rx="25" fill="#277423"/>
                        <path d="M31.91 27.6927C31.91 28.9827 32.97 30.0327 34.26 30.0327C34.26 33.7827 33.32 34.7227 29.57 34.7227H20.19C16.44 34.7227 15.5 33.7827 15.5 30.0327V29.5727C16.79 29.5727 17.85 28.5127 17.85 27.2227C17.85 25.9327 16.79 24.8727 15.5 24.8727V24.4127C15.51 20.6627 16.44 19.7227 20.19 19.7227H29.56C33.31 19.7227 34.25 20.6627 34.25 24.4127V25.3527C32.96 25.3527 31.91 26.3927 31.91 27.6927Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M29.2111 19.7225H20.1211L23.0511 16.7925C25.4411 14.4025 26.6411 14.4025 29.0311 16.7925L29.6311 17.3925C29.0011 18.0225 28.8511 18.9525 29.2111 19.7225Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22.8789 19.7227L22.8789 34.7227" stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="5 5"/>
                    </svg>
                </div>

                <span>Заказ завершён</span>
            </div>

        </div>
    </section>

    <section class="order-success">

        <div class="container">

            <h1>
                Заказ номер {{ $order['number'] }} оформлен
            </h1>


            <div class="order-success-content">

                <div class="order-success-info">

                    <div class="order-success-text">

                        <p>
                            Письмо с составом заказа было отправлено на вашу почту.
                        </p>

                        <p>
                            После отправки заказа вам придет трек-номер на электронную почту.
                        </p>

                        <p>
                            Пожалуйста внимательно проверьте данные, содержащиеся в ней.
                            Если письмо не пришло или вы хотите внести изменения в данные
                            по заказу, напишите в чат нашему консультанту.
                            Перед этим, пожалуйста, проверьте папку спам.
                        </p>

                    </div>

                </div>


                <div class="order-success-summary">

                    <!-- Сумма товаров -->
                    <div class="order-summary-row order-summary-total">

                    <span>
                        Заказ на сумму
                    </span>

                        <strong>
                            {{ number_format($order['subtotal'], 0, '.', ' ') }} ₽
                        </strong>

                    </div>


                    <!-- Всего товаров -->
                    <div class="order-summary-row order-summary-total">

                    <span>
                        Всего товаров
                    </span>

                        <strong>
                            {{ count($order['items'] ?? []) }} шт
                        </strong>

                    </div>


                    <!-- Скидка -->
                    @if(($order['discount'] ?? 0) > 0)

                        <div class="order-summary-row">

                        <span>
                            Скидка:
                        </span>

                            <span>
                            −{{ number_format($order['discount'], 0, '.', ' ') }} ₽
                        </span>

                        </div>

                    @else

                        <div class="order-summary-row">

                        <span>
                            Скидка:
                        </span>

                            <span>
                            0 ₽
                        </span>

                        </div>

                    @endif


                    <!-- Стоимость доставки -->
                    <div class="order-summary-row">

                    <span>
                        Стоимость доставки
                    </span>

                        <span>

                        @if(($order['delivery'] ?? 0) > 0)

                                {{ number_format($order['delivery'], 0, '.', ' ') }} ₽

                            @else

                                Бесплатно

                            @endif

                    </span>

                    </div>


                    <!-- Итого -->
                    <div class="order-summary-row order-summary-result">

                    <span>
                        Итого
                    </span>

                        <strong>
                            {{ number_format($order['total'], 0, '.', ' ') }} ₽
                        </strong>

                    </div>

                </div>

            </div>


            <div class="order-support">


                <!-- Промокод -->
                <div class="order-success-promo">

                    <div class="order-success-promo-title">
                        Вы получили промокод на 4% на будущие покупки.
                    </div>


                    @if(!empty($order['promo_code']))

                        <div class="order-success-promo-code">

                        <span>
                            Ваш промокод
                        </span>

                            <strong>
                                {{ $order['promo_code'] }}
                            </strong>

                            <button type="button">
                                <!-- иконка копирования -->
                            </button>

                        </div>

                    @endif

                </div>


                <!-- Поддержка -->
                <div class="order-success-promo-2">

                    <div class="order-support-title">
                        Служба поддержки клиентов
                    </div>


                    <div class="order-support-item">

                        <div class="order-support-icon">

                            <svg
                                width="40"
                                height="40"
                                viewBox="0 0 40 40"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <circle
                                    cx="20"
                                    cy="20"
                                    r="20"
                                    fill="#35A0D6"
                                />

                                <path
                                    d="M26.9857 14.2088C27.1182 13.4033 26.3045 12.7676 25.5436 13.082L10.3876 19.3448C9.84193 19.5703 9.88185 20.3483 10.4478 20.5179L13.5733 21.4547C14.1699 21.6335 14.8158 21.541 15.3367 21.2023L22.3834 16.6203C22.5959 16.4821 22.8275 16.7665 22.646 16.9426L17.5736 21.8646C17.0816 22.3421 17.1792 23.1512 17.7711 23.5005L23.4501 26.8523C24.0871 27.2282 24.9065 26.8506 25.0257 26.1261L26.9857 14.2088Z"
                                    fill="white"
                                />
                            </svg>

                        </div>

                        <span>
                        Написать консультанту
                    </span>

                    </div>


                    <div class="order-support-item">

                        <div class="order-support-icon">

                            <svg
                                width="40"
                                height="40"
                                viewBox="0 0 40 40"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >

                                <path
                                    d="M40 20C40 31.0457 31.0457 40 20 40C15.7869 40 11.8781 38.6973 8.65443 36.4728L1.81818 38.1818L3.6096 31.464C1.33487 28.2178 0 24.2648 0 20C0 8.9543 8.9543 0 20 0C31.0457 0 40 8.9543 40 20Z"
                                    fill="url(#paint0_linear_16455_9051)"
                                />

                                <path
                                    d="M13.8442 8.95426C13.321 7.90961 12.5186 8.00209 11.7079 8.00209C10.2591 8.00209 8 9.72754 8 12.9388C8 15.5705 9.16639 18.4514 13.0967 22.7609C16.8898 26.92 21.8737 29.0714 26.0112 28.9982C30.1487 28.9249 31 25.3849 31 24.1894C31 23.6595 30.6693 23.3951 30.4414 23.3233C29.0315 22.6505 26.431 21.3969 25.8393 21.1614C25.2476 20.9258 24.9387 21.2444 24.7466 21.4177C24.21 21.9261 23.1464 23.4245 22.7822 23.7615C22.418 24.0985 21.8751 23.9279 21.6491 23.8005C20.8177 23.4688 18.5633 22.4718 16.7663 20.7398C14.544 18.5979 14.4135 17.8609 13.9948 17.205C13.6599 16.6802 13.9057 16.3582 14.0283 16.2175C14.5071 15.6682 15.1683 14.8201 15.4648 14.3986C15.7613 13.9772 15.5259 13.3373 15.3847 12.9388C14.7772 11.2248 14.2626 9.78999 13.8442 8.95426Z"
                                    fill="white"
                                />

                                <defs>

                                    <linearGradient
                                        id="paint0_linear_16455_9051"
                                        x1="37.5"
                                        y1="5"
                                        x2="-9.68575e-07"
                                        y2="40"
                                        gradientUnits="userSpaceOnUse"
                                    >

                                        <stop stop-color="#5BD066" />
                                        <stop offset="1" stop-color="#27B43E" />

                                    </linearGradient>

                                </defs>

                            </svg>

                        </div>

                        <span>
                        Связаться с поддержкой
                    </span>

                    </div>

                </div>

            </div>

        </div>

    </section>

@endsection
