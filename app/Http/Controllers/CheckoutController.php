<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\OrderExcelService;
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
    public function store(Request $request, CartService $cartService, OrderExcelService $orderExcelService) {
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
