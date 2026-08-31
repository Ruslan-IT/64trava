@extends('layouts.app')

@section('title', 'Новости | Dutch Seeds')

@section('seo')

    <meta name="description" content="Новости, новинки и полезная информация от Dutch Seeds.">
    <meta name="keywords" content="новости, новинки, информация, Dutch Seeds">
    <link rel="canonical" href="{{ url('/news') }}">

@endsection

@push('schema')

    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Новости | Dutch Seeds',
            'description' => 'Новости, новинки и полезная информация от Dutch Seeds.',
            'url' => url('/news'),
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Dutch Seeds',
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $news->values()->map(function ($new, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('news.show', $new),
                        'name' => $new->title,
                    ];
                })->values()->toArray(),
            ],
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

@endpush

@section('content')

    <main class="news-page">
        @include('components.breadcrumbs')

        <div class="news-container">



            <!-- ЗАГОЛОВОК -->

            <h1 class="news-title">Новости</h1>


            <!-- ФИЛЬТРЫ -->

            <div class="news-filters">

                <button type="button" class="news-filter active">Все новости</button>

                <button type="button" class="news-filter">Новинки</button>

                <button type="button" class="news-filter">Информация</button>

                <button type="button" class="news-filter">Факты</button>

            </div>


            <!-- НОВОСТИ -->

            <div class="news-grid">

                @foreach($news as $new)

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

                        $category = $categories[$new->category] ?? $categories['info'];
                    @endphp

                    <article class="news-card">

                        <!-- КАРТИНКА -->

                        <a href="{{ route('news.show', $new) }}" class="news-card-image">
                            <img src="{{ asset('storage/' . $new->image) }}" alt="{{ $new->title }}">
                        </a>


                        <!-- META -->

                        <div class="news-card-meta">

                            <span class="news-card-category {{ $category['class'] }}">{{ $category['name'] }}</span>

                            <time class="news-card-date" datetime="{{ $new->published_at?->format('Y-m-d') }}">{{ $new->formatted_date }}</time>

                        </div>

                        <h2 class="news-card-title">{{ $new->title }}</h2>

                        <p class="news-card-text">{{ $new->excerpt }}</p>

                        <a href="{{ route('news.show', $new) }}" class="news-card-link">Читать далее</a>

                    </article>

                @endforeach

            </div>

        </div>

    </main>

@endsection
