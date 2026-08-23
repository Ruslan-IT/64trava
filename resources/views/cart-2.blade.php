@extends('layouts.app')

@section('title', 'Главная')

@section('content')

    <main>
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


        <section class="order-success">

            <div class="container">
                <h1>Заказ номер 454654 оформлен</h1>

                <div class="order-success-content">
                    <div class="order-success-info">
                        <div class="order-success-text">
                            <p>Письмо с составом заказа было отправлено на вашу почту.</p>
                            <p>После отправки заказа вам придет трек-номер на электронную почту.</p>
                            <p>Пожалуйста внимательно проверьте данные, содержащиеся в ней. Если письмо не пришло или вы хотите внести изменения в данные по заказу, напишите в чат нашему консультанту. Перед этим, пожалуйста, проверьте папку спам.</p>
                        </div>


                    </div>

                    <div class="order-success-summary">
                        <div class="order-summary-row order-summary-total">
                            <span>Заказ на сумму</span>
                            <strong>1500 р</strong>
                        </div>

                        <div class="order-summary-row">
                            <span>Скидка:</span>
                            <span>152 р</span>
                        </div>

                        <div class="order-summary-row">
                            <span>Стоимость доставки</span>
                            <span>100 р</span>
                        </div>

                        <div class="order-summary-row order-summary-result">
                            <span>Итого</span>
                            <strong>1500 р</strong>
                        </div>
                    </div>
                </div>
                <div class="order-support">

                    <div class="order-success-promo">
                        <div class="order-success-promo-title">
                            Вы получили промокод на 4% на будущие покупки.
                        </div>

                        <div class="order-success-promo-code">
                            <span>Ваш промокод</span>
                            <strong>ABC123</strong>
                            <button type="button">
                                <!-- иконка копирования -->
                            </button>
                        </div>
                    </div>
                    <div class="order-success-promo-2">
                        <div class="order-support-title">Служба поддержки клиентов</div>

                        <div class="order-support-item">
                            <div class="order-support-icon">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="20" cy="20" r="20" fill="#35A0D6"/>
                                    <path d="M26.9857 14.2088C27.1182 13.4033 26.3045 12.7676 25.5436 13.082L10.3876 19.3448C9.84193 19.5703 9.88185 20.3483 10.4478 20.5179L13.5733 21.4547C14.1699 21.6335 14.8158 21.541 15.3367 21.2023L22.3834 16.6203C22.5959 16.4821 22.8275 16.7665 22.646 16.9426L17.5736 21.8646C17.0816 22.3421 17.1792 23.1512 17.7711 23.5005L23.4501 26.8523C24.0871 27.2282 24.9065 26.8506 25.0257 26.1261L26.9857 14.2088Z" fill="white"/>
                                </svg>
                            </div>
                            <span>Написать консультанту</span>
                        </div>

                        <div class="order-support-item">
                            <div class="order-support-icon">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M40 20C40 31.0457 31.0457 40 20 40C15.7869 40 11.8781 38.6973 8.65443 36.4728L1.81818 38.1818L3.6096 31.464C1.33487 28.2178 0 24.2648 0 20C0 8.9543 8.9543 0 20 0C31.0457 0 40 8.9543 40 20Z" fill="url(#paint0_linear_16455_9051)"/>
                                    <path d="M13.8442 8.95426C13.321 7.90961 12.5186 8.00209 11.7079 8.00209C10.2591 8.00209 8 9.72754 8 12.9388C8 15.5705 9.16639 18.4514 13.0967 22.7609C16.8898 26.92 21.8737 29.0714 26.0112 28.9982C30.1487 28.9249 31 25.3849 31 24.1894C31 23.6595 30.6693 23.3951 30.4414 23.3233C29.0315 22.6505 26.431 21.3969 25.8393 21.1614C25.2476 20.9258 24.9387 21.2444 24.7466 21.4177C24.21 21.9261 23.1464 23.4245 22.7822 23.7615C22.418 24.0985 21.8751 23.9279 21.6491 23.8005C20.8177 23.4688 18.5633 22.4718 16.7663 20.7398C14.544 18.5979 14.4135 17.8609 13.9948 17.205C13.6599 16.6802 13.9057 16.3582 14.0283 16.2175C14.5071 15.6682 15.1683 14.8201 15.4648 14.3986C15.7613 13.9772 15.5259 13.3373 15.3847 12.9388C14.7772 11.2248 14.2626 9.78999 13.8442 8.95426Z" fill="white"/>
                                    <defs>
                                        <linearGradient id="paint0_linear_16455_9051" x1="37.5" y1="5" x2="-9.68575e-07" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#5BD066"/>
                                            <stop offset="1" stop-color="#27B43E"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            <span>Связаться с поддержкой</span>
                        </div>
                    </div>

                </div>

            </div>





        </section>




    </main>
@endsection
