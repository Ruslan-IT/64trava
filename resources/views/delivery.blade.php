@extends('layouts.app')

@section('title', 'Главная')

@section('content')




    <div class="container">
        <nav class="breadcrumbs">
            <a href="#">Главная</a>
            <span>-</span>

            <a href="#" class="breadcrumbs-current">Доставка и оплата</a>
        </nav>
    </div>


    <main class="delivery-page">

        <div class="delivery-container">

            <h1 class="delivery-title">
                Доставка и оплата
            </h1>

            <p class="delivery-intro">
                Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh.
            </p>


            <!-- СПОСОБ ДОСТАВКИ №1 -->

            <section class="delivery-method-section">

                <h2 class="delivery-section-title">
                    Способ доставки
                </h2>

                <p class="delivery-section-description">
                    Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh.
                </p>


                <div class="delivery-methods">

                    <div class="delivery-method">

                        <div class="delivery-method-image">
                            <img src="images/russian-post.jpg" alt="Почта России">
                        </div>

                        <h3 class="delivery-method-title">
                            Почта России
                        </h3>

                        <p class="delivery-method-text">
                            Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh.
                        </p>

                    </div>


                    <div class="delivery-method">

                        <div class="delivery-method-image">
                            <img src="images/cdek.jpg" alt="СДЭК">
                        </div>

                        <h3 class="delivery-method-title">
                            СДЭК
                        </h3>

                        <p class="delivery-method-text">
                            Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh.
                        </p>

                    </div>

                </div>

            </section>


            <!-- СПОСОБ ДОСТАВКИ №2 -->

            <section class="delivery-payment-section">

                <h2 class="delivery-section-title">
                    Способ оплаты
                </h2>

                <p class="delivery-section-description">
                    Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh.
                </p>


                <div class="delivery-payment-cards">

                    <div class="delivery-payment-card"
                         style="background-image: url('images/payment-1.jpg');">
                    </div>

                    <div class="delivery-payment-card"
                         style="background-image: url('images/payment-2.jpg');">
                    </div>

                    <div class="delivery-payment-card"
                         style="background-image: url('images/payment-3.jpg');">
                    </div>

                </div>

            </section>


            <!-- ИНФОРМАЦИОННЫЕ БЛОКИ -->

            <section class="delivery-info">

                <div class="delivery-info-row">

                    <div class="delivery-info-content">

                        <h2 class="delivery-info-title">
                            Оформление заказа
                        </h2>

                        <p class="delivery-info-text">
                            Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh.
                        </p>

                    </div>

                    <div class="delivery-info-image">
                        <img src="images/delivery-info-2.jpg"
                             alt="Оформление заказа">
                    </div>

                </div>


                <div class="delivery-info-row delivery-info-row-reverse">

                    <div class="delivery-info-content">

                        <h2 class="delivery-info-title">
                            Получение заказа
                        </h2>

                        <p class="delivery-info-text">
                            Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh. Lorem ipsum dolor sit amet consectetur. Mi a enim mauris felis nibh. Rhoncus varius in nibh pharetra eu. Libero mauris habitasse amet risus faucibus tincidunt nunc lorem. Nullam nulla sed nec est amet nibh.
                        </p>

                    </div>

                    <div class="delivery-info-image">
                        <img src="images/delivery-info-1.jpg"
                             alt="Получение заказа">
                    </div>

                </div>

            </section>

        </div>

    </main>
@endsection
