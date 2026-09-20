<div class="catalog-layout ">
    <aside class="catalog-filter ">

        <button type="button" class="catalog-filter-close">
            ×
        </button>

        <div class="filter-percent-values">

            <div class="filter-percent-value {{ $activeThcHandle === 'min' ? 'active' : '' }}">
                {{ $thcMin ?? 1 }}%
            </div>

            <div class="filter-percent-value {{ $activeThcHandle === 'max' ? 'active' : '' }}">
                {{ $thcMax ?? 60 }}%
            </div>

        </div>

        <div class="filter-range filter-thc-range">

            <div class="filter-thc-track"></div>

            <div
                class="filter-thc-range-fill"
                style="
            left: {{ (($thcMin ?? 1) - 1) / 59 * 100 }}%;
            right: {{ 100 - (($thcMax ?? 60) - 1) / 59 * 100 }}%;
        "
            ></div>

            <input
                type="range"
                min="1"
                max="60"
                value="{{ $thcMin ?? 1 }}"
                wire:model.live="thcMin"
                wire:mousedown="setThcMin"
                class="filter-thc-input filter-thc-min"
            >

            <input
                type="range"
                min="1"
                max="60"
                value="{{ $thcMax ?? 60 }}"
                wire:model.live="thcMax"
                wire:mousedown="setThcMax"
                class="filter-thc-input filter-thc-max"
            >

        </div>

        <div class="filter-divider"></div>


        <div class="filter-group filter-group-light">

            <button type="button" class="filter-group-header">
                <span>Сведение</span>

                <svg class="filter-arrow" width="14" height="8" viewBox="0 0 14 8" fill="none">
                    <path d="M1 7L7 1L13 7" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>

            <div class="filter-options">

                <label class="filter-option">

                    <input
                        type="checkbox"
                        wire:model.live="seedTypes"
                        value="F"
                    >

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">
                Феминизированный
            </span>

                    <span class="filter-option-count">
                {{ $seedTypeCounts['F'] ?? 0 }}
            </span>

                </label>

                <label class="filter-option">

                    <input
                        type="checkbox"
                        wire:model.live="seedTypes"
                        value="A"
                    >

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">
                Автоцветущий
            </span>

                    <span class="filter-option-count">
                {{ $seedTypeCounts['A'] ?? 0 }}
            </span>

                </label>

                <label class="filter-option">

                    <input
                        type="checkbox"
                        wire:model.live="seedTypes"
                        value="R"
                    >

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">
                Регулярный
            </span>

                    <span class="filter-option-count">
                {{ $seedTypeCounts['R'] ?? 0 }}
            </span>

                </label>

            </div>

        </div>

        <div class="filter-divider"></div>

        <div class="filter-group filter-group-flowering">

            <button type="button" class="filter-group-header">
                <span>Период цветения</span>

                <svg class="filter-arrow" width="14" height="8" viewBox="0 0 14 8" fill="none">
                    <path d="M1 7L7 1L13 7" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>

            <div class="filter-subtitle">
                Время от посадки до урожая
            </div>

            <div class="filter-week-values">

                <div class="filter-week-value">
                    {{ $floweringMin }} дней
                </div>

                <div class="filter-week-value">
                    {{ $floweringMax }} дней
                </div>

            </div>

            <div class="filter-range filter-flowering-range">

                <div class="filter-flowering-track"></div>

                <div
                    class="filter-flowering-range-fill"
                    style="
                left: {{ (($floweringMin - 50) / 40) * 100 }}%;
                right: {{ 100 - (($floweringMax - 50) / 40) * 100 }}%;
            "
                ></div>

                <input
                    type="range"
                    min="50"
                    max="90"
                    value="{{ $floweringMin }}"
                    wire:model.live="floweringMin"
                    class="filter-flowering-input filter-flowering-min"
                >

                <input
                    type="range"
                    min="50"
                    max="90"
                    value="{{ $floweringMax }}"
                    wire:model.live="floweringMax"
                    class="filter-flowering-input filter-flowering-max"
                >

            </div>

        </div>


        <div class="filter-divider"></div>


        <div class="filter-group filter-group-genotype">

            <button type="button" class="filter-group-header">
                <span>Генотип</span>

                <svg class="filter-arrow" width="14" height="8" viewBox="0 0 14 8" fill="none">
                    <path d="M1 7L7 1L13 7" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>

            <div class="filter-genotype-list">

                <label class="filter-option">

                    <input
                        type="radio"
                        name="genotype"
                        value="sativa"
                        wire:model.live="genotype"
                    >

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">
                Сатива
            </span>

                    <span class="filter-option-count">
                {{ $genotypeCounts['sativa'] ?? 0 }}
            </span>

                </label>


                <label class="filter-option">

                    <input
                        type="radio"
                        name="genotype"
                        value="balance"
                        wire:model.live="genotype"
                    >

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">
                Баланс
            </span>

                    <span class="filter-option-count">
                {{ $genotypeCounts['balance'] ?? 0 }}
            </span>

                </label>


                <label class="filter-option">

                    <input
                        type="radio"
                        name="genotype"
                        value="indica"
                        wire:model.live="genotype"
                    >

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">
                Индика
            </span>

                    <span class="filter-option-count">
                {{ $genotypeCounts['indica'] ?? 0 }}
            </span>

                </label>

            </div>

        </div>

        <div class="filter-divider"></div>


        {{--<div class="filter-group filter-group-genotype">

            <button type="button" class="filter-group-header">
                <span>Генотип</span>

                <svg class="filter-arrow" width="14" height="8" viewBox="0 0 14 8" fill="none">
                    <path d="M1 7L7 1L13 7" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>

            <div class="filter-genotype-list bot">

                <label class="filter-option">
                    <input type="radio" name="genotype" value="sativa">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Haze</span>

                    <span class="filter-option-count">30</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="mostly-sativa">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Haze</span>

                    <span class="filter-option-count">43</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="indica">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Haze</span>

                    <span class="filter-option-count">40</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="mostly-indica">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Haze</span>

                    <span class="filter-option-count">20</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="hybrid">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Haze</span>

                    <span class="filter-option-count">15</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="hybrid">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Fast version</span>

                    <span class="filter-option-count">15</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="hybrid">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Big devil</span>

                    <span class="filter-option-count">15</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="hybrid">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Indica</span>

                    <span class="filter-option-count">15</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="hybrid">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Sativa</span>

                    <span class="filter-option-count">15</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="hybrid">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Regular</span>

                    <span class="filter-option-count">15</span>
                </label>

                <label class="filter-option">
                    <input type="radio" name="genotype" value="hybrid">

                    <span class="filter-radio"></span>

                    <span class="filter-option-name">Haze</span>

                    <span class="filter-option-count">15</span>
                </label>

            </div>

        </div>--}}

        <div class="filter-divider"></div>


        <div class="filter-group filter-group-height">

            <button type="button" class="filter-group-header">

                <span>Высота</span>

                <svg
                    class="filter-arrow"
                    width="14"
                    height="8"
                    viewBox="0 0 14 8"
                    fill="none"
                >
                    <path
                        d="M1 7L7 1L13 7"
                        stroke="currentColor"
                        stroke-width="1.5"
                    />
                </svg>

            </button>


            <div class="filter-height-values">

                <div class="filter-height-value">
                    {{ $heightMin ?? 70 }} см
                </div>

                <div class="filter-height-value">
                    {{ $heightMax ?? 200 }} см
                </div>

            </div>


            <div class="filter-range filter-height-range">

                <div class="filter-height-track"></div>

                <div
                    class="filter-height-range-fill"
                    style="
                left: {{ (($heightMin ?? 70) - 70) / 130 * 100 }}%;
                right: {{ 100 - (($heightMax ?? 200) - 70) / 130 * 100 }}%;
            "
                ></div>


                <input
                    type="range"
                    min="70"
                    max="200"
                    value="{{ $heightMin ?? 70 }}"
                    wire:model.live="heightMin"
                    class="filter-height-input filter-height-min"
                >


                <input
                    type="range"
                    min="70"
                    max="200"
                    value="{{ $heightMax ?? 200 }}"
                    wire:model.live="heightMax"
                    class="filter-height-input filter-height-max"
                >

            </div>

        </div>

        <div class="filter-divider"></div>


        <div class="filter-buttons">

            <button type="button" class="filter-apply">
                Применить
            </button>

            <button type="button" class="filter-reset" wire:click="resetFilters">
                Сбросить фильтры
            </button>

        </div>

    </aside>
    <div class="catalog-content">
        <div class="catalog-products">

           {{-- @if($currentCategory || $brand || $tag)

                <div class="catalog-welcome">

                    <div class="catalog-welcome-image">

                        @if($brand && $brand->logo)

                            <img
                                src="{{ asset('storage/' . $brand->logo) }}"
                                alt="{{ $brand->name }}"
                            >

                        @elseif($currentCategory && $currentCategory->image)

                            <img
                                src="{{ asset('storage/' . $currentCategory->image) }}"
                                alt="{{ $currentCategory->name }}"
                            >

                        @elseif($tag && $tag->image)

                            <img
                                src="{{ asset('storage/' . $tag->image) }}"
                                alt="{{ $tag->name }}"
                            >

                        @else

                            <img
                                src="{{ asset('images/no-img2.png') }}"
                                alt="Изображение отсутствует"
                            >

                        @endif

                    </div>

                    <div class="catalog-welcome-content">

                        <div class="catalog-welcome-title">
                            <span>{{ $catalogInfo->name }}</span>
                            <span class="catalog-welcome-slash"></span>
                            <span class="catalog-welcome-country"></span>
                        </div>

                        <div class="catalog-welcome-text">
                            {!! $catalogInfo->description !!}
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

            </div>--}}


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



