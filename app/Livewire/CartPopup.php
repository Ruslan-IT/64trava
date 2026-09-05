<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPopup extends Component
{
    public array $cart = [];

    public int|float $cartTotal = 0;

    public int $cartQuantity = 0;

    public bool $isOpen = false;

    #[On('open-cart')]
    public function openCart(): void
    {
        $this->isOpen = true;
    }

    public function mount(CartService $cart): void
    {
        $this->loadCart($cart);
    }

    #[On('cart-updated')]
    public function refreshCart(CartService $cart): void
    {
        $this->loadCart($cart);
    }

    private function loadCart(CartService $cart): void
    {
        $this->cart = $cart->get();

        $this->cartTotal = $cart->total();

        $this->cartQuantity = collect($this->cart)->sum('quantity');
    }

    public function render()
    {
        $variantIds = array_keys($this->cart);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        return view('livewire.cart-popup', [
            'variants' => $variants,
        ]);
    }

    public function increment(int $variantId, CartService $cart): void
    {
        $variant = ProductVariant::find($variantId);

        if (!$variant || $variant->stock <= 0) {
            return;
        }

        $items = $cart->get();

        if (!isset($items[$variantId])) {
            return;
        }

        $quantity = $items[$variantId]['quantity'];

        if ($quantity < $variant->stock) {
            $cart->update($variant, $quantity + 1);
        }

        $this->loadCart($cart);
        $this->dispatch('cart-updated');
    }

    public function decrement(int $variantId, CartService $cart): void
    {
        $variant = ProductVariant::find($variantId);

        if (!$variant) {
            return;
        }

        $items = $cart->get();

        if (!isset($items[$variantId])) {
            return;
        }

        $quantity = $items[$variantId]['quantity'];

        if ($quantity > 1) {
            $cart->update($variant, $quantity - 1);
        }

        $this->loadCart($cart);
        $this->dispatch('cart-updated');
    }

    public function remove(int $variantId, CartService $cart): void
    {
        $cart->remove($variantId);

        $this->loadCart($cart);
        $this->dispatch('cart-updated');
    }

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }
}
