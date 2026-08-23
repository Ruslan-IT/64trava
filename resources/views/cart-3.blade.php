@extends('layouts.app')

@section('title', 'Главная')

@section('content')

    <main>

        <div class="container">
            <section class="cart-steps">
                <div class="cart-steps-inner">

                    <div class="cart-step cart-step--active">
                        <div class="cart-step-icon">

                            <svg width="" height="" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="32" height="32" rx="16" fill="#277423"/>
                                <path d="M13 13.1133V12.4667C13 10.9667 14.2067 9.49334 15.7067 9.35334C17.4933 9.18001 19 10.5867 19 12.34V13.26" stroke="white" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.9999 22.6668H17.9999C20.6799 22.6668 21.1599 21.5935 21.2999 20.2868L21.7999 16.2868C21.9799 14.6602 21.5132 13.3335 18.6666 13.3335H13.3332C10.4866 13.3335 10.0199 14.6602 10.1999 16.2868L10.6999 20.2868C10.8399 21.5935 11.3199 22.6668 13.9999 22.6668Z" stroke="white" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.3302 15.9998H18.3361" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.6632 15.9998H13.6692" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <span>Корзина</span>
                    </div>

                    <div class="cart-step-line"></div>

                    <div class="cart-step">
                        <div class="cart-step-icon">

                            <svg width="" height="" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="31" height="31" rx="15.5" fill="white"/>
                                <rect x="0.5" y="0.5" width="31" height="31" rx="15.5" stroke="#D8D8D8"/>
                                <path d="M22.6668 15.9998V19.3332C22.6668 21.3332 21.3335 22.6665 19.3335 22.6665H12.6668C10.6668 22.6665 9.3335 21.3332 9.3335 19.3332V15.9998C9.3335 14.1865 10.4268 12.9198 12.1268 12.7065C12.3002 12.6798 12.4802 12.6665 12.6668 12.6665H19.3335C19.5068 12.6665 19.6735 12.6732 19.8335 12.6998C21.5535 12.8998 22.6668 14.1732 22.6668 15.9998Z" stroke="#585858" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19.8341 12.7002C19.6741 12.6735 19.5074 12.6668 19.3341 12.6668H12.6674C12.4808 12.6668 12.3008 12.6802 12.1274 12.7068C12.2208 12.5202 12.3541 12.3468 12.5141 12.1868L14.6808 10.0135C15.5941 9.10683 17.0741 9.10683 17.9874 10.0135L19.1541 11.1935C19.5808 11.6135 19.8074 12.1468 19.8341 12.7002Z" stroke="#585858" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22.6668 16.3335H20.6668C19.9335 16.3335 19.3335 16.9335 19.3335 17.6668C19.3335 18.4002 19.9335 19.0002 20.6668 19.0002H22.6668" stroke="#585858" stroke-linecap="round" stroke-linejoin="round"/>
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


            <section class="empty-cart">

                <h1>Ваша корзина пуста</h1>

                <div class="empty-cart-line"></div>


                <div class="empty-cart-content">

                    <div class="empty-cart-left">
                        <div class="empty-cart-text">
                            Ваша корзина сейчас пуста. Вернитесь в каталог.
                        </div>

                        <a href="#" class="empty-cart-button">
                            Продолжить покупки
                        </a>
                    </div>

                    <div class="empty-cart-right">

                        <div class="delivery-title">
                            Выберите товары для заказа
                        </div>

                        <div class="delivery-text">
                            А мы покажем, как работает доставка.
                        </div>

                        <div class="delivery-progress">
                            <div class="delivery-progress-filled"></div>
                        </div>

                        <div class="delivery-description">
                            Бесплатная доставка при заказе от 6 тысяч.
                        </div>

                        <a href="#" class="delivery-link">
                            Продолжить покупки
                        </a>


                        <div class="empty-cart-order">

                            <div class="empty-cart-order-title">
                                Ваш заказ
                            </div>

                            <div class="empty-cart-order-row">
                                <span>Товары (0)</span>
                                <span>0 р</span>
                            </div>

                            <div class="empty-cart-order-row empty-cart-order-total">
                                <span class="empty-span">Итого</span>
                                <span>0 р</span>
                            </div>

                            <button type="button" class="empty-cart-checkout">
                                Оформление заказа | 0 р
                            </button>

                        </div>

                    </div>

                </div>

            </section>
        </div>


    </main>
@endsection
