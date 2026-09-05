<div>

    <style>
        .order-details-page {
            padding: 24px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Header */

        .order-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .order-header-left {
            min-width: 0;
        }

        .order-title {
            font-size: 24px;
            line-height: 1.2;
            font-weight: 700;
            color: #111827;
            margin: 0 0 6px;
        }

        .order-date {
            font-size: 14px;
            color: #6b7280;
        }

        .order-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Buttons */

        .filament-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 38px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition:
                background-color .15s ease,
                border-color .15s ease,
                color .15s ease,
                box-shadow .15s ease;
            cursor: pointer;
        }

        .filament-button-primary {
            color: #ffffff;
            background: #2563eb;
            border: 1px solid #2563eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
        }

        .filament-button-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        .filament-button-secondary {
            color: #374151;
            background: #ffffff;
            border: 1px solid #d1d5db;
        }

        .filament-button-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        /* Grid */

        .order-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 24px;
            align-items: start;
        }

        .order-main {
            min-width: 0;
        }

        .order-sidebar {
            min-width: 0;
        }

        /* Cards */

        .filament-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow:
                0 1px 2px rgba(0, 0, 0, .03);
            overflow: hidden;
        }

        .filament-card + .filament-card {
            margin-top: 24px;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-title {
            margin: 0;
            font-size: 16px;
            line-height: 1.4;
            font-weight: 600;
            color: #111827;
        }

        .card-description {
            margin-top: 4px;
            font-size: 13px;
            color: #6b7280;
        }

        .card-body {
            padding: 20px;
        }

        /* Customer */

        .customer-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 32px;
        }

        .info-item {
            min-width: 0;
        }

        .info-label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            line-height: 1.4;
            font-weight: 500;
            color: #6b7280;
        }

        .info-value {
            font-size: 14px;
            line-height: 1.5;
            color: #111827;
            word-break: break-word;
        }

        .info-value a {
            color: #2563eb;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        .info-item-full {
            grid-column: 1 / -1;
        }

        .comment-box {
            padding: 12px 14px;
            border-radius: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            color: #374151;
            white-space: pre-wrap;
        }

        /* Badges */

        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 26px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 12px;
            line-height: 1;
            font-weight: 600;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #374151;
        }

        .badge-blue {
            background: #eff6ff;
            color: #1d4ed8;
        }

        /* Products */

        .products-table-wrapper {
            overflow-x: auto;
        }

        .products-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .products-table th {
            padding: 12px 16px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
        }

        .products-table td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            color: #374151;
            font-size: 14px;
            vertical-align: middle;
        }

        .products-table tbody tr:last-child td {
            border-bottom: none;
        }

        .products-table tbody tr:hover {
            background: #fafafa;
        }

        .product-image-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            overflow: hidden;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image-placeholder {
            color: #9ca3af;
            font-size: 11px;
            text-align: center;
        }

        .product-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        .product-variant {
            color: #6b7280;
            font-size: 13px;
        }

        .product-sku {
            color: #6b7280;
            font-size: 13px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .product-price {
            white-space: nowrap;
            font-weight: 500;
            color: #374151;
        }

        .product-quantity {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            padding: 5px 9px;
            border-radius: 7px;
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
            font-size: 13px;
        }

        .product-total {
            white-space: nowrap;
            font-weight: 700;
            color: #111827;
        }

        /* Summary */

        .summary-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            font-size: 14px;
        }

        .summary-label {
            color: #6b7280;
        }

        .summary-value {
            color: #374151;
            font-weight: 500;
            white-space: nowrap;
        }

        .summary-discount {
            color: #059669;
        }

        .summary-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 4px 0;
        }

        .summary-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-top: 4px;
        }

        .summary-total-label {
            color: #111827;
            font-size: 15px;
            font-weight: 600;
        }

        .summary-total-value {
            color: #111827;
            font-size: 20px;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Order number */

        .order-number-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 14px 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .order-number-label {
            color: #6b7280;
            font-size: 13px;
        }

        .order-number-value {
            color: #111827;
            font-size: 13px;
            font-weight: 600;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            text-align: right;
            word-break: break-all;
        }

        /* Empty */

        .empty-products {
            padding: 40px 20px;
            text-align: center;
            color: #6b7280;
        }

        /* Responsive */

        @media (max-width: 1100px) {
            .order-grid {
                grid-template-columns: 1fr;
            }

            .order-sidebar {
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .order-details-page {
                padding: 16px;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-header-actions {
                width: 100%;
            }

            .order-header-actions .filament-button {
                flex: 1;
            }

            .customer-info {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .info-item-full {
                grid-column: auto;
            }

            .card-header,
            .card-body {
                padding: 16px;
            }

            .products-table th,
            .products-table td {
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .order-title {
                font-size: 21px;
            }

            .product-image-wrapper {
                width: 52px;
                height: 52px;
            }

            .order-number-box {
                align-items: flex-start;
                flex-direction: column;
            }

            .order-number-value {
                text-align: left;
            }
        }
    </style>


    <div class="order-details-page">

        {{-- ============================================================
             HEADER
        ============================================================= --}}

        <div class="order-header">

            <div class="order-header-left">

                <h1 class="order-title">
                    Заказ {{ $order['number'] }}
                </h1>

                <div class="order-date">
                    {{ $order['created_at'] }}
                </div>

            </div>


            <div class="order-header-actions">

                <a
                    href="{{ url('/admin/orders') }}"
                    class="filament-button filament-button-secondary"
                >
                    ← Назад
                </a>

                <a
                    href="{{ route('admin.orders.download', $order['number']) }}"
                    class="filament-button filament-button-primary"
                >
                    Скачать Excel
                </a>

            </div>

        </div>


        {{-- ============================================================
             MAIN GRID
        ============================================================= --}}

        <div class="order-grid">


            {{-- ========================================================
                 LEFT COLUMN
            ========================================================= --}}

            <div class="order-main">


                {{-- ====================================================
                     PRODUCTS
                ===================================================== --}}

                <div class="filament-card">

                    <div class="card-header">

                        <h2 class="card-title">
                            Товары
                        </h2>

                        <div class="card-description">
                            Состав заказа
                        </div>

                    </div>


                    @if(!empty($order['items']))

                        <div class="products-table-wrapper">

                            <table class="products-table">

                                <thead>

                                <tr>
                                    <th>Фото</th>
                                    <th>Товар</th>
                                    <th>SKU</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                    <th>Сумма</th>
                                </tr>

                                </thead>


                                <tbody>

                                @foreach($order['items'] as $item)



                                    <tr>

                                        {{-- Фото --}}

                                        <td>

                                            <div class="product-image-wrapper">

                                                @if(!empty($item['image']))

                                                    <img
                                                        src="{{ asset('storage/' . ltrim($item['image'], '/')) }}"
                                                        alt="{{ $item['product_name'] }}"
                                                        class="product-image"
                                                    >

                                                @else

                                                    <div class="product-image-placeholder">
                                                        Нет фото
                                                    </div>

                                                @endif

                                            </div>

                                        </td>


                                        {{-- Товар --}}

                                        <td>

                                            <div class="product-name">
                                                {{ $item['product_name'] }}
                                            </div>

                                            @if(!empty($item['variant_name']))

                                                <div class="product-variant">
                                                    {{ $item['variant_name'] }}
                                                </div>

                                            @endif

                                        </td>


                                        {{-- SKU --}}

                                        <td>

                                                <span class="product-sku">
                                                    {{ $item['sku'] ?: '—' }}
                                                </span>

                                        </td>


                                        {{-- Количество --}}

                                        <td>

                                                <span class="product-quantity">
                                                    {{ $item['quantity'] }}
                                                </span>

                                        </td>


                                        {{-- Цена --}}

                                        <td>

                                                <span class="product-price">
                                                   {{ $item['price'] }}
                                                </span>

                                        </td>


                                        {{-- Сумма --}}

                                        <td>

                                            <span class="product-total">
                                               {{ $item['total'] }}
                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="empty-products">
                            В заказе нет товаров
                        </div>

                    @endif

                </div>


                {{-- ====================================================
                     CUSTOMER
                ===================================================== --}}

                <div class="filament-card">

                    <div class="card-header">

                        <h2 class="card-title">
                            Данные клиента
                        </h2>

                        <div class="card-description">
                            Контактная и адресная информация
                        </div>

                    </div>


                    <div class="card-body">

                        <div class="customer-info">


                            {{-- Имя --}}

                            <div class="info-item">

                                <span class="info-label">
                                    Имя
                                </span>

                                <div class="info-value">
                                    {{ $order['name'] ?: '—' }}
                                </div>

                            </div>


                            {{-- Телефон --}}

                            <div class="info-item">

                                <span class="info-label">
                                    Телефон
                                </span>

                                <div class="info-value">

                                    @if(!empty($order['phone']))

                                        <a href="tel:{{ $order['phone'] }}">
                                            {{ $order['phone'] }}
                                        </a>

                                    @else

                                        —

                                    @endif

                                </div>

                            </div>


                            {{-- Email --}}

                            <div class="info-item">

                                <span class="info-label">
                                    Email
                                </span>

                                <div class="info-value">

                                    @if(!empty($order['email']))

                                        <a href="mailto:{{ $order['email'] }}">
                                            {{ $order['email'] }}
                                        </a>

                                    @else

                                        —

                                    @endif

                                </div>

                            </div>


                            {{-- Город --}}

                            <div class="info-item">

                                <span class="info-label">
                                    Город
                                </span>

                                <div class="info-value">
                                    {{ $order['city'] ?: '—' }}
                                </div>

                            </div>


                            {{-- Адрес --}}

                            <div class="info-item info-item-full">

                                <span class="info-label">
                                    Адрес доставки
                                </span>

                                <div class="info-value">
                                    {{ $order['address'] ?: '—' }}
                                </div>

                            </div>


                            {{-- Комментарий --}}

                            @if(!empty($order['comment']))

                                <div class="info-item info-item-full">

                                    <span class="info-label">
                                        Комментарий
                                    </span>

                                    <div class="info-value comment-box">
                                        {{ $order['comment'] }}
                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>


            </div>


            {{-- ========================================================
                 RIGHT COLUMN
            ========================================================= --}}

            <div class="order-sidebar">


                {{-- ====================================================
                     ORDER INFO
                ===================================================== --}}

                <div class="filament-card">

                    <div class="card-header">

                        <h2 class="card-title">
                            Информация о заказе
                        </h2>

                    </div>


                    <div class="card-body">

                        <div class="order-number-box">

                            <span class="order-number-label">
                                Номер заказа
                            </span>

                            <span class="order-number-value">
                                {{ $order['number'] }}
                            </span>

                        </div>


                        <div style="height: 18px;"></div>


                        <div class="summary-list">

                            {{-- Дата --}}

                            <div class="summary-row">

                                <span class="summary-label">
                                    Дата
                                </span>

                                <span class="summary-value">
                                    {{ $order['created_at'] }}
                                </span>

                            </div>


                            {{-- Оплата --}}

                            <div class="summary-row">

                                <span class="summary-label">
                                    Оплата
                                </span>

                                <span class="summary-value">
                                    <span class="badge badge-blue">
                                        {{ $order['payment_method'] ?: 'Не указано' }}
                                    </span>
                                </span>

                            </div>


                            {{-- Промокод --}}

                            @if(!empty($order['promo_code']))

                                <div class="summary-row">

                                    <span class="summary-label">
                                        Промокод
                                    </span>

                                    <span class="summary-value">
                                        <span class="badge badge-gray">
                                            {{ $order['promo_code'] }}
                                        </span>
                                    </span>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                     TOTALS
                ===================================================== --}}

                <div class="filament-card">

                    <div class="card-header">

                        <h2 class="card-title">
                            Итого
                        </h2>

                    </div>


                    <div class="card-body">

                        <div class="summary-list">


                            {{-- Сумма товаров --}}

                            <div class="summary-row">

                                <span class="summary-label">
                                    Товары
                                </span>

                                <span class="summary-value">
                                    {{ number_format((float) $order['subtotal'], 0, '.', ' ') }} ₽
                                </span>

                            </div>


                            {{-- Скидка --}}

                            @if((float) $order['discount'] > 0)

                                <div class="summary-row">

                                    <span class="summary-label">
                                        Скидка
                                    </span>

                                    <span class="summary-value summary-discount">
                                        −{{ number_format((float) $order['discount'], 0, '.', ' ') }} ₽
                                    </span>

                                </div>

                            @endif


                            {{-- Доставка --}}

                            <div class="summary-row">

                                <span class="summary-label">
                                    Доставка
                                </span>

                                <span class="summary-value">

                                    @if((float) $order['delivery'] > 0)

                                        {{ number_format((float) $order['delivery'], 0, '.', ' ') }} ₽

                                    @else

                                        Бесплатно

                                    @endif

                                </span>

                            </div>


                            <div class="summary-divider"></div>


                            {{-- Всего --}}

                            <div class="summary-total">

                                <span class="summary-total-label">
                                    Итого
                                </span>

                                <span class="summary-total-value">
                                    {{ number_format((float) $order['total'], 0, '.', ' ') }} ₽
                                </span>

                            </div>


                        </div>

                    </div>

                </div>


                {{-- ====================================================
                     DOWNLOAD
                ===================================================== --}}

                <div class="filament-card">

                    <div class="card-body">

                        <a
                            href="{{ route('admin.orders.download', $order['number']) }}"
                            class="filament-button filament-button-primary"
                            style="width: 100%;"
                        >
                            Скачать заказ в Excel
                        </a>

                    </div>

                </div>


            </div>

        </div>

    </div>

</div>
