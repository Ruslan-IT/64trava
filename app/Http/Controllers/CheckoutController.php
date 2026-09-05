<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\OrderExcelService;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Страница оформления заказа.
     */
    public function index(CartService $cartService)
    {
        $cart = $cartService->get();

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $categories = Category::orderBy('name')->get();

        $variantIds = array_keys($cart);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $items = [];

        $total = 0;

        foreach ($cart as $variantId => $item) {

            $variant = $variants[$variantId] ?? null;

            if (!$variant) {
                continue;
            }

            $quantity = (int) $item['quantity'];

            $price = (float) $item['price'];

            $itemTotal = $quantity * $price;

            $total += $itemTotal;

            $items[] = [
                'product_id' => $item['product_id'],

                'variant_id' => $item['variant_id'],

                'product_name' => $variant->product->name,

                'variant_name' => $variant->name,

                'sku' => $item['sku'],

                'quantity' => $quantity,

                'price' => $price,

                'total' => $itemTotal,

                'image' => $variant->product->image,
            ];



        }

        return view('checkout.index', compact(
            'categories',
            'items',
            'total'
        ));
    }

    /**
     * Сохранение заказа.
     */
    public function store(Request $request, CartService $cartService, OrderExcelService $orderExcelService, TelegramService $telegramService) {
        /*
        |--------------------------------------------------------------------------
        | Проверяем данные клиента
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'phone' => ['required', 'string', 'max:30'],

            'email' => ['required', 'email', 'max:255'],

            'city' => ['required', 'string', 'max:255'],

            'address' => ['required', 'string', 'max:500'],

            'comment' => ['nullable', 'string', 'max:1000'],

            'payment_method' => ['required', 'string', 'max:100'],

            'promo_code' => ['nullable', 'string', 'max:100'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Получаем корзину
        |--------------------------------------------------------------------------
        */

        $cart = $cartService->get();

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        /*
        |--------------------------------------------------------------------------
        | Получаем варианты товаров
        |--------------------------------------------------------------------------
        */

        $variantIds = array_keys($cart);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | Формируем товары заказа
        |--------------------------------------------------------------------------
        */

        $items = [];

        $subtotal = 0;

        foreach ($cart as $variantId => $item) {

            $variant = $variants[$variantId] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Если товар удалён из БД
            |--------------------------------------------------------------------------
            */

            if (!$variant) {
                continue;
            }

            $quantity = (int) $item['quantity'];

            /*
            |--------------------------------------------------------------------------
            | Берём цену из корзины.
            | Именно её пользователь видел при добавлении.
            |--------------------------------------------------------------------------
            */

            $price = (float) $item['price'];

            $itemTotal = $quantity * $price;

            $subtotal += $itemTotal;

            $items[] = [
                'product_id' => $item['product_id'],

                'variant_id' => $item['variant_id'],

                'product_name' => $variant->product->name,

                'variant_name' => $variant->name,

                'sku' => $item['sku'],

                'quantity' => $quantity,

                'price' => $price,

                'total' => $itemTotal,

                'image' => $variant->product->image,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Пока скидка и доставка = 0
        |--------------------------------------------------------------------------
        */

        $discount = 0;

        $delivery = 0;

        $total = $subtotal - $discount + $delivery;

        /*
        |--------------------------------------------------------------------------
        | Генерируем номер заказа
        |--------------------------------------------------------------------------
        */

        $orderNumber = 'ORD-' . now()->format('Ymd-His') . '-' . random_int(100, 999);

        /*
        |--------------------------------------------------------------------------
        | Формируем заказ
        |--------------------------------------------------------------------------
        */

        $orderData = [

            'number' => $orderNumber,

            'created_at' => now()->format('d.m.Y H:i:s'),

            /*
            |--------------------------------------------------------------------------
            | Клиент
            |--------------------------------------------------------------------------
            */

            'name' => $validated['name'],

            'phone' => $validated['phone'],

            'email' => $validated['email'],

            'city' => $validated['city'],

            'address' => $validated['address'],

            'comment' => $validated['comment'] ?? '',

            'payment_method' => $validated['payment_method'],

            'promo_code' => $validated['promo_code'] ?? '',

            /*
            |--------------------------------------------------------------------------
            | Заказ
            |--------------------------------------------------------------------------
            */

            'items' => $items,

            'subtotal' => $subtotal,

            'discount' => $discount,

            'delivery' => $delivery,

            'total' => $total,
        ];

        /*
        |--------------------------------------------------------------------------
        | Сохраняем заказ в Excel
        |--------------------------------------------------------------------------
        */

        $orderExcelService->save($orderData);



        /*
|--------------------------------------------------------------------------
| Отправляем заказ в Telegram
|--------------------------------------------------------------------------
*/

        $telegramMessage = "🛒 НОВЫЙ ЗАКАЗ {$orderData['number']}\n\n";

        $telegramMessage .= "👤 Клиент:\n";
        $telegramMessage .= $orderData['name'] . "\n\n";

        $telegramMessage .= "📞 Телефон:\n";
        $telegramMessage .= $orderData['phone'] . "\n\n";

        $telegramMessage .= "📧 Email:\n";
        $telegramMessage .= $orderData['email'] . "\n\n";

        $telegramMessage .= "📍 Город:\n";
        $telegramMessage .= $orderData['city'] . "\n\n";

        $telegramMessage .= "🏠 Адрес:\n";
        $telegramMessage .= $orderData['address'] . "\n\n";

        if (!empty($orderData['comment'])) {
            $telegramMessage .= "💬 Комментарий:\n";
            $telegramMessage .= $orderData['comment'] . "\n\n";
        }

        $telegramMessage .= "📦 Товары:\n";

        foreach ($orderData['items'] as $item) {

            $telegramMessage .= "• {$item['product_name']}";

            if (!empty($item['variant_name'])) {
                $telegramMessage .= " — {$item['variant_name']}";
            }

            $telegramMessage .= "\n";

            if (!empty($item['sku'])) {
                $telegramMessage .= "  Артикул: {$item['sku']}\n";
            }

            $telegramMessage .= "  {$item['quantity']} шт. × "
                . number_format($item['price'], 0, '.', ' ')
                . " ₽ = "
                . number_format($item['total'], 0, '.', ' ')
                . " ₽\n\n";
        }

        $telegramMessage .= "💰 Сумма товаров: "
            . number_format($orderData['subtotal'], 0, '.', ' ')
            . " ₽\n";

        $telegramMessage .= "🏷 Скидка: "
            . number_format($orderData['discount'], 0, '.', ' ')
            . " ₽\n";

        $telegramMessage .= "🚚 Доставка: "
            . number_format($orderData['delivery'], 0, '.', ' ')
            . " ₽\n";

        $telegramMessage .= "💵 ИТОГО: "
            . number_format($orderData['total'], 0, '.', ' ')
            . " ₽\n\n";

        $telegramMessage .= "💳 Оплата: {$orderData['payment_method']}\n";

        if (!empty($orderData['promo_code'])) {
            $telegramMessage .= "🎟 Промокод: {$orderData['promo_code']}\n";
        }

        $telegramMessage .= "\n🕐 {$orderData['created_at']}";

        /*
        |--------------------------------------------------------------------------
        | Отправляем сообщение
        |--------------------------------------------------------------------------
        */

        try {
            $telegramResult = $telegramService->sendMessage($telegramMessage);

            if ($telegramResult['status'] !== 200) {
                \Log::error('Telegram order notification failed', [
                    'order' => $orderNumber,
                    'response' => $telegramResult,
                ]);
            }
        } catch (\Throwable $e) {

            \Log::error('Telegram order notification exception', [
                'order' => $orderNumber,
                'message' => $e->getMessage(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Очищаем корзину
        |--------------------------------------------------------------------------
        */

        $cartService->clear();

        /*
        |--------------------------------------------------------------------------
        | Сохраняем заказ в session,
        | чтобы показать страницу успешного заказа.
        |--------------------------------------------------------------------------
        */

        session()->put('order_success', $orderData);

        /*
        |--------------------------------------------------------------------------
        | Переходим на страницу успешного заказа
        |--------------------------------------------------------------------------
        */

        return redirect()->route('order.success');
    }

    /**
     * Страница успешного оформления заказа.
     */
    public function success()
    {

        $categories = Category::orderBy('name')->get();


        $order = session('order_success');

        if (!$order) {
            return redirect()->route('cart.index');
        }

        /*
        |--------------------------------------------------------------------------
        | После показа удаляем заказ из session.
        | Excel при этом остаётся.
        |--------------------------------------------------------------------------
        */

        session()->forget('order_success');

        return view('order.success', compact('order', 'categories'));
    }
}
