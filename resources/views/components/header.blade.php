<header class="header">



    <livewire:product-search />





    <!-- Меню -->
    <nav class="main-menu">

        <div class="">
            <div class="menu-inner">

                <a href="{{ route('catalog.index') }}" class="menu-link">
                    Категории
                </a>


                <a href="{{ route('seedbanks.index')  }}" class="menu-link">
                    Сидбанки
                </a>


                <a href="#" class="menu-link">
                    Акции и бонусы
                </a>


                <a href="{{ route('delivery.index') }}" class="menu-link">
                    Доставка и оплата
                </a>


                <!-- Меню с подменю -->
                <div class="menu-dropdown">

                    <button class="menu-dropdown-button">
                        Ссылка
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>

                    <div class="dropdown-content">

                        <a href="{{ url('/') }}">Главная</a>
                        <a href="{{ url('/cart') }}">Корзина</a>
                        <a href="{{ url('/cart-2') }}">Корзина 2</a>
                        <a href="{{ url('/cart-3') }}">Корзина 3</a>
                        <a href="{{ url('/delivery') }}">Доставка и оплата</a>
                        <a href="{{ url('/news') }}">Новости</a>
                        <a href="{{ url('/news-details') }}">Детальная новость</a>
                        <a href="{{ url('/product') }}">Товар</a>
                        <a href="{{ url('/product-2') }}">Товар 2</a>
                        <a href="{{ url('/product-details') }}">Детальная страница товара</a>
                        <a href="{{ url('/text') }}">Текстовая страница</a>


                    </div>

                </div>


                <!-- Второе меню с подменю -->
                <div class="menu-dropdown">

                    <button class="menu-dropdown-button">
                        Cсылка
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>

                    <div class="dropdown-content">

                        <a href="#">Пункт меню 1</a>
                        <a href="#">Пункт меню 2</a>
                        <a href="#">Пункт меню 3</a>
                        <a href="#">Пункт меню 4</a>

                    </div>

                </div>



                <div class="mobile-categories">



                    @foreach($categories as $category)
                        <a href="{{ route('catalog.category', $category->slug) }}"
                           class="">
                            {{ $category->name }}
                        </a>
                    @endforeach


                </div>

            </div>
        </div>


    </nav>

</header>
