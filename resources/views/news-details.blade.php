@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <div class="container">
        <nav class="breadcrumbs">
            <a href="#">Главная</a>
            <span>-</span>

            <a href="#" class="breadcrumbs-current">Сидбанки</a>
        </nav>
    </div>

    <main class="news-detail-page">

        <div class="news-detail-container">

            <div class="news-detail-layout">

                <!-- ЛЕВАЯ ЧАСТЬ -->

                <article class="news-detail-content">

                    <h1 class="news-detail-title">
                        Новая коллекция уже доступна
                        в нашем магазине
                    </h1>


                    <!-- META -->

                    <div class="news-detail-meta">

                        <div class="news-detail-meta-item">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M5 2V4M13 2V4M3 7H15M4 3H14C14.5523 3 15 3.44772 15 4V15C15 15.5523 14.5523 16 14 16H4C3.44772 16 3 15.5523 3 15V4C3 3.44772 3.44772 3 4 3Z"
                                      stroke="currentColor"
                                      stroke-width="1.5"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <span>10 июля 25</span>
                        </div>


                        <span class="news-detail-meta-dot">•</span>


                        <div class="news-detail-meta-item">

                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M2 9C2 9 4.5 4 9 4C13.5 4 16 9 16 9C16 9 13.5 14 9 14C4.5 14 2 9 2 9Z"
                                      stroke="currentColor"
                                      stroke-width="1.5"/>
                                <circle cx="9" cy="9" r="2"
                                        stroke="currentColor"
                                        stroke-width="1.5"/>
                            </svg>

                            <span>24K просмотров</span>

                        </div>


                        <span class="news-detail-meta-dot">•</span>


                        <div class="news-detail-meta-item">

                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <circle cx="9" cy="9" r="6.5"
                                        stroke="currentColor"
                                        stroke-width="1.5"/>
                                <path d="M9 5.5V9L11.5 10.5"
                                      stroke="currentColor"
                                      stroke-width="1.5"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <span>2 минуты чтения</span>

                        </div>

                    </div>


                    <!-- ТЕКСТ НОВОСТИ -->

                    <div class="news-detail-text">

                        <p>
                            Мы рады представить новую коллекцию товаров,
                            которая уже доступна в нашем магазине.
                        </p>

                        <p>
                            В новой коллекции мы собрали популярные позиции
                            и несколько интересных новинок. Каждый товар был
                            выбран с особым вниманием к качеству и характеристикам.
                        </p>

                        <p>
                            Мы постоянно работаем над расширением ассортимента,
                            чтобы вы могли находить подходящие товары и получать
                            максимум удовольствия от покупок.
                        </p>

                        <p>
                            Следите за обновлениями нашего каталога, чтобы не
                            пропустить новые поступления, специальные предложения
                            и другие интересные новости нашего магазина.
                        </p>

                    </div>


                    <!-- НАВИГАЦИЯ -->

                    <div class="news-detail-navigation">

                        <a href="#" class="news-detail-navigation-button ">
                            Предыдущая новость
                        </a>

                        <a href="#" class="news-detail-navigation-button">
                            Следующая новость
                        </a>

                    </div>

                </article>


                <!-- ПРАВАЯ ЧАСТЬ -->

                <div class="news-detail-image">

                    <img src="images/news-detail.jpg"
                         alt="Новая коллекция">

                </div>

            </div>

        </div>

    </main>
@endsection
