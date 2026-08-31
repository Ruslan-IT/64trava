@extends('layouts.app')

@section('title', 'Сидбанки | Dutch Seeds')

@section('seo')

    <meta name="description" content="Сидбанки">
    <meta name="keywords" content="Сидбанки, новинки, информация, Dutch Seeds">
    {{--<link rel="canonical" href="{{ url('/news') }}">
--}}
@endsection



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
                {{ $page->title }}
            </h1>

            <p class="delivery-intro">
                {{ $page->intro }}
            </p>


            <section class="delivery-method-section">

                <h2 class="delivery-section-title">
                    {{ $page->delivery_title }}
                </h2>

                <p class="delivery-section-description">
                    {{ $page->delivery_description }}
                </p>


                <div class="delivery-methods">

                    <div class="delivery-method">

                        <div class="delivery-method-image">
                            @if($page->delivery_method_1_image)
                                <img
                                    src="{{ asset('storage/' . $page->delivery_method_1_image) }}"
                                    alt="{{ $page->delivery_method_1_title }}"
                                >
                            @endif
                        </div>

                        <h3 class="delivery-method-title">
                            {{ $page->delivery_method_1_title }}
                        </h3>

                        <p class="delivery-method-text">
                            {{ $page->delivery_method_1_text }}
                        </p>

                    </div>


                    <div class="delivery-method">

                        <div class="delivery-method-image">
                            @if($page->delivery_method_2_image)
                                <img
                                    src="{{ asset('storage/' . $page->delivery_method_2_image) }}"
                                    alt="{{ $page->delivery_method_2_title }}"
                                >
                            @endif
                        </div>

                        <h3 class="delivery-method-title">
                            {{ $page->delivery_method_2_title }}
                        </h3>

                        <p class="delivery-method-text">
                            {{ $page->delivery_method_2_text }}
                        </p>

                    </div>

                </div>

            </section>


            <section class="delivery-payment-section">

                <h2 class="delivery-section-title">
                    {{ $page->payment_title }}
                </h2>

                <p class="delivery-section-description">
                    {{ $page->payment_description }}
                </p>


                <div class="delivery-payment-cards">

                    <div
                        class="delivery-payment-card"
                        @if($page->payment_image_1)
                            style="background-image: url('{{ asset('storage/' . $page->payment_image_1) }}');"
                        @endif
                    >
                    </div>

                    <div
                        class="delivery-payment-card"
                        @if($page->payment_image_2)
                            style="background-image: url('{{ asset('storage/' . $page->payment_image_2) }}');"
                        @endif
                    >
                    </div>

                    <div
                        class="delivery-payment-card"
                        @if($page->payment_image_3)
                            style="background-image: url('{{ asset('storage/' . $page->payment_image_3) }}');"
                        @endif
                    >
                    </div>

                </div>

            </section>


            <section class="delivery-info">

                <div class="delivery-info-row">

                    <div class="delivery-info-content">

                        <h2 class="delivery-info-title">
                            {{ $page->info_1_title }}
                        </h2>

                        <p class="delivery-info-text">
                            {{ $page->info_1_text }}
                        </p>

                    </div>

                    <div class="delivery-info-image">

                        @if($page->info_1_image)
                            <img
                                src="{{ asset('storage/' . $page->info_1_image) }}"
                                alt="{{ $page->info_1_title }}"
                            >
                        @endif

                    </div>

                </div>


                <div class="delivery-info-row delivery-info-row-reverse">

                    <div class="delivery-info-content">

                        <h2 class="delivery-info-title">
                            {{ $page->info_2_title }}
                        </h2>

                        <p class="delivery-info-text">
                            {{ $page->info_2_text }}
                        </p>

                    </div>

                    <div class="delivery-info-image">

                        @if($page->info_2_image)
                            <img
                                src="{{ asset('storage/' . $page->info_2_image) }}"
                                alt="{{ $page->info_2_title }}"
                            >
                        @endif

                    </div>

                </div>

            </section>

        </div>

    </main>

@endsection
