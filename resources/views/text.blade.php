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

    <main class="article-page">

        <div class="article-container">

            <!-- ВЕРХНИЙ БЛОК -->

            <div class="article-top">

                <!-- ЛЕВАЯ ЧАСТЬ -->

                <div class="article-sidebar">

                    <h1 class="article-title">
                        Как проращить семена конопли
                    </h1>


                    <!-- META -->

                    <div class="article-meta">

                        <div class="article-meta-item">

                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M5 2V4M13 2V4M3 7H15M4 3H14C14.5523 3 15 3.44772 15 4V15C15 15.5523 14.5523 16 14 16H4C3.44772 16 3 15.5523 3 15V4C3 3.44772 3.44772 3 4 3Z"
                                      stroke="currentColor"
                                      stroke-width="1.5"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <span>10 июля 2025 года</span>

                        </div>

                        <span class="article-meta-dot">•</span>

                        <div class="article-meta-item">

                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M2 9C2 9 4.5 4 9 4C13.5 4 16 9 16 9C16 9 13.5 14 9 14C4.5 14 2 9 2 9Z"
                                      stroke="currentColor"
                                      stroke-width="1.5"/>
                                <circle cx="9" cy="9" r="2"
                                        stroke="currentColor"
                                        stroke-width="1.5"/>
                            </svg>

                            <span>24K просмотра</span>

                        </div>

                        <span class="article-meta-dot">•</span>

                        <div class="article-meta-item">

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


                    <!-- ОГЛАВЛЕНИЕ -->

                    <nav class="article-navigation">

                        <a href="#method">
                            1. Способы проращивания семян
                        </a>

                        <a href="#germination">
                            1.1 Проращивание семян
                        </a>

                        <a href="#cotton">
                            1.2 Проращивание семян в ватных дисках
                        </a>

                        <a href="#after-germination">
                            2. Что делать после прорастания
                        </a>

                        <a href="#soil">
                            3. Как подготовить грунт
                        </a>

                        <a href="#planting">
                            4. Как посадить проросшие семена
                        </a>

                        <a href="#sprout">
                            5. Что делать после появления ростка
                        </a>

                        <a href="#helmet">
                            6. Что делать, если росток сбрасывает каску
                        </a>

                    </nav>

                </div>


                <!-- ПРАВАЯ ФОТОГРАФИЯ -->

                <div class="article-main-image">

                    <img src="images/hemp-seeds.jpg"
                         alt="Как проращить семена">

                </div>

            </div>


            <!-- ОСНОВНОЙ КОНТЕНТ -->

            <article class="article-content">


                <section class="article-section" id="method">

                    <h2>
                        Способы проращивания семени
                    </h2>

                    <p>
                        Здесь располагается основной текст статьи. В этом разделе
                        можно подробно описать общую информацию, особенности процесса
                        и важные моменты, которые необходимо учитывать.
                    </p>

                </section>


                <section class="article-section" id="germination">

                    <h2>
                        Проращивание семян в стакане
                    </h2>

                    <p>
                        Здесь располагается текстовый блок с описанием соответствующего
                        раздела статьи. Текст может занимать несколько строк.
                    </p>

                </section>


                <!-- ФОТОГАЛЕРЕЯ -->

                <div class="article-gallery">

                    <div class="article-gallery-item">

                        <img src="images/article-1.jpg" alt="">



                    </div>

                    <div class="article-gallery-item">

                        <img src="images/article-2.jpg" alt="">



                    </div>

                    <div class="article-gallery-item">

                        <img src="images/article-3.jpg" alt="">



                    </div>

                    <div class="article-gallery-item">

                        <img src="images/article-4.jpg" alt="">



                    </div>

                </div>


                <section class="article-section">

                    <p>
                        Дополнительный текст статьи. Здесь можно разместить несколько
                        предложений с описанием процесса, особенностей и рекомендаций.
                        Следующая строка продолжает содержание материала.
                    </p>

                    <p>
                        Ещё один абзац с дополнительной информацией. Он отделяется
                        от предыдущего абзаца стандартным вертикальным отступом.
                    </p>

                </section>


                <section class="article-section" id="cotton">

                    <h2>
                        Проращивание семян в ватных дисках
                    </h2>

                    <p>
                        Текст данного раздела располагается под заголовком.
                        Он может занимать три или больше строк в зависимости
                        от ширины экрана.
                    </p>

                </section>


                <section class="article-section" id="after-germination">

                    <h2>
                        Что делать после прорастания
                    </h2>

                    <p>
                        Здесь располагается следующий информационный блок статьи.
                        Текст адаптируется под ширину контейнера.
                    </p>

                </section>


                <section class="article-section" id="soil">

                    <h2>
                        Как подготовить грунт
                    </h2>

                    <p>
                        Дополнительная информация по теме статьи располагается
                        здесь отдельным параграфом.
                    </p>

                </section>


                <section class="article-section" id="planting">

                    <h2>
                        Как посадить проросшие семена
                    </h2>

                    <p>
                        Текстовый материал раздела. Здесь можно разместить
                        подробное описание соответствующего этапа.
                    </p>

                </section>


                <section class="article-section" id="sprout">

                    <h2>
                        Что делать после появления ростка
                    </h2>

                    <p>
                        Ещё один текстовый блок статьи с необходимой информацией.
                    </p>

                </section>


                <section class="article-section" id="helmet">

                    <h2>
                        Что делать, если росток сбрасывает каску
                    </h2>

                    <p>
                        Заключительный информационный блок статьи.
                    </p>

                </section>

            </article>

        </div>

    </main>
@endsection
