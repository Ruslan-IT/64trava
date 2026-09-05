<div>

    <style>
        .orders-page {
            padding: 24px;
        }

        .orders-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .orders-table-wrapper {
            width: 100%;
            overflow-x: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .orders-table th {
            padding: 14px 16px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            text-align: left;
            white-space: nowrap;
        }

        .orders-table td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            color: #374151;
        }

        .orders-table tbody tr:last-child td {
            border-bottom: none;
        }

        .orders-table tbody tr:hover {
            background: #fafafa;
        }

        .order-number {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .order-number:hover {
            text-decoration: underline;
        }

        .order-total {
            font-weight: 700;
            white-space: nowrap;
        }

        .download-button {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 7px;
            background: #f3f4f6;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
        }

        .download-button:hover {
            background: #e5e7eb;
        }

        .empty-orders {
            padding: 50px 20px;
            text-align: center;
            color: #6b7280;
        }
    </style>


    <div class="orders-page">

        <div class="orders-title">
            Заказы
        </div>


        <div class="orders-table-wrapper">

            <table class="orders-table">

                <thead>
                <tr>
                    <th>№ заказа</th>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Телефон</th>
                    <th>Email</th>
                  {{--  <th>Город</th>
                    <th>Адрес</th>--}}
                    <th>Итого</th>
                    <th>Excel</th>
                </tr>
                </thead>

                <tbody>

                @forelse($orders as $order)

                    <tr>

                        <td>
                            <a
                                href="{{ url('/admin/orders/' . $order['number']) }}"
                                class="order-number"
                            >
                                {{ $order['number'] }}
                            </a>
                        </td>

                        <td>
                            {{ $order['created_at'] }}
                        </td>

                        <td>
                            {{ $order['name'] }}
                        </td>

                        <td>
                            {{ $order['phone'] }}
                        </td>

                        <td>
                            {{ $order['email'] }}
                        </td>

                       {{-- <td>
                            {{ $order['city'] }}
                        </td>--}}

                       {{-- <td>
                            {{ $order['address'] }}
                        </td>--}}

                        <td class="order-total">
                            {{ number_format((float) $order['total'], 0, '.', ' ') }} ₽
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.orders.download', $order['number']) }}"
                                class="download-button"
                            >
                                Скачать
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9">
                            <div class="empty-orders">
                                Заказов пока нет
                            </div>
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
