@extends('layouts.app')

@section('title', 'Сидбанки | Dutch Seeds')

@section('seo')

    <meta name="description" content="Сидбанки">
    <meta name="keywords" content="Сидбанки, новинки, информация, Dutch Seeds">
    {{--<link rel="canonical" href="{{ url('/news') }}">
--}}
@endsection

{{--@push('schema')

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

@endpush--}}

@section('content')

    <section class="catalog-categories">

        @include('components.breadcrumbs')

        <div class="container">

            <h1 class="catalog-categories-title">
                Сидбанки
            </h1>

            <div class="catalog-categories-grid">

                @foreach($seedbanks as $seedbank)
                    <a href="#" class="catalog-category">

                        <img src="{{  asset('storage/' . $seedbank->logo)  }}" alt="">

                    </a>
                @endforeach



            </div>

        </div>

    </section>

@endsection
