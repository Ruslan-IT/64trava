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

                                <article class="product-card">

                                    <div class="product-image-wrapper">

                                        <img
                                            src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/product/product-1.jpg') }}"
                                            alt="{{ $product->title }}"
                                            class="product-image"
                                        >

                                        <div class="product-labels">

                                            <div>

                                               {{-- @if($product->is_recommended)--}}
                                                    <span class="product-label recommend">
                                                    Рекомендуем
                                                </span>
                                               {{-- @endif--}}

                                                {{--@if($product->is_popular)--}}
                                                    <span class="product-label top">
                                                        TOP
                                                    </span>
                                               {{-- @endif--}}

                                            </div>


                                            <div class="product-rating">

                                                <span class="product-star">
                                                    <svg width="23" height="22" viewBox="0 0 23 22" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M22.2103 7.74683L15.3297 6.74311L12.2539 0.484186C12.1699 0.31282 12.0317 0.174096 11.861 0.089773C11.4328 -0.122394 10.9125 0.0544119 10.6984 0.484186L7.62257 6.74311L0.741948 7.74683C0.55225 7.77403 0.378812 7.86379 0.246023 7.99979C0.085489 8.16541 -0.00297259 8.38822 7.62746e-05 8.61927C0.00312514 8.85032 0.097435 9.0707 0.262283 9.232L5.2405 14.1037L4.06437 20.9828C4.03679 21.1428 4.05443 21.3074 4.1153 21.4579C4.17616 21.6083 4.27781 21.7387 4.40873 21.8341C4.53964 21.9295 4.69457 21.9862 4.85596 21.9978C5.01734 22.0093 5.17872 21.9753 5.3218 21.8995L11.4761 18.6517L17.6305 21.8995C17.7985 21.9892 17.9936 22.0191 18.1806 21.9865C18.6522 21.9049 18.9692 21.4561 18.8879 20.9828L17.7118 14.1037L22.69 9.232C22.8255 9.09871 22.9149 9.07755 22.942 8.73422C23.0152 8.2582 22.6846 7.81755 22.2103 7.74683Z"
                                                            fill="#F9C50F"></path>
                                                    </svg>
                                                </span>

                                                <span class="product-rating-value">
                                                    {{ $product->rating ?? '4.8' }} / 5
                                                </span>

                                            </div>

                                        </div>

                                    </div>


                                    <h3 class="product-title">
                                        {{ $product->name }}
                                    </h3>

                                    <div class="product-subtitle">

                                        {{ $product->brand?->name ?? 'NULL' }}

                                    </div>




                                    <div class="product-info">

                                        <div class="product-info-row">


                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_16708_8211)">
                                                    <path d="M10.2716 2.14064L13.9786 4.00207L17.6855 5.8635L17.9619 6.00414V6.28542V7.18718C17.7808 7.07963 17.5807 6.98862 17.3615 6.91417C17.2376 6.86453 17.1042 6.83144 16.9708 6.79007C16.9327 6.7818 16.8851 6.76525 16.8469 6.75698V6.5667L13.4163 4.84592L9.98571 3.12513L6.55509 4.84592L3.12448 6.5667V6.69907H2.14294H2.05718H2V6.28542V6.00414L2.27635 5.8635L5.98332 4.00207L9.69029 2.14064L9.96665 2L10.243 2.14064H10.2716ZM17.9714 12.8128V13.7146V13.9959L17.6951 14.1365L13.9881 15.9979L10.2811 17.8594L10.0048 18L9.72841 17.8594L6.02144 15.9979L2.31447 14.1365L2.03812 13.9959V13.7146V13.3009H2.69565H3.15307V13.4333L6.58368 15.1541L10.0143 16.8749L13.4449 15.1541L16.8755 13.4333V13.243C16.9136 13.2347 16.9613 13.2265 16.9994 13.2099C17.0566 13.1934 17.1042 13.1768 17.1614 13.1603H17.19L17.3996 13.0858C17.514 13.0445 17.6379 12.9948 17.7713 12.9286C17.8475 12.8873 17.9238 12.8459 18 12.8046L17.9714 12.8128Z" fill="#585858"/>
                                                    <path d="M3.79124 8.51935H2.15217H2.06641V8.44489V7.73341V7.65896H2.15217H6.56432H6.65009V7.73341V8.44489V8.51935H6.56432H4.92525V12.2505V12.3249H4.83949H3.87701H3.79124V12.2505V8.51935ZM12.0057 7.73341V12.2505V12.3249H11.9199H10.9574H10.8716V12.2505V10.3891H8.34634V12.2505V12.3249H8.26057H7.29809H7.21233V12.2505V7.73341V7.65896H7.29809H8.26057H8.34634V7.73341V9.52866H10.8716V7.73341V7.65896H10.9574H11.9199H12.0057V7.73341ZM15.7317 12.3911C15.4649 12.3911 15.2076 12.3663 14.9598 12.3167C14.712 12.267 14.4833 12.1843 14.2641 12.085C14.045 11.9857 13.8544 11.8616 13.6828 11.7127C13.5113 11.5721 13.3588 11.4066 13.235 11.2246C13.1111 11.0426 13.0158 10.8441 12.9586 10.6455C12.9014 10.4387 12.8633 10.2236 12.8633 9.99195C12.8633 9.7603 12.8919 9.5452 12.9586 9.33838C13.0253 9.13155 13.1111 8.94127 13.2445 8.75927C13.3684 8.57726 13.5208 8.4118 13.6924 8.27116C13.8639 8.13052 14.064 8.00642 14.2737 7.89887C14.4929 7.7996 14.7216 7.71687 14.9693 7.66723C15.2171 7.61759 15.4744 7.59277 15.7412 7.59277C15.9604 7.59277 16.17 7.60932 16.3702 7.64241C16.5703 7.6755 16.7609 7.72514 16.9419 7.79133C17.304 7.92369 17.6185 8.12225 17.8663 8.37044L17.9235 8.42007L17.8663 8.46971L17.2373 8.98264L17.1706 9.03228L17.1135 8.97437C16.9324 8.80891 16.7323 8.67654 16.5131 8.59381C16.2939 8.51108 16.0462 8.46971 15.7889 8.46971C15.4458 8.46971 15.1409 8.5359 14.874 8.65999C14.7406 8.72618 14.6167 8.80063 14.5119 8.89164C14.4071 8.98264 14.3213 9.08192 14.2451 9.19774C14.1688 9.31356 14.1117 9.43765 14.0736 9.56175C14.0354 9.69412 14.0164 9.83476 14.0164 9.9754C14.0164 10.1243 14.0354 10.265 14.0736 10.3891C14.1117 10.5214 14.1688 10.6372 14.2451 10.7531C14.3213 10.8689 14.4071 10.9682 14.5119 11.0592C14.6167 11.1502 14.7406 11.2246 14.874 11.2908C15.0074 11.357 15.1504 11.4066 15.3029 11.4397C15.4553 11.4728 15.6173 11.4894 15.7889 11.4894C16.313 11.4894 16.7609 11.3156 17.1135 10.9764L17.1706 10.9185L17.2373 10.9682L17.8663 11.4811L17.9235 11.5307L17.8663 11.5804C17.7424 11.7045 17.5995 11.8203 17.447 11.9196C17.2945 12.0188 17.123 12.1016 16.9419 12.1677C16.7609 12.2339 16.5703 12.2836 16.3702 12.3167C16.17 12.3498 15.9604 12.3663 15.7412 12.3663L15.7317 12.3911Z" fill="#585858"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_16708_8211">
                                                        <rect width="16" height="16" fill="white" transform="translate(2 2)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>


                                            <span>{{ $product->thc ?? '30-33%' }}</span>

                                        </div>


                                        <div class="product-info-row tip">

                                            <div>
                                                Тип семян:
                                                <span class="seed-type-active">
                            {{ $product->seed_type ?? 'A' }}
                        </span>/F/R
                                            </div>

                                            <div class="product-info-row-el">

                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_16708_901)">
                                                        <path d="M15.521 1.44336V18.9577H14.1006V1.44336H15.521Z" fill="#585858"/>
                                                        <path d="M10.8706 3.95189L14.3185 0.504009L14.811 0L15.315 0.504009L18.7629 3.95189L17.7549 4.94845L14.811 2.00458L11.8672 4.94845L10.8706 3.95189Z" fill="#585858"/>
                                                        <path d="M9.9541 8.8087L14.3184 4.44444L14.8109 3.94043L15.3149 4.44444L19.6792 8.8087L18.6712 9.80526L14.8109 5.94501L10.9507 9.80526L9.9541 8.8087Z" fill="#585858"/>
                                                        <path d="M9.3584 13.3446L14.3183 8.3961L14.8109 7.89209L15.3149 8.3961L20.2748 13.3446L19.2668 14.3526L14.8109 9.89667L10.355 14.3526L9.3584 13.3446Z" fill="#585858"/>
                                                        <path d="M19.6905 19.9999H14.8108H14.1006V19.3011V14.4214H15.521V18.5909H19.6905V19.9999Z" fill="#585858"/>
                                                        <path d="M-0.274902 0.297852H6.90723V1.70679H-0.274902V0.297852Z" fill="#585858"/>
                                                        <path d="M-0.274902 9.43848H6.90723V10.8589H-0.274902V9.43848Z" fill="#585858"/>
                                                        <path d="M-0.274902 18.5908H6.90723V19.9998H-0.274902V18.5908Z" fill="#585858"/>
                                                        <path d="M-0.274902 15.5439H3.3448V16.9529H-0.274902V15.5439Z" fill="#585858"/>
                                                        <path d="M-0.274902 12.4858H3.3448V13.9062H-0.274902V12.4858Z" fill="#585858"/>
                                                        <path d="M-0.274902 6.3916H3.3448V7.81199H-0.274902V6.3916Z" fill="#585858"/>
                                                        <path d="M-0.274902 3.34473H3.3448V4.75366H-0.274902V3.34473Z" fill="#585858"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_16708_901">
                                                            <rect width="20" height="20" fill="white"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>

                                                <span>{{ $product->height ?? '70cm' }}</span>

                                            </div>

                                        </div>


                                        <div class="product-info-row">

                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_16708_918)">
                                                    <path d="M1.98373 0H3.24852H3.39497V0.14645V6.93639V7.08284H3.24852H1.98373H1.83728V6.93639V0.14645V0H1.98373ZM15.6834 16.2293H12.4749L11.8491 17.7071L11.8092 17.8003H11.716H10.4112H10.1849L10.2781 17.5873L13.3402 10.7973L13.3802 10.7041H13.4734H14.7115H14.8047L14.8447 10.7973L17.9068 17.5873L18 17.8003H17.7737H16.4556H16.3624L16.3225 17.7071L15.6834 16.2293ZM15.1376 14.9379L14.0858 12.4882L13.034 14.9379H15.1376ZM2.86243 17.8935C2.59615 17.8935 2.32988 17.8802 2.06361 17.8402C1.79734 17.8003 1.54438 17.747 1.29142 17.6672C1.03846 17.5873 0.81213 17.4941 0.612426 17.4009C0.412722 17.2944 0.226331 17.1746 0.0798817 17.0547L0 16.9882L0.0399408 16.8817L0.47929 15.8964L0.559172 15.7234L0.705621 15.8432C0.971893 16.0562 1.29142 16.2293 1.69083 16.3624C1.89053 16.429 2.07692 16.4822 2.27663 16.5089C2.47633 16.5355 2.66272 16.5621 2.86243 16.5621C3.10207 16.5621 3.31509 16.5488 3.48817 16.5089C3.66124 16.4689 3.79438 16.4157 3.9142 16.3491C4.02071 16.2825 4.10059 16.2027 4.14053 16.1228C4.19379 16.0429 4.22041 15.9497 4.22041 15.8432C4.22041 15.6967 4.16716 15.5636 4.06065 15.4704C3.99408 15.4172 3.92751 15.3639 3.84763 15.324C3.76775 15.284 3.67456 15.2441 3.58136 15.2041C3.47485 15.1642 3.35503 15.1376 3.20858 15.0976C3.06213 15.0577 2.90237 15.0178 2.71598 14.9645C2.4497 14.8979 2.22337 14.8447 2.01035 14.7781C1.79734 14.7115 1.61095 14.6583 1.45118 14.5917C1.27811 14.5251 1.11834 14.4453 0.971893 14.3388C0.825444 14.2322 0.692308 14.1124 0.572485 13.9793C0.439349 13.8328 0.346154 13.6598 0.279586 13.4601C0.213018 13.2737 0.186391 13.0473 0.186391 12.821C0.186391 12.6213 0.213018 12.4216 0.266272 12.2352C0.319527 12.0488 0.399408 11.8757 0.519231 11.716C0.62574 11.5429 0.772189 11.3964 0.931953 11.2633C1.09172 11.1302 1.29142 11.0237 1.50444 10.9305C1.71746 10.8373 1.9571 10.7707 2.22337 10.7175C2.48965 10.6642 2.76923 10.6509 3.07544 10.6509C3.28846 10.6509 3.50148 10.6642 3.7145 10.6908C3.92751 10.7175 4.12722 10.7574 4.34024 10.8107C4.55325 10.8639 4.73965 10.9305 4.92604 11.0104C5.11243 11.0902 5.27219 11.1834 5.43195 11.2766L5.53846 11.3432L5.49852 11.4497L5.09911 12.4349L5.03254 12.5947L4.88609 12.5015C4.73965 12.4216 4.59319 12.3417 4.44675 12.2751C4.3003 12.2086 4.14053 12.1553 3.99408 12.1154C3.83432 12.0754 3.68787 12.0355 3.54142 12.0222C3.39497 11.9956 3.24852 11.9956 3.10207 11.9956C2.86243 11.9956 2.64941 12.0222 2.47633 12.0488C2.31657 12.0888 2.17012 12.142 2.06361 12.2219C1.9571 12.2885 1.89053 12.3683 1.83728 12.4615C1.78402 12.5547 1.77071 12.6479 1.77071 12.7678C1.77071 12.9142 1.82396 13.034 1.93047 13.1272C1.99704 13.1805 2.06361 13.2337 2.14349 13.2737C2.22337 13.3136 2.31657 13.3535 2.40976 13.3935C2.51627 13.4334 2.63609 13.4601 2.78254 13.5C2.92899 13.5399 3.08876 13.5799 3.27515 13.6331C3.52811 13.6864 3.76775 13.753 3.96746 13.8195C4.18047 13.8861 4.36686 13.9393 4.52663 14.0059C4.6997 14.0725 4.85947 14.1524 5.00592 14.2589C5.15237 14.3654 5.2855 14.4852 5.40533 14.6183C5.53846 14.7648 5.63166 14.9379 5.69822 15.1243C5.76479 15.3107 5.79142 15.5237 5.79142 15.7633C5.79142 16.1627 5.68491 16.5355 5.45858 16.8683C5.35207 17.0281 5.20562 17.1879 5.04586 17.3077C4.88609 17.4408 4.68639 17.5473 4.47337 17.6405C4.26035 17.7337 4.02071 17.8003 3.75444 17.8535C3.48817 17.9068 3.20858 17.9201 2.90237 17.9201L2.86243 17.8935ZM17.2012 0.14645V6.93639V7.08284H17.0547H16.0163H15.9497L15.9098 7.02959L12.4216 2.75592V6.93639V7.08284H12.2751H11.0237H10.8772V6.93639V0.14645V0H11.0237H12.0621H12.1287L12.1686 0.0532544L15.6568 4.32692V0.14645V0H15.8033H17.0547H17.2012V0.14645Z" fill="#585858"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_16708_918">
                                                        <rect width="18" height="17.9201" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>

                                            <span>
                        Преимущества: {{ $product->advantages ?? 'сатива' }}
                    </span>

                                        </div>


                                        <div class="product-info-row">

                                            <svg width="15" height="20" viewBox="0 0 15 20" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M2.61729 1.4292H12.3694C12.3418 1.88333 12.2867 2.29739 12.2178 2.68473H2.75503C2.68616 2.29739 2.63106 1.88333 2.60352 1.4292H2.61729Z"
                                                    fill="#585858"></path>
                                            </svg>

                                            <span>
                        {{ $product->yield ?? 'Высокая урожайность' }}
                    </span>

                                        </div>

                                    </div>


                                    <div class="product-price">

                                        @if($product->old_price)
                                            <span class="product-old-price">
                        ${{ number_format($product->old_price, 2) }}
                    </span>
                                        @endif

                                        <span class="product-current-price">
                    {{ number_format($product->price, 0, '.', ' ') }} р
                </span>

                                    </div>


                                    <div class="products-pagination">

                                        <a href="#" class="pagination-button active">
                                            1
                                        </a>

                                        <a href="#" class="pagination-button">
                                            2
                                        </a>

                                        <a href="#" class="pagination-button">
                                            3
                                        </a>

                                    </div>


                                    <div class="product-actions">

                                        <div class="quantity">

                                            <button type="button" class="quantity-button">
                                                −
                                            </button>

                                            <span class="quantity-value">
                        1
                    </span>

                                            <button type="button" class="quantity-button">
                                                +
                                            </button>

                                        </div>


                                        <button type="button" class="add-to-cart">
                                            В корзину
                                        </button>

                                    </div>


                                </article>

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
@endsection
