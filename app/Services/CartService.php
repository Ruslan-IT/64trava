<?php

namespace App\Services;

use App\Models\ProductVariant;

class CartService
{
    /**
     * Получить корзину из сессии.
     */
    public function get(): array
    {
        return session()->get('cart', []);
    }

    /**
     * Добавить вариант товара в корзину.
     */
    public function add(ProductVariant $variant, int $quantity = 1): array
    {
        $cart = $this->get();

        $variantId = $variant->id;

        // Если товара ещё нет в корзине
        if (!isset($cart[$variantId])) {
            $cart[$variantId] = [
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'quantity' => 0,
                'price' => $variant->price,
            ];
        }

        // Увеличиваем количество
        $newQuantity = $cart[$variantId]['quantity'] + $quantity;

        // Не даём превысить остаток
        if ($newQuantity > $variant->stock) {
            $newQuantity = $variant->stock;
        }

        $cart[$variantId]['quantity'] = $newQuantity;

        session()->put('cart', $cart);

        return $cart;
    }

    /**
     * Изменить количество.
     */
    public function update(ProductVariant $variant, int $quantity): array
    {
        $cart = $this->get();

        if ($quantity <= 0) {
            unset($cart[$variant->id]);
        } else {
            $cart[$variant->id] = [
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'quantity' => min($quantity, $variant->stock),
                'price' => $variant->price,
            ];
        }

        session()->put('cart', $cart);

        return $cart;
    }

    /**
     * Удалить товар из корзины.
     */
    public function remove(int $variantId): array
    {
        $cart = $this->get();

        unset($cart[$variantId]);

        session()->put('cart', $cart);

        return $cart;
    }

    /**
     * Очистить корзину.
     */
    public function clear(): void
    {
        session()->forget('cart');
    }

    /**
     * Общее количество товаров.
     */
    public function count(): int
    {
        return collect($this->get())->sum('quantity');
    }

    /**
     * Общая стоимость корзины.
     */
    public function total(): float
    {
        return collect($this->get())->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }
}
