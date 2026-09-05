<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart
    ) {
    }



    /**
     * Добавить товар.
     */
    public function add(Request $request)
    {


        $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        if ($variant->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Товара нет в наличии.',
            ], 422);
        }

        $cart = $this->cart->add(
            $variant,
            $request->quantity
        );

        return response()->json([
            'success' => true,
            'message' => 'Товар добавлен в корзину.',
            'cart' => $cart,
            'count' => $this->cart->count(),
            'total' => $this->cart->total(),
        ]);
    }

    /**
     * Изменить количество.
     */
    public function update(Request $request)
    {
        $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        $cart = $this->cart->update(
            $variant,
            $request->quantity
        );

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'count' => $this->cart->count(),
            'total' => $this->cart->total(),
        ]);
    }

    /**
     * Удалить товар.
     */
    public function remove(int $variantId)
    {
        $cart = $this->cart->remove($variantId);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'count' => $this->cart->count(),
            'total' => $this->cart->total(),
        ]);
    }

    /**
     * Очистить корзину.
     */
    public function clear()
    {
        $this->cart->clear();

        return response()->json([
            'success' => true,
            'cart' => [],
            'count' => 0,
            'total' => 0,
        ]);
    }


    public function count()
    {
        return response()->json([
            'success' => true,
            'count' => $this->cart->count(),
        ]);
    }

    /**
     * Показать корзину.
     */
    public function index()
    {
        $cart = $this->cart->get();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $variantIds = array_keys($cart);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        return view('cart.index', [
            'categories'=>$categories,
            'cart' => $cart,
            'variants' => $variants,
            'cartCount' => $this->cart->count(),
            'cartTotal' => $this->cart->total(),
        ]);
    }

    public function popup()
    {
        $cart = $this->cart->get();

        $variantIds = array_keys($cart);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $cartTotal = $this->cart->total();

        $html = view('cart.popup-items', [
            'cart' => $cart,
            'variants' => $variants,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $this->cart->count(),
            'total' => $cartTotal,
            'totalWithDelivery' => $cartTotal + 100,
        ]);
    }
}
