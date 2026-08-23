@extends('layouts.app')

@section('title', 'Главная')

@section('content')


    <main class="news-page">

        <div class="news-container">

            <!-- ФИЛЬТРЫ -->

            <div class="news-filters">

                <button type="button" class="news-filter active">
                    Все новости
                </button>

                <button type="button" class="news-filter">
                    Новинки
                </button>

                <button type="button" class="news-filter">
                    Акции
                </button>

                <button type="button" class="news-filter">
                    Бонусы
                </button>

                <button type="button" class="news-filter">
                    Графики работ
                </button>

            </div>


            <!-- ЗАГОЛОВОК -->

            <h1 class="news-title">
                Новости
            </h1>


            <!-- НОВОСТИ -->

            <div class="news-grid">


                <!-- НОВОСТЬ 1 -->

                <article class="news-card">

                    <a href="#" class="news-card-image">
                        <img src="images/news/news-1.jpg" alt="Новость">
                    </a>

                    <div class="news-card-meta">

                    <span class="news-card-category">
                        Новинки
                    </span>

                        <time class="news-card-date">
                            10 июня 25
                        </time>

                    </div>

                    <h2 class="news-card-title">
                        Новая коллекция уже доступна
                        в нашем магазине
                    </h2>

                    <p class="news-card-text">
                        Мы подготовили для вас новые
                        товары и интересные предложения.
                        Узнайте больше о новинках.
                    </p>

                    <a href="#" class="news-card-link">
                        Читать далее
                    </a>

                </article>


                <!-- НОВОСТЬ 2 -->

                <article class="news-card">

                    <a href="#" class="news-card-image">
                        <img src="images/news/news-2.jpg" alt="Новость">
                    </a>

                    <div class="news-card-meta">

                    <span class="news-card-category">
                        Новинки
                    </span>

                        <time class="news-card-date">
                            8 июня 25
                        </time>

                    </div>

                    <h2 class="news-card-title">
                        Встречайте новые сорта
                        в нашем каталоге
                    </h2>

                    <p class="news-card-text">
                        Добавили новые позиции
                        в каталог. Уже сейчас можно
                        ознакомиться с ассортиментом.
                    </p>

                    <a href="#" class="news-card-link">
                        Читать далее
                    </a>

                </article>


                <!-- НОВОСТЬ 3 -->

                <article class="news-card">

                    <a href="#" class="news-card-image">
                        <img src="images/news/news-3.jpg" alt="Новость">
                    </a>

                    <div class="news-card-meta">

                    <span class="news-card-category">
                        Акции
                    </span>

                        <time class="news-card-date">
                            5 июня 25
                        </time>

                    </div>

                    <h2 class="news-card-title">
                        Большая летняя акция
                        для наших покупателей
                    </h2>

                    <p class="news-card-text">
                        Только в этом месяце действуют
                        специальные условия и приятные
                        скидки на популярные товары.
                    </p>

                    <a href="#" class="news-card-link">
                        Читать далее
                    </a>

                </article>
                <article class="news-card">

                    <a href="#" class="news-card-image">
                        <img src="images/news/news-3.jpg" alt="Новость">
                    </a>

                    <div class="news-card-meta">

                    <span class="news-card-category">
                        Акции
                    </span>

                        <time class="news-card-date">
                            5 июня 25
                        </time>

                    </div>

                    <h2 class="news-card-title">
                        Большая летняя акция
                        для наших покупателей
                    </h2>

                    <p class="news-card-text">
                        Только в этом месяце действуют
                        специальные условия и приятные
                        скидки на популярные товары.
                    </p>

                    <a href="#" class="news-card-link">
                        Читать далее
                    </a>

                </article>



            </div>

        </div>

    </main>
@endsection
