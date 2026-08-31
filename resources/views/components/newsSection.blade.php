<section class="news">

    <div class="container">
        <div class="news-header">
            <h2>Новости</h2>

            <div class="news-header-actions">
                <a href="{{ route('news.index') }}">Все новости</a>


                <button
                    type="button"
                    class="slider-arrow"
                    aria-label="Назад"
                >

                    <svg width="8" height="15" viewBox="0 0 8 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.66458 13.95L1.23125 8.51667C0.589583 7.875 0.589583 6.825 1.23125 6.18333L6.66458 0.75" stroke="#585858" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button
                    type="button"
                    class="slider-arrow active"
                    aria-label="Вперёд"
                >

                    <svg width="8" height="15" viewBox="0 0 8 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.750456 0.750194L6.18379 6.18353C6.82546 6.8252 6.82546 7.87519 6.18379 8.51686L0.750455 13.9502" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="news-slider">
            <div class="news-list">


                @php
                    $categories = [
                        'new' => [
                            'name' => 'Новинки',
                            'class' => 'category-new',
                        ],
                        'info' => [
                            'name' => 'Информация',
                            'class' => 'category-info',
                        ],
                        'facts' => [
                            'name' => 'Факты',
                            'class' => 'category-facts',
                        ],
                    ];


                @endphp

                @foreach($newsBlock as $new)


                    @php
                        $category = $categories[$new->category] ?? $categories['info'];
                    @endphp


                    <article class="news-card">
                        <img src="{{ asset('storage/' . $new->image) }}" alt="">

                        <div class="news-card-meta">
                                    <span class="news-card-category category- {{ $category['class'] }}">
                                        {{ $category['name'] }}
                                    </span>

                            <time datetime="{{ $new->published_at?->format('Y-m-d') }}">
                                {{ $new->formatted_date }}
                            </time>
                        </div>

                        <h3>{{ $new->title }}</h3>

                        <p>
                            {{ $new->excerpt }}
                        </p>

                        <a href="{{ route('news.show', $new) }}" class="news-card-link">
                            Читать далее
                        </a>
                    </article>
                @endforeach
            </div>
        </div>


    </div>

</section>
