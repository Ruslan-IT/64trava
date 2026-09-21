<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\PromoCode;
use App\Services\BonusService;
use App\Services\CartService;
use App\Services\OrderExcelService;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Страница оформления заказа.
     */
    public function index(CartService $cartService,  BonusService $bonusService)
    {
        $cart = $cartService->get();

        $selectedIds = session('cart_selected');

        if (is_array($selectedIds)) {
            $cart = array_filter(
                $cart,
                fn ($item, $variantId) => in_array(
                    (int) $variantId,
                    array_map('intval', $selectedIds),
                    true
                ),
                ARRAY_FILTER_USE_BOTH
            );
        }

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

                'manufacturer' => $variant->product->brand?->name ?? '',

                'variant_name' => $variant->name,

                'sku' => $item['sku'],

                'quantity' => $quantity,

                'price' => $price,

                'total' => $itemTotal,

                'image' => $variant->product->image,

                'brand_id' => $variant->product->brand_id,
            ];



        }

        //$bonuses = $bonusService->calculate($items, $total);

        $bonuses = $bonusService->calculate($cart);

        return view('checkout.index', compact(
            'categories',
            'items',
            'total',
            'bonuses'
        ));
    }

    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => ['required', 'string', 'max:100'],
        ]);

        $code = trim($request->input('promo_code'));

        $currentPromo = session('promo_code');

        if (
            is_array($currentPromo) &&
            !empty($currentPromo['code']) &&
            strtoupper($currentPromo['code']) === strtoupper($code)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Этот промокод можно использовать только в следующем заказе.',
            ], 422);
        }

        $promoCode = PromoCode::where('code', $code)->first();

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Промокод не найден.',
            ], 422);
        }

        if ($promoCode->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Промокод уже использован или недействителен.',
            ], 422);
        }

        if ($promoCode->expires_at && $promoCode->expires_at->isPast()) {
            $promoCode->update([
                'status' => 'expired',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Срок действия промокода истёк.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'code' => $promoCode->code,
            'amount' => (float) $promoCode->amount,
            'message' => 'Промокод применён.',
        ]);
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

        $selectedIds = session('cart_selected');

        if (is_array($selectedIds)) {
            $cart = array_filter(
                $cart,
                fn ($item, $variantId) => in_array(
                    (int) $variantId,
                    array_map('intval', $selectedIds),
                    true
                ),
                ARRAY_FILTER_USE_BOTH
            );
        }

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $bonuses = session('bonuses', []);

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

                'manufacturer' => $variant->product->brand?->name ?? '',

                'variant_name' => $variant->name,

                'sku' => $item['sku'],

                'quantity' => $quantity,

                'price' => $price,

                'total' => $itemTotal,

                'image' => $variant->product->image,

                'brand_id' => $variant->product->brand_id,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Пока скидка и доставка = 0
        |--------------------------------------------------------------------------
        */

        $discount = 0;

        $promoCode = null;

        if (!empty($validated['promo_code'])) {

            $code = trim($validated['promo_code']);

            $currentPromo = session('promo_code');

            $isCurrentPromo = (
                is_array($currentPromo) &&
                !empty($currentPromo['code']) &&
                strtoupper($currentPromo['code']) === strtoupper($code)
            );

            if (!$isCurrentPromo) {

                $promoCode = PromoCode::where('code', $code)
                    ->where('status', 'active')
                    ->first();

                if (!$promoCode) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'promo_code' => 'Промокод недействителен или уже использован.',
                        ]);
                }

                if ($promoCode->expires_at && $promoCode->expires_at->isPast()) {

                    $promoCode->update([
                        'status' => 'expired',
                    ]);

                    return back()
                        ->withInput()
                        ->withErrors([
                            'promo_code' => 'Срок действия промокода истёк.',
                        ]);
                }

                $discount = min(
                    (float) $promoCode->amount,
                    $subtotal
                );
            }
        }

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

            'promo_code' => (
                !empty($validated['promo_code'])
                    ? $validated['promo_code']
                    : (session('promo_code')['code'] ?? '')
                ),

            /*
            |--------------------------------------------------------------------------
            | Заказ
            |--------------------------------------------------------------------------
            */

            'items' => $items,

            'subtotal' => $subtotal,

            'discount' => $discount,

            'bonuses' => $bonuses,

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

        try {

            /*
            |--------------------------------------------------------------------------
            | Формируем сообщение
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

                if (!empty($item['manufacturer'])) {
                    $telegramMessage .= " — {$item['manufacturer']}";
                }

                if (!empty($item['variant_name'])) {
                    $telegramMessage .= " — {$item['variant_name']}";
                }

                $telegramMessage .= "\n";

                if (!empty($item['sku'])) {
                    $telegramMessage .= "  SKU: {$item['sku']}\n";
                }

                $telegramMessage .= "  {$item['quantity']} шт. × "
                    . number_format($item['price'], 0, '.', ' ')
                    . " ₽ = "
                    . number_format($item['total'], 0, '.', ' ')
                    . " ₽\n";
            }

            if (!empty($orderData['bonuses'])) {

                $telegramMessage .= "\n🎁 Бонусные семена:\n";

                foreach ($orderData['bonuses'] as $bonus) {

                    $telegramMessage .= "• {$bonus['name']}";

                    if (!empty($bonus['manufacturer'])) {
                        $telegramMessage .= " — {$bonus['manufacturer']}";
                    }

                    $telegramMessage .= " — {$bonus['quantity']} шт.\n";
                }
            }

            $telegramMessage .= "\n";

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

                if ($orderData['discount'] > 0) {

                    $telegramMessage .= "🎟 Промокод: {$orderData['promo_code']}\n";

                    $telegramMessage .= "🏷 Скидка по промокоду: "
                        . number_format($orderData['discount'], 0, '.', ' ')
                        . " ₽\n";

                } else {

                    $telegramMessage .= "🎁 Выдан промокод: {$orderData['promo_code']}\n";

                }
            }

            $telegramMessage .= "\n🕐 {$orderData['created_at']}";


            /*
            |--------------------------------------------------------------------------
            | Отправляем текст заказа
            |--------------------------------------------------------------------------
            */

            $telegramResult = $telegramService->sendMessage($telegramMessage);

            if ($telegramResult['status'] !== 200) {
                \Log::error('Telegram order message failed', [
                    'order' => $orderData['number'],
                    'response' => $telegramResult,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Получаем путь к Excel-файлу
            |--------------------------------------------------------------------------
            */

            $excelPath = $orderExcelService->getFilePath(
                $orderData['number']
            );


            /*
            |--------------------------------------------------------------------------
            | Отправляем Excel
            |--------------------------------------------------------------------------
            */

            if ($excelPath) {

                $telegramFileResult = $telegramService->sendDocument(
                    $excelPath,
                    '📎 Excel заказа ' . $orderData['number']
                );

                if ($telegramFileResult['status'] !== 200) {
                    \Log::error('Telegram order file failed', [
                        'order' => $orderData['number'],
                        'file' => $excelPath,
                        'response' => $telegramFileResult,
                    ]);
                }
            }

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Telegram не должен ломать оформление заказа
            |--------------------------------------------------------------------------
            */

            \Log::error('Telegram order notification exception', [
                'order' => $orderData['number'],
                'message' => $e->getMessage(),
            ]);
        }
        if ($promoCode) {
            $promoCode->update([
                'status' => 'used',
                'used_at' => now(),
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
