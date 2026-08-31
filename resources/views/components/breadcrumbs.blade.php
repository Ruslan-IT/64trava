<div class="container">
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <a href="{{ route('home') }}">Главная</a>

        @if(request()->routeIs('news.index'))
            <span>-</span>
            <span class="breadcrumbs-current">Новости</span>

        @elseif(request()->routeIs('news.show'))
            <span>-</span>
            <a href="{{ route('news.index') }}">Новости</a>
            <span>-</span>
            <span class="breadcrumbs-current">{{ $news->title }}</span>

        @elseif(request()->routeIs('catalog.index'))
            <span>-</span>
            <span class="breadcrumbs-current">Каталог</span>

        @elseif(request()->routeIs('seedbanks.index'))
            <span>-</span>
            <span class="breadcrumbs-current">Сидбанки</span>

        @elseif(request()->routeIs('catalog.category'))
            <span>-</span>
            <a href="{{ route('catalog.index') }}">Каталог</a>
            <span>-</span>

            @if($currentCategory)
                <span class="breadcrumbs-current">{{ $currentCategory->name }}</span>
            @elseif(request()->filter === 'new')
                <span class="breadcrumbs-current">Новинки</span>
            @elseif(request()->filter === 'popular')
                <span class="breadcrumbs-current">Популярные</span>
            @else
                <span class="breadcrumbs-current">Каталог</span>
            @endif
        @endif
    </nav>
</div>
