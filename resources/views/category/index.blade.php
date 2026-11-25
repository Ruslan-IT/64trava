@extends('layouts.app')

@section('title', $seoTitle ?? 'Все семена | Dutch Seeds')

@section('seo')

    @if(filled($seoDescription ?? null))
        <meta name="description" content="{{ $seoDescription }}">
    @endif
    @if(filled($seoKeywords ?? null))
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl ?? url('/news') }}">
    @if(! empty($brand) || ! empty($brandSubcategory))
        <meta name="robots" content="index, follow">
    @endif

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



            @livewire('catalog-filter', [
                'currentCategory' => $currentCategory,
                'brand' => $brand,
                'tag' => $tag,
                'catalogInfo' => $catalogInfo,
                'brandSubcategory' => $brandSubcategory ?? null,
            ])





        </div>


    </main>

    <script>
        document.addEventListener('click', (event) => {
            const currentFilter = document.querySelector('.catalog-filter');

            if (!currentFilter) {
                return;
            }

            if (event.target.closest('.settings')) {
                currentFilter.classList.toggle('active');
            }

            if (event.target.closest('.catalog-filter-open')) {
                event.preventDefault();
                currentFilter.classList.add('active');
            }

            if (event.target.closest('.catalog-filter-close')) {
                currentFilter.classList.remove('active');
            }
        });
    </script>

    {{-- вариант фасовки --}}
    <script>
        function initCatalogProductCards() {
            document.querySelectorAll('.product-card').forEach(card => {
                if (card.dataset.variantsBound === '1') {
                    return;
                }

                card.dataset.variantsBound = '1';

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

                        if (price) {
                            price.textContent = variantPrice + ' р';
                        }

                        if (oldPrice) {
                            if (variantOldPrice && variantOldPrice > variantPrice) {
                                oldPrice.textContent = '$ ' + variantOldPrice;
                                oldPrice.style.display = '';
                            } else {
                                oldPrice.style.display = 'none';
                            }
                        }

                        variants.forEach(item => {
                            item.classList.remove('active');
                        });

                        this.classList.add('active');

                        if (outOfStock) {
                            outOfStock.style.display = 'none';
                        }
                        quantity = 1;
                        if (quantityValue) {
                            quantityValue.textContent = quantity;
                        }
                        if (minusButton) {
                            minusButton.disabled = false;
                        }
                        if (plusButton) {
                            plusButton.disabled = false;
                        }
                    });
                });

                minusButton?.addEventListener('click', function () {
                    if (!selectedVariant) {
                        return;
                    }

                    if (quantity > 1) {
                        quantity--;
                    }

                    quantityValue.textContent = quantity;
                    plusButton.disabled = false;
                });

                plusButton?.addEventListener('click', function () {
                    if (!selectedVariant) {
                        return;
                    }

                    quantity++;

                    quantityValue.textContent = quantity;
                    plusButton.disabled = false;
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initCatalogProductCards);
        document.addEventListener('livewire:navigated', initCatalogProductCards);
    </script>


    <script>
        window.catalogWelcomeText = function catalogWelcomeText() {
            return {
                expanded: false,
                showMore: false,
                init() {
                    this.expanded = false;
                    this.$nextTick(() => this.measure());
                    requestAnimationFrame(() => this.measure());
                },
                measure() {
                    const el = this.$refs.text;

                    if (!el || el.dataset.empty === '1') {
                        this.showMore = false;
                        this.expanded = false;
                        return;
                    }

                    this.expanded = false;

                    this.$nextTick(() => {
                        this.showMore = el.scrollHeight > el.clientHeight + 1;
                        this.expanded = false;
                    });
                },
                toggle() {
                    this.expanded = !this.expanded;
                },
            };
        };
    </script>

@endsection
