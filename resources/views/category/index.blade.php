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



                @livewire('catalog-filter')





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
