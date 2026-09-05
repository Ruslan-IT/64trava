@extends('layouts.app')

@section('title', 'Главная')

@section('content')

    <main>

        <section class="mobile-welcome">

            <p>
                Добро пожаловать в магазин семян конопли Dutch Seeds!
                Мы заботимся о Вашем комфорте и безопасности.
            </p>

        </section>

        <section class="brands-section">

            <div class="container">

                <div class="brands-inner">

                    @foreach($brands as $brand)

                        <div class="brands-column">
                            <a href="{{ route('catalog.brand', $brand->slug ) }}">{{ $brand->name  }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="category-buttons">
            <div class="container">
                <div class="category-buttons-inner">

                    @foreach($categories as $category)
                        <a href="{{ route('catalog.category', $category->slug) }}"
                           class="category-button">
                            {{ $category->name }}
                        </a>
                    @endforeach

                </div>


            </div>


        </section>

        <section class="hero-banner">
            <picture>
                <source
                    media="(max-width: 768px)"
                    srcset="{{ asset('images/banner-mobile-2.jpg') }}"
                >

                <img
                    src="{{ asset('images/banner-desktop.jpg') }}"
                    alt="Amnesia Lemon"
                >
            </picture>
            <div class="container">


                <!-- Текст только для desktop -->


                <div class="hero-banner-content">

                    <div class="hero-label">
                        Бестселлер
                    </div>


                    <h1 class="hero-title">
                        <p class="hero-title-new">NEW</p>
                        <p class="hero-title-releases">releases</p>
                    </h1>


                    <p class="hero-description">
                        Amnesia Lemon — это сорт, созданный совместными
                        усилиями селекционеров Barneys Farm и Soma,
                        выигравшей Кубок Каннабиса в 2004 году.
                    </p>

                </div>
            </div>


        </section>

        <section class="tags-section">

            <div class="container">

                <div class="tags-inner" id="tagsContainer">

                    @foreach($tags as $tag)
                        <a href="{{ route('catalog.tag', $tag->slug)  }}" class="tag-button">{{ $tag->name }}</a>
                    @endforeach

                </div>

                <div class="tags-inner-href">
                    <button type="button" id="showAllTags"> Посмотреть все теги</button>
                </div>


            </div>

        </section>

        <section class="brands-guide">

            <div class="container">

                <h2 class="brands-guide-title">
                    Ваш гид по миру проверенных брендов
                </h2>

                <p class="brands-guide-text">
                    Мы работаем с лучшими селекционерами и брендами, чтобы предложить
                    покупателям только проверенные генетики. Наш магазин — это
                    прозрачность, внимание к деталям и уважение к каждому клиенту.
                    Доставляем качество, сопровождаем информацией и гарантируем
                    легальность. С нами удобно, безопасно и просто.
                </p>

            </div>

        </section>

        <section class="new-products one">

            <div class="container">

                <div class="new-products-header">

                    <h2 class="new-products-title">
                        Новинки
                    </h2>
                    <div class="new-products-row">

                        <div class="new-products-buttons">


                            @foreach($categories as $category)
                                <a href="{{ route('catalog.category', $category->slug) }}"
                                   class="new-products-button">
                                    {{ $category->name }}
                                </a>
                            @endforeach

                        </div>
                        <div class="new-products-arrows">

                            <button
                                type="button"
                                class="slider-arrow products-slider-prev"
                                aria-label="Назад"
                            >
                                <svg width="8" height="15" viewBox="0 0 8 15" fill="none">
                                    <path
                                        d="M6.66458 13.95L1.23125 8.51667C0.589583 7.875 0.589583 6.825 1.23125 6.18333L6.66458 0.75"
                                        stroke="#585858"
                                        stroke-width="1.5"
                                        stroke-miterlimit="10"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="slider-arrow active products-slider-next"
                                aria-label="Вперёд"
                            >
                                <svg width="8" height="15" viewBox="0 0 8 15" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0.750456 0.750194L6.18379 6.18353C6.82546 6.8252 6.82546 7.87519 6.18379 8.51686L0.750455 13.9502"
                                        stroke="#585858" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                        </div>
                    </div>

                </div>

            </div>

        </section>

        <section class="products-slider-wrapper">

            <div class="container">

                <div class="products-grid">

                    {{-- =========================
                          PROMO
                     ========================== --}}

                    @php
                        $promoProduct = $newProducts->firstWhere('is_promo', true);


                    @endphp

                    @if($promoProduct)

                        <article class="products-promo">

                            <img
                                src="{{ asset('storage/' . $promoProduct->image) }}"
                                alt="{{ $promoProduct->name }}"
                                class="products-promo-image"
                            >

                            <h2 class="products-promo-title">
                                {{ $promoProduct->name }}
                            </h2>

                            <p class="products-promo-text">
                                {{ $promoProduct->brand->name }}
                            </p>

                            <a href="#" class="products-promo-link">
                                Показать все {{ $promoProduct->id }}
                            </a>

                        </article>

                    @endif





                    @foreach($newProducts as $product)
                        @include('components.product-card', ['product' => $product, 'livewireKey' => 'new-' . $product->id,])
                    @endforeach

                </div>


            </div>

        </section>

        <!-- =========================
        Новости
       ========================= -->
        <section class="news news-slider">

            <div class="container">
                <div class="news-header">
                    <h2>Новости</h2>

                    <div class="news-header-actions">
                        <a href="#">Все новости3</a>

                        <button class="news-slider-btn news-slider-btn--prev slider-arrow"></button>
                        <button class="news-slider-btn news-slider-btn--next slider-arrow"></button>
                    </div>
                </div>

                <div class="news-list">


                    @foreach($newsBlock as $news)
                        <article class="news-card">
                            <img
                                src="{{ asset('storage/' . $news->image) }}"
                                alt="{{ $news->title }}"
                            >

                            <div class="news-card-meta">
                                <span class="news-card-category">
                                    {{ $news->category }}
                                </span>

                                <time datetime="{{ $news->published_at->format('Y-m-d') }}">
                                    {{ $news->published_at->locale('ru')->translatedFormat('d F Y') }}
                                </time>
                            </div>

                            <h3>{{ $news->title }}</h3>

                            <p>
                                {{ $news->excerpt }}
                            </p>

                            <a href="{{ route('news.show', $news) }}" class="news-card-link">
                                Читать далее
                            </a>
                        </article>
                    @endforeach


                </div>
            </div>

        </section>

        <!-- =========================
         Каталог
        ========================= -->
        <section class="new-products">

            <div class="container">

                <div class="new-products-header">

                    <h2 class="new-products-title">
                        Каталог
                    </h2>


                    <div class="catalog-popular-header">

                        <div class="catalog-popular-title">
                            Наиболее популярные
                        </div>

                        <a href="{{ route('catalog.index') }}" class="catalog-popular-link">
                            Смотреть все
                        </a>

                    </div>


                    <div class="new-products-row">

                        <div class="catalog-products-home">




                                @foreach($productPopular as $product)
                                    @include('components.product-card', [
                                        'product' => $product,
                                        'livewireKey' => 'productPopular-' . $product->id,
                                    ])
                                @endforeach

                        </div>

                    </div>

                </div>


            </div>

        </section>


        <!-- =========================
         Крупные пачки
        ========================= -->
        <section class="new-products">

            <div class="container">

                <div class="new-products-header">


                    <div class="catalog-popular-header">

                        <div class="catalog-popular-title">
                            <h2 class="new-products-title">
                                Крупные пачки
                            </h2>
                        </div>

                        <a href="{{ route('catalog.category', ['category' => 'krupnye-packi']) }}"
                           class="catalog-popular-link">
                            Смотреть все
                        </a>

                    </div>


                    <div class="new-products-row">

                        <div class="catalog-products-home">

                            @foreach($largePackProducts as $product)
                                @include('components.product-card', [
                                    'product' => $product,
                                    'livewireKey' => 'largePackProducts-' . $product->id,
                                ])
                            @endforeach

                        </div>

                    </div>

                </div>


            </div>

        </section>


        <!-- =========================
         Популярные
        ========================= -->
        <section class="new-products">

            <div class="container">

                <div class="new-products-header">


                    <div class="catalog-popular-header">

                        <div class="catalog-popular-title">
                            <h2 class="new-products-title">
                                Популярые
                            </h2>
                        </div>

                        <a href="{{ route('catalog.category', ['category' => 'naibolee-populiarnye']) }}"
                           class="catalog-popular-link">
                            Смотреть все
                        </a>

                    </div>


                    <div class="new-products-row">

                        <div class="catalog-products-home">


                            @foreach($productPopular as $product)
                                @include('components.product-card', [
                                    'product' => $product,
                                    'livewireKey' => 'productPopular-' . $product->id,
                                ])
                            @endforeach

                        </div>

                    </div>

                </div>


            </div>

        </section>


        <!-- =========================
               Хотите получать новости первыми?
              ========================= -->
        <section class="subscribe">
            <div class="container subscribe-container">
                <div class="subscribe-content">
                    <h2>Хотите получать новости первыми?</h2>

                    <p>Тогда подпишитесь на телеграм-канал прямо сейчас.</p>
                </div>

                <form class="subscribe-form">
                    <input type="email" placeholder="Ваш email">
                    <button type="submit">Подписаться</button>
                </form>
            </div>
        </section>


        <section class="new-products one">

            <div class="container">

                <div class="new-products-header">

                    <h2 class="new-products-title">
                        Товары на акции
                    </h2>
                    <div class="new-products-row">

                        <div class="new-products-buttons">


                            @foreach($categories as $category)
                                <a href="{{ route('catalog.category', $category->slug) }}"
                                   class="new-products-button">
                                    {{ $category->name }}
                                </a>
                            @endforeach

                        </div>
                        <div class="new-products-arrows">

                            <button
                                type="button"
                                class="slider-arrow products-slider-prev"
                                aria-label="Назад"
                            >
                                <svg width="8" height="15" viewBox="0 0 8 15" fill="none">
                                    <path
                                        d="M6.66458 13.95L1.23125 8.51667C0.589583 7.875 0.589583 6.825 1.23125 6.18333L6.66458 0.75"
                                        stroke="#585858"
                                        stroke-width="1.5"
                                        stroke-miterlimit="10"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="slider-arrow active products-slider-next"
                                aria-label="Вперёд"
                            >
                                <svg width="8" height="15" viewBox="0 0 8 15" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0.750456 0.750194L6.18379 6.18353C6.82546 6.8252 6.82546 7.87519 6.18379 8.51686L0.750455 13.9502"
                                        stroke="#585858" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                        </div>
                    </div>

                </div>

            </div>

        </section>


        <section class="products-slider-wrapper">

            <div class="container">

                <div class="products-grid">

                    {{-- =========================
                          PROMO
                     ========================== --}}

                    @php
                        //$promoProduct = $newProducts->firstWhere('is_promo', true);

                    @endphp

                    @if($promoProduct)

                        <article class="products-promo">

                            <img
                                src="{{ asset('storage/' . $promoProduct->image) }}"
                                alt="{{ $promoProduct->name }}"
                                class="products-promo-image"
                            >

                            <h2 class="products-promo-title">
                                {{ $promoProduct->name }}
                            </h2>

                            <p class="products-promo-text">
                                {{ $promoProduct->brand->name }}
                            </p>

                            <a href="#" class="products-promo-link">
                                Показать все {{ $promoProduct->id }}
                            </a>

                        </article>

                    @endif

                    @foreach($discountProducts as $product)
                        @include('components.product-card', [
                            'product' => $product,
                            'livewireKey' => 'discount-' . $product->id,
                        ])
                    @endforeach

                </div>


            </div>

        </section>


        <section class="advantages">
            <div class="container">
                <h2 class="advantages-title">Наши преимущества</h2>

                <div class="advantages-list">

                    <article class="advantage">

                        <div class="advantage-number-block">
                            <div class="advantage-number">1</div>

                            <img
                                class="advantage-image"
                                src="images/icon/advantage-1.png"
                                alt=""
                            >
                        </div>


                        <h3>Огромный ассортимент</h3>

                        <p>
                            Мы все совершенно разные люди со своими предпочтениями,
                            пожеланиями и возможностями.
                        </p>
                    </article>

                    <article class="advantage">
                        <div class="advantage-number-block">
                            <div class="advantage-number">2</div>

                            <img
                                class="advantage-image"
                                src="images/icon/advantage-2.png"
                                alt=""
                            >
                        </div>

                        <h3>Высокое качество</h3>

                        <p>
                            Мы тщательно подбираем продукцию и работаем только
                            с проверенными производителями.
                        </p>
                    </article>

                    <article class="advantage">
                        <div class="advantage-number-block">
                            <div class="advantage-number">3</div>

                            <img
                                class="advantage-image"
                                src="images/icon/advantage-3.png"
                                alt=""
                            >
                        </div>

                        <h3>Гарантия качества</h3>

                        <p>
                            Каждый товар проходит проверку, чтобы наши покупатели
                            получали только качественную продукцию.
                        </p>
                    </article>

                    <article class="advantage">
                        <div class="advantage-number-block">
                            <div class="advantage-number">4</div>

                            <img
                                class="advantage-image"
                                src="images/icon/advantage-4.png"
                                alt=""
                            >
                        </div>

                        <h3>Удобная доставка</h3>

                        <p>
                            Быстро доставляем заказы и сопровождаем покупателя
                            на всех этапах оформления.
                        </p>
                    </article>

                </div>
            </div>
        </section>


        <section class="news-section">
            <div class="container">
                <h2 class="news-section-title">Новости4</h2>

                <div class="news-section-list">

                    @foreach($newsBlock as $key => $newsBlocks)

                        @php
                            if($key === 3)  break;
                        @endphp

                        <article class="news-section-card">
                            <img
                                src="{{ asset('storage/' . $newsBlocks->image) }}"
                                alt=""
                                class="news-section-card-image"
                            >

                            <h3 class="news-section-card-title">
                                {{ $newsBlocks->title }}
                            </h3>

                            <p class="news-section-card-text">
                                {!! $newsBlocks->excerpt !!}
                            </p>

                            <a href="{{ route('news.show', $newsBlocks->slug) }}" class="news-section-card-link">
                                Читать далее
                            </a>
                        </article>
                    @endforeach


                </div>

                <div class="news-section-bottom">
                    <p>
                        Lorem ipsum dolor sit amet consectetur. Sagittis morbi diam sapien scelerisque pellentesque non
                        purus amet. Amet tellus pulvinar dis potenti. Consectetur quisque sed consectetur euismod mattis
                        laoreet tristique hendrerit blandit. Amet rutrum viverra semper vulputate. Turpis vulputate sit
                        pulvinar diam ac. Ipsum posuere scelerisque ipsum etiam tempus posuere luctus nunc sem. Quisque
                        elementum pellentesque adipiscing pellentesque varius ipsum a. Eu pharetra pharetra nunc leo
                        scelerisque fames elit sit donec. Vestibulum a pharetra placerat cursus leo est sit leo
                        facilisis.
                        Praesent mauris ultricies egestas nam sagittis quis eget.
                    </p>

                    <p>
                        Lorem ipsum dolor sit amet consectetur. Sagittis morbi diam sapien scelerisque pellentesque non
                        purus amet. Amet tellus pulvinar dis potenti. Consectetur quisque sed consectetur euismod mattis
                        laoreet tristique hendrerit blandit. Amet rutrum viverra semper vulputate. Turpis vulputate sit
                        pulvinar diam ac. Ipsum posuere scelerisque ipsum etiam tempus posuere luctus nunc sem. Quisque
                        elementum pellentesque adipiscing pellentesque varius ipsum a. Eu pharetra pharetra nunc leo
                        scelerisque fames elit sit donec. Vestibulum a pharetra placerat cursus leo est sit leo
                        facilisis.
                        Praesent mauris ultricies egestas nam sagittis quis eget.
                    </p>

                    <p>
                        Lorem ipsum dolor sit amet consectetur. Sagittis morbi diam sapien scelerisque pellentesque non
                        purus amet. Amet tellus pulvinar dis potenti. Consectetur quisque sed consectetur euismod mattis
                        laoreet tristique hendrerit blandit. Amet rutrum viverra semper vulputate. Turpis vulputate sit
                        pulvinar diam ac. Ipsum posuere scelerisque ipsum etiam tempus posuere luctus nunc sem. Quisque
                        elementum pellentesque adipiscing pellentesque varius ipsum a. Eu pharetra pharetra nunc leo
                        scelerisque fames elit sit donec. Vestibulum a pharetra placerat cursus leo est sit leo
                        facilisis.
                        Praesent mauris ultricies egestas nam sagittis quis eget.
                    </p>

                </div>
            </div>
        </section>


    </main>


    <script>

        /*
        *
        * Слайдер новинки
        *
        * */
        document.addEventListener('DOMContentLoaded', function () {

            const slider = document.querySelector('.products-grid');
            const prevButton = document.querySelector('.products-slider-prev');
            const nextButton = document.querySelector('.products-slider-next');

            if (!slider || !prevButton || !nextButton) {
                return;
            }

            const cards = slider.children;

            if (cards.length <= 4) {
                prevButton.disabled = true;
                nextButton.disabled = true;
                return;
            }

            let currentPosition = 0;

            // ==========================
            // НАСТРОЙКИ DRAG / SWIPE
            // ==========================

            let isDragging = false;
            let startX = 0;
            let startTranslate = 0;
            let currentTranslate = 0;

            function getStep() {

                const card = cards[0];

                if (!card) {
                    return 0;
                }

                const cardWidth = card.getBoundingClientRect().width;

                // расстояние между карточками
                const gap = 35;

                return cardWidth + gap;
            }

            function getMaxPosition() {

                const visibleCards = 4;

                return Math.max(0, cards.length - visibleCards);
            }

            function getTranslate(position) {

                return -(position * getStep());
            }

            function updateSlider(animate = true) {

                const maxPosition = getMaxPosition();

                currentPosition = Math.max(
                    0,
                    Math.min(currentPosition, maxPosition)
                );

                const translate = getTranslate(currentPosition);

                slider.style.transition = animate
                    ? 'transform 0.35s ease'
                    : 'none';

                slider.style.transform = `translateX(${translate}px)`;

                prevButton.disabled = currentPosition === 0;
                nextButton.disabled = currentPosition >= maxPosition;

                prevButton.classList.toggle(
                    'active',
                    currentPosition > 0
                );

                nextButton.classList.toggle(
                    'active',
                    currentPosition < maxPosition
                );
            }

            // ==========================
            // СТРЕЛКА ВПЕРЁД
            // ==========================

            nextButton.addEventListener('click', function () {

                const maxPosition = getMaxPosition();

                if (currentPosition < maxPosition) {
                    currentPosition++;
                    updateSlider();
                }

            });

            // ==========================
            // СТРЕЛКА НАЗАД
            // ==========================

            prevButton.addEventListener('click', function () {

                if (currentPosition > 0) {
                    currentPosition--;
                    updateSlider();
                }

            });


            // ==========================
            // MOUSE DRAG
            // ==========================

            slider.addEventListener('mousedown', function (e) {

                // Только левая кнопка мыши
                if (e.button !== 0) {
                    return;
                }

                isDragging = true;

                startX = e.clientX;

                startTranslate = getTranslate(currentPosition);

                currentTranslate = startTranslate;

                slider.style.transition = 'none';

                slider.classList.add('is-dragging');

                e.preventDefault();
            });


            document.addEventListener('mousemove', function (e) {

                if (!isDragging) {
                    return;
                }

                const diff = e.clientX - startX;

                currentTranslate = startTranslate + diff;

                const maxTranslate = 0;
                const minTranslate = getTranslate(getMaxPosition());

                // Не даём вытянуть слайдер слишком далеко
                if (currentTranslate > maxTranslate) {

                    currentTranslate =
                        maxTranslate + (currentTranslate - maxTranslate) * 0.25;

                }

                if (currentTranslate < minTranslate) {

                    currentTranslate =
                        minTranslate + (currentTranslate - minTranslate) * 0.25;

                }

                slider.style.transform =
                    `translateX(${currentTranslate}px)`;

            });


            document.addEventListener('mouseup', function () {

                if (!isDragging) {
                    return;
                }

                isDragging = false;

                slider.classList.remove('is-dragging');

                const step = getStep();

                const movedDistance =
                    currentTranslate - startTranslate;

                // Если перетащили достаточно далеко
                if (Math.abs(movedDistance) > step * 0.2) {

                    if (movedDistance < 0) {

                        // Влево → следующий
                        currentPosition++;

                    } else {

                        // Вправо → предыдущий
                        currentPosition--;

                    }

                }

                updateSlider(true);

            });


            // ==========================
            // TOUCH / SWIPE
            // ==========================

            slider.addEventListener(
                'touchstart',
                function (e) {

                    if (e.touches.length !== 1) {
                        return;
                    }

                    isDragging = true;

                    startX = e.touches[0].clientX;

                    startTranslate = getTranslate(currentPosition);

                    currentTranslate = startTranslate;

                    slider.style.transition = 'none';

                },
                {passive: true}
            );


            slider.addEventListener(
                'touchmove',
                function (e) {

                    if (!isDragging || e.touches.length !== 1) {
                        return;
                    }

                    const diff =
                        e.touches[0].clientX - startX;

                    currentTranslate =
                        startTranslate + diff;

                    const maxTranslate = 0;
                    const minTranslate =
                        getTranslate(getMaxPosition());

                    if (currentTranslate > maxTranslate) {

                        currentTranslate =
                            maxTranslate +
                            (currentTranslate - maxTranslate) * 0.25;

                    }

                    if (currentTranslate < minTranslate) {

                        currentTranslate =
                            minTranslate +
                            (currentTranslate - minTranslate) * 0.25;

                    }

                    slider.style.transform =
                        `translateX(${currentTranslate}px)`;

                },
                {passive: true}
            );


            slider.addEventListener(
                'touchend',
                function () {

                    if (!isDragging) {
                        return;
                    }

                    isDragging = false;

                    const step = getStep();

                    const movedDistance =
                        currentTranslate - startTranslate;

                    if (Math.abs(movedDistance) > step * 0.2) {

                        if (movedDistance < 0) {

                            currentPosition++;

                        } else {

                            currentPosition--;

                        }

                    }

                    updateSlider(true);

                }
            );


            // ==========================
            // RESIZE
            // ==========================

            window.addEventListener('resize', function () {
                updateSlider(false);
            });


            // ==========================
            // START
            // ==========================

            updateSlider(false);

        });
    </script>


    <script>

        /*
        *
        * Слайдер новости
        *
        * */
        document.addEventListener('DOMContentLoaded', function () {

            const slider = document.querySelector('.news-slider');
            const track = document.querySelector('.news-list');

            const prevButton = document.querySelector('.news-header-actions .slider-arrow:first-of-type');
            const nextButton = document.querySelector('.news-header-actions .slider-arrow:last-of-type');

            if (!slider || !track || !prevButton || !nextButton) {
                return;
            }

            let currentPosition = 0;

            let isDragging = false;
            let startX = 0;
            let currentX = 0;
            let startPosition = 0;

            function getGap() {

                const styles = window.getComputedStyle(track);

                return parseFloat(styles.gap) || 0;
            }

            function getCardWidth() {

                const card = track.querySelector('.news-card');

                if (!card) {
                    return 0;
                }

                return card.getBoundingClientRect().width;
            }

            function getVisibleCards() {

                if (window.innerWidth <= 520) {
                    return 1;
                }

                if (window.innerWidth <= 768) {
                    return 2;
                }

                if (window.innerWidth <= 1200) {
                    return 3;
                }

                return 4;
            }

            function getMaxPosition() {

                const visibleCards = getVisibleCards();

                return Math.max(
                    0,
                    track.children.length - visibleCards
                );
            }

            function updateSlider(animate = true) {

                const step = getCardWidth() + getGap();

                if (!animate) {
                    track.style.transition = 'none';
                } else {
                    track.style.transition = 'transform 0.35s ease';
                }

                track.style.transform =
                    `translate3d(-${currentPosition * step}px, 0, 0)`;

                prevButton.disabled = currentPosition <= 0;
                nextButton.disabled = currentPosition >= getMaxPosition();

                prevButton.classList.toggle(
                    'active',
                    currentPosition > 0
                );

                nextButton.classList.toggle(
                    'active',
                    currentPosition < getMaxPosition()
                );
            }

            // =========================
            // СТРЕЛКА НАЗАД
            // =========================

            prevButton.addEventListener('click', function () {

                if (currentPosition > 0) {

                    currentPosition--;

                    updateSlider();

                }

            });

            // =========================
            // СТРЕЛКА ВПЕРЁД
            // =========================

            nextButton.addEventListener('click', function () {

                const maxPosition = getMaxPosition();

                if (currentPosition < maxPosition) {

                    currentPosition++;

                    updateSlider();

                }

            });

            // =========================
            // НАЧАЛО DRAG / SWIPE
            // =========================

            function startDrag(clientX) {

                isDragging = true;

                startX = clientX;
                currentX = clientX;

                startPosition = currentPosition;

                slider.classList.add('is-dragging');

                track.style.transition = 'none';

            }

            // =========================
            // ДВИЖЕНИЕ
            // =========================

            function moveDrag(clientX) {

                if (!isDragging) {
                    return;
                }

                currentX = clientX;

                const diff = currentX - startX;

                const step = getCardWidth() + getGap();

                if (!step) {
                    return;
                }

                let position =
                    startPosition - (diff / step);

                const maxPosition = getMaxPosition();

                // Небольшое сопротивление за краями
                if (position < 0) {
                    position = position * 0.3;
                }

                if (position > maxPosition) {
                    position =
                        maxPosition +
                        (position - maxPosition) * 0.3;
                }

                track.style.transform =
                    `translate3d(-${position * step}px, 0, 0)`;

            }

            // =========================
            // КОНЕЦ DRAG / SWIPE
            // =========================

            function endDrag() {

                if (!isDragging) {
                    return;
                }

                isDragging = false;

                slider.classList.remove('is-dragging');

                const diff = currentX - startX;

                const threshold = 50;

                if (Math.abs(diff) > threshold) {

                    if (diff < 0) {

                        currentPosition++;

                    } else {

                        currentPosition--;

                    }

                } else {

                    currentPosition = startPosition;

                }

                const maxPosition = getMaxPosition();

                currentPosition = Math.max(
                    0,
                    Math.min(currentPosition, maxPosition)
                );

                updateSlider();

            }

            // =========================
            // MOUSE
            // =========================

            slider.addEventListener('mousedown', function (event) {

                startDrag(event.clientX);

            });

            window.addEventListener('mousemove', function (event) {

                moveDrag(event.clientX);

            });

            window.addEventListener('mouseup', function () {

                endDrag();

            });

            // =========================
            // TOUCH
            // =========================

            slider.addEventListener(
                'touchstart',
                function (event) {

                    startDrag(event.touches[0].clientX);

                },
                {passive: true}
            );

            slider.addEventListener(
                'touchmove',
                function (event) {

                    moveDrag(event.touches[0].clientX);

                },
                {passive: true}
            );

            slider.addEventListener(
                'touchend',
                function () {

                    endDrag();

                }
            );

            // =========================
            // RESIZE
            // =========================

            window.addEventListener('resize', function () {

                const maxPosition = getMaxPosition();

                if (currentPosition > maxPosition) {
                    currentPosition = maxPosition;
                }

                updateSlider(false);

            });

            // =========================
            // START
            // =========================

            updateSlider(false);

        });
    </script>




    <!--Теги-->
    <script> document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('tagsContainer');
            const buttonWrapper = document.querySelector('.tags-inner-href');
            const button = document.getElementById('showAllTags');
            if (!container || !button) {
                return;
            }
            if (container.scrollHeight <= container.clientHeight) {
                buttonWrapper.classList.add('hidden');
            }
            button.addEventListener('click', function () {
                container.classList.add('expanded');
                buttonWrapper.classList.add('hidden');
            });
        });

    </script>








@endsection
