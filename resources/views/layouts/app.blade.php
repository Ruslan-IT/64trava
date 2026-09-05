<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @yield('seo')
    @stack('schema')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style-3.css') }}">




    @livewireStyles
</head>

<body>

@include('components.header')





<main>
    @yield('content')
</main>

@include('components.footer')


<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Dropdown меню
        |--------------------------------------------------------------------------
        */

        const dropdownButtons = document.querySelectorAll(
            '.menu-dropdown-button'
        );

        dropdownButtons.forEach(function (button) {

            button.addEventListener('click', function (event) {

                event.preventDefault();
                event.stopPropagation();

                const dropdown = button.closest('.menu-dropdown');

                // Закрываем остальные меню
                document.querySelectorAll('.menu-dropdown').forEach(function (item) {

                    if (item !== dropdown) {
                        item.classList.remove('active');
                    }

                });

                // Переключаем текущее
                dropdown.classList.toggle('active');

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Закрытие dropdown при клике вне меню
        |--------------------------------------------------------------------------
        */

        document.addEventListener('click', function (event) {

            if (!event.target.closest('.menu-dropdown')) {

                document.querySelectorAll('.menu-dropdown').forEach(function (item) {
                    item.classList.remove('active');
                });

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Мобильный поиск
        |--------------------------------------------------------------------------
        */

        const mobileSearchButton = document.querySelector(
            '.mobile-search-button'
        );

        const mobileSearch = document.querySelector(
            '.mobile-search'
        );


        if (mobileSearchButton && mobileSearch) {

            mobileSearchButton.addEventListener('click', function () {

                mobileSearch.classList.toggle('active');

                if (mobileSearch.classList.contains('active')) {

                    const input = mobileSearch.querySelector('.search-input');

                    if (input) {
                        setTimeout(function () {
                            input.focus();
                        }, 100);
                    }

                }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Мобильный бургер
        |--------------------------------------------------------------------------
        */

        const burgerButton = document.querySelector(
            '.burger-button'
        );

        const mainMenu = document.querySelector(
            '.main-menu'
        );


        if (burgerButton && mainMenu) {

            burgerButton.addEventListener('click', function () {

                mainMenu.classList.toggle('active');

                const icon = burgerButton.querySelector('i');

                if (mainMenu.classList.contains('active')) {

                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');

                } else {

                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');

                }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | При переходе с мобильного на desktop
        |--------------------------------------------------------------------------
        */

        window.addEventListener('resize', function () {

            if (window.innerWidth > 768) {

                mainMenu?.classList.remove('active');
                mobileSearch?.classList.remove('active');

                document.querySelectorAll('.menu-dropdown').forEach(function (item) {
                    item.classList.remove('active');
                });

                if (burgerButton) {

                    const icon = burgerButton.querySelector('i');

                    if (icon) {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }

                }

            }

        });




        const cartButton = document.getElementById('cartButton');
        const cartPopup = document.getElementById('cartPopup');
        const cartPopupClose = document.getElementById('cartPopupClose');
        const cartItems = document.getElementById('cartItems');
        const cartSubtotal = document.getElementById('cartSubtotal');
        const cartDiscount = document.getElementById('cartDiscount');
        const cartDelivery = document.getElementById('cartDelivery');
        const cartTotal = document.getElementById('cartTotal');

        const discount = 200;
        const delivery = 100;

        function formatPrice(value) {
            return `${value.toLocaleString('ru-RU')} ₽`;
        }


        function openCart() {

            cartPopup.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }

        function closeCart() {
            cartPopup.classList.remove('is-open');
            document.body.style.overflow = '';
        }

        cartButton.addEventListener('click', openCart);

        cartPopupClose.addEventListener('click', closeCart);

        cartPopup.addEventListener('click', event => {
            if (event.target === cartPopup) {
                closeCart();
            }
        });





    });



</script>








@livewireScripts

<script>
    window.cartUrls = {
        update: @json(route('cart.update')),
        remove: @json(url('/cart/remove')),
    };
</script>
{{--
<script src="{{ asset('js/cart.js') }}"></script>
<script src="{{ asset('js/cart-page.js') }}"></script>--}}
</body>

</html>
