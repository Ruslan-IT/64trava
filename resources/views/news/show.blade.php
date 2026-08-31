@extends('layouts.app')

@section('title', $news->seo_title ?: $news->title)
@section('seo')

    <meta name="description" content="{{ $news->seo_description }}">
    <meta name="keywords" content="{{ $news->seo_keywords }}">
    <link rel="canonical" href="{{ url()->current() }}">

@endsection
@push('schema')

    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $news->title,
            'description' => $news->excerpt,
            'image' => [
                asset('storage/' . $news->image),
            ],
            'datePublished' => $news->published_at?->toIso8601String(),
            'dateModified' => $news->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'Dutch Seeds',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Dutch Seeds',
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

@endpush

@section('content')


    @include('components.breadcrumbs')


    <main class="news-detail-page">

        <div class="news-detail-container">

            <div class="news-detail-layout">

                <!-- ЛЕВАЯ ЧАСТЬ -->

                <article class="news-detail-content">

                    <h1 class="news-detail-title">
                        {{ $news->title }}
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

                            <span>{{ $news->published_at->format('d.m.Y') }}</span>
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

                            <span>{{ number_format($news->views, 0, '.', ' ') }} K просмотров</span>

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

                            <span>{{ $news->reading_time }} минуты чтения</span>

                        </div>

                    </div>


                    <!-- ТЕКСТ НОВОСТИ -->

                    <div class="news-detail-text">

                        {!! $news->content !!}

                    </div>


                    <!-- НАВИГАЦИЯ -->

                    <div class="news-detail-navigation">

                        @if($previousNews)
                            <a
                                href="{{ route('news.show', $previousNews) }}"
                                class="news-detail-navigation-button"
                            >
                                Предыдущая новость
                            </a>
                        @endif

                        @if($nextNews)
                            <a
                                href="{{ route('news.show', $nextNews) }}"
                                class="news-detail-navigation-button"
                            >
                                Следующая новость
                            </a>
                        @endif

                    </div>

                </article>


                <!-- ПРАВАЯ ЧАСТЬ -->

                <div class="news-detail-image">

                    <img src="{{ asset('storage/' . $news->image) }}"
                         alt="Новая коллекция">

                </div>

            </div>

        </div>

        @include('components.newsSection')

    </main>

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
                { passive: true }
            );

            slider.addEventListener(
                'touchmove',
                function (event) {

                    moveDrag(event.touches[0].clientX);

                },
                { passive: true }
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

@endsection

