@extends('layouts.app')

@section('title', 'Каталог | Dutch Seeds')

@section('seo')

    <meta name="description" content="Каталог Dutch Seeds.">
    <meta name="keywords" content="Каталог, новинки, информация, Dutch Seeds">
    <link rel="canonical" href="{{ url('/news') }}">

@endsection

@push('schema')

    @php
        /*$schema = [
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
        ];*/
    @endphp

    {{--  <script type="application/ld+json">
          {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
      </script>--}}

@endpush

@section('content')

    @include('components.breadcrumbs')

    <main>


        <div class="container">
            <div class="catalog-layout ">


                @include('components.aside')


                <div class="catalog-content">
                    <div class="catalog-products">

                        @if($currentCategory || $brand || $tag)

                            <div class="catalog-welcome">

                                <div class="catalog-welcome-image">

                                    @if($brand)

                                        <img
                                            src="{{ asset('storage/' . $brand->logo) }}"
                                            alt="{{ $brand->name }}"
                                        >

                                    @elseif($currentCategory)

                                        <img
                                            src="{{ asset('storage/' . $currentCategory->image) }}"
                                            alt="{{ $currentCategory->name }}"
                                        >

                                    @elseif($tag)

                                        {{-- Если у тега есть image --}}
                                        @if($tag->image)
                                            <img
                                                src="{{ asset('storage/' . $tag->image) }}"
                                                alt="{{ $tag->name }}"
                                            >
                                        @endif

                                    @endif


                                </div>

                                <div class="catalog-welcome-content">

                                    <div class="catalog-welcome-title">
                                        <span>{{ $catalogInfo->name }}</span>
                                        <span class="catalog-welcome-slash"></span>
                                        <span class="catalog-welcome-country"></span>
                                    </div>

                                    <div class="catalog-welcome-text">
                                        {{ $catalogInfo->description }}
                                    </div>

                                    <a href="#" class="catalog-welcome-more">
                                        Читать далее
                                    </a>

                                </div>

                            </div>

                        @endif
                        <div class="catalog-category-buttons">

                            <a href="{{ route('catalog.index') }}" class="category-button">
                                Все товары
                            </a>

                            @foreach($categories as $category)
                                <a href="{{ route('catalog.category', $category->slug) }}"
                                   class="category-button">
                                    {{ $category->name }}
                                </a>
                            @endforeach


                            <button type="button" class="catalog-category-more">
                                <span>Показать ещё</span>

                                <svg width="12" height="7" viewBox="0 0 12 7" fill="none">
                                    <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                            </button>

                        </div>


                        <div class="catalog-controls">

                            <div class="catalog-controls-left">

                                <div class="catalog-sort">
                                    <span class="catalog-controls-title">Сортировка</span>

                                    <button type="button" class="catalog-select">
                                        <span>Сначала дорогие</span>

                                        <svg width="12" height="7" viewBox="0 0 12 7" fill="none">
                                            <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="catalog-grouping">
                                    <span>Группировка:</span>

                                    <button type="button" class="catalog-select">
                                        <span>Отсутствует</span>

                                        <svg width="12" height="7" viewBox="0 0 12 7" fill="none">
                                            <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </button>
                                </div>

                            </div>

                            <div class="catalog-view-buttons">

                                <a href="#" class="catalog-category-button mb">
                                    Фильтр
                                </a>

                                <div class="catalog-view-buttons-2">
                                    <button type="button" class="catalog-view-button active">
                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M15 3C15.8284 3 16.5 3.67157 16.5 4.5V15C16.5 15.8284 15.8284 16.5 15 16.5H4.5C3.67157 16.5 3 15.8284 3 15V4.5C3 3.67157 3.67157 3 4.5 3H15ZM6 13.5H13.5V6H6V13.5Z"
                                                fill="#277423"/>
                                            <path
                                                d="M31.5 3C32.3284 3 33 3.67157 33 4.5V15C33 15.8284 32.3284 16.5 31.5 16.5H21C20.1716 16.5 19.5 15.8284 19.5 15V4.5C19.5 3.67157 20.1716 3 21 3H31.5ZM22.5 13.5H30V6H22.5V13.5Z"
                                                fill="#277423"/>
                                            <path
                                                d="M31.5 19.5C32.3284 19.5 33 20.1716 33 21V31.5C33 32.3284 32.3284 33 31.5 33H21C20.1716 33 19.5 32.3284 19.5 31.5V21C19.5 20.1716 20.1716 19.5 21 19.5H31.5ZM22.5 30H30V22.5H22.5V30Z"
                                                fill="#277423"/>
                                            <path
                                                d="M15 19.5C15.8284 19.5 16.5 20.1716 16.5 21V31.5C16.5 32.3284 15.8284 33 15 33H4.5C3.67157 33 3 32.3284 3 31.5V21C3 20.1716 3.67157 19.5 4.5 19.5H15ZM6 30H13.5V22.5H6V30Z"
                                                fill="#277423"/>
                                        </svg>
                                    </button>

                                    <button type="button" class="catalog-view-button">

                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M31.5 6C31.5 5.17157 30.8284 4.5 30 4.5H6C5.17157 4.5 4.5 5.17157 4.5 6V12C4.5 12.8284 5.17157 13.5 6 13.5H30C30.8284 13.5 31.5 12.8284 31.5 12V6ZM34.5 12C34.5 14.4853 32.4853 16.5 30 16.5H6C3.51472 16.5 1.5 14.4853 1.5 12V6C1.5 3.51472 3.51472 1.5 6 1.5H30C32.4853 1.5 34.5 3.51472 34.5 6V12Z"
                                                fill="#585858"/>
                                            <path
                                                d="M31.5 24C31.5 23.1716 30.8284 22.5 30 22.5H6C5.17157 22.5 4.5 23.1716 4.5 24V30C4.5 30.8284 5.17157 31.5 6 31.5H30C30.8284 31.5 31.5 30.8284 31.5 30V24ZM34.5 30C34.5 32.4853 32.4853 34.5 30 34.5H6C3.51472 34.5 1.5 32.4853 1.5 30V24C1.5 21.5147 3.51472 19.5 6 19.5H30C32.4853 19.5 34.5 21.5147 34.5 24V30Z"
                                                fill="#585858"/>
                                        </svg>
                                    </button>
                                </div>


                            </div>

                        </div>

                        <div class="catalog-products-list">
                            <!-- товары -->
                        </div>


                        <div class="products-grid-catalog">



                                @foreach($products as $product)
                                    @include('components.product-card', ['product' => $product])
                                @endforeach

                        </div>


                    </div>

                </div>

            </div>
        </div>


    </main>

    <script>
        const settingsButton = document.querySelector('.settings');
        const filter = document.querySelector('.catalog-filter');
        const filterClose = document.querySelector('.catalog-filter-close');

        settingsButton?.addEventListener('click', () => {
            filter?.classList.toggle('active');
        });

        filterClose?.addEventListener('click', () => {
            filter?.classList.remove('active');
        });
    </script>

    {{--показать еще --}}

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const container = document.querySelector('.catalog-category-buttons');
            const moreButton = container?.querySelector('.catalog-category-more');
            const buttons = container
                ? Array.from(container.querySelectorAll('.category-button'))
                : [];

            if (!container || !moreButton || !buttons.length) {
                return;
            }

            let expanded = false;

            function updateCategories() {

                if (expanded) {
                    buttons.forEach(button => {
                        button.style.display = '';
                    });

                    moreButton.style.display = 'none';

                    return;
                }

                /*
                 * Сначала показываем всё.
                 */
                buttons.forEach(button => {
                    button.style.display = '';
                });

                moreButton.style.display = 'flex';

                requestAnimationFrame(() => {

                    const containerWidth = container.clientWidth;

                    const styles = getComputedStyle(container);

                    const gap = parseFloat(styles.columnGap) || 0;

                    const moreWidth = moreButton.offsetWidth;

                    let usedWidth = 0;
                    let visibleCount = 0;

                    buttons.forEach((button, index) => {

                        const buttonWidth = button.offsetWidth;

                        const nextWidth =
                            visibleCount === 0
                                ? buttonWidth
                                : usedWidth + gap + buttonWidth;

                        /*
                         * Сколько места потребуется,
                         * если добавить кнопку.
                         */
                        const requiredWidth =
                            nextWidth +
                            gap +
                            moreWidth;

                        if (requiredWidth <= containerWidth) {

                            usedWidth = nextWidth;
                            visibleCount++;

                        }

                    });

                    /*
                     * Если все кнопки помещаются даже вместе
                     * с "Показать ещё", значит кнопка не нужна.
                     */
                    if (visibleCount === buttons.length) {

                        buttons.forEach(button => {
                            button.style.display = '';
                        });

                        moreButton.style.display = 'none';

                        return;
                    }

                    /*
                     * Оставляем только те кнопки,
                     * которые помещаются вместе с "Показать ещё".
                     */
                    buttons.forEach((button, index) => {

                        button.style.display =
                            index < visibleCount
                                ? ''
                                : 'none';

                    });

                    moreButton.style.display = 'flex';

                });
            }

            moreButton.addEventListener('click', () => {

                expanded = true;

                buttons.forEach(button => {
                    button.style.display = '';
                });

                moreButton.style.display = 'none';

            });

            updateCategories();

            let resizeTimer;

            window.addEventListener('resize', () => {

                clearTimeout(resizeTimer);

                resizeTimer = setTimeout(() => {

                    expanded = false;

                    updateCategories();

                }, 150);

            });

        });
    </script>


    {{-- вариант фасовки --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.product-card').forEach(card => {

                const variants = card.querySelectorAll('.pagination-button');
                const outOfStock = card.querySelector('.variant-out-of-stock');

                const price = card.querySelector('.product-current-price');
                const oldPrice = card.querySelector('.product-old-price');

                const quantityValue = card.querySelector('.quantity-value');
                const minusButton = card.querySelector('.quantity-button:first-child');
                const plusButton = card.querySelector('.quantity-button:last-child');

                let selectedVariant = null;
                let quantity = 1;

                variants.forEach(variant => {

                    variant.addEventListener('click', function (e) {

                        e.preventDefault();

                        if (this.classList.contains('disabled')) {
                            return;
                        }

                        selectedVariant = this;

                        const variantPrice = parseFloat(this.dataset.price);
                        const variantOldPrice = parseFloat(this.dataset.oldPrice);
                        const stock = parseInt(this.dataset.stock);

                        // Цена
                        if (price) {
                            price.textContent = variantPrice + ' р';
                        }

                        // Старая цена
                        if (oldPrice) {

                            if (variantOldPrice && variantOldPrice > variantPrice) {
                                oldPrice.textContent = '$ ' + variantOldPrice;
                                oldPrice.style.display = '';
                            } else {
                                oldPrice.style.display = 'none';
                            }

                        }

                        // Активная фасовка
                        variants.forEach(item => {
                            item.classList.remove('active');
                        });

                        this.classList.add('active');

                        // Если товара нет
                        if (stock <= 0) {

                            outOfStock.style.display = 'block';

                            quantity = 0;

                            quantityValue.textContent = '0';

                            minusButton.disabled = true;
                            plusButton.disabled = true;

                            return;
                        }

                        // Товар есть
                        outOfStock.style.display = 'none';

                        quantity = 1;

                        quantityValue.textContent = quantity;

                        minusButton.disabled = false;

                        // + нельзя нажать больше остатка
                        plusButton.disabled = quantity >= stock;

                    });
                });


                // МИНУС
                minusButton.addEventListener('click', function () {

                    if (!selectedVariant) {
                        return;
                    }

                    const stock = parseInt(selectedVariant.dataset.stock);

                    if (quantity > 1) {
                        quantity--;
                    }

                    quantityValue.textContent = quantity;

                    plusButton.disabled = quantity >= stock;
                });


                // ПЛЮС
                plusButton.addEventListener('click', function () {

                    if (!selectedVariant) {
                        return;
                    }

                    const stock = parseInt(selectedVariant.dataset.stock);

                    if (quantity < stock) {
                        quantity++;
                    }

                    quantityValue.textContent = quantity;

                    plusButton.disabled = quantity >= stock;
                });

            });

        });
    </script>

@endsection
