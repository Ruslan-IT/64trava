<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;

    public ?int $selectedVariantId = null;

    public int $quantity = 1;

    public bool $addedToCart = false;

    public string $type = 'default';

    public function mount(Product $product, CartService $cart, string $type = 'default'): void
    {
        $this->product = $product;
        $this->type = $type;

        // Фасовка неактивна, пока пользователь сам не выберет.
        $this->selectedVariantId = null;
        $this->syncQuantityFromCart($cart);
    }

    public static function priceSource(Product $product, ?int $selectedVariantId = null): Product|ProductVariant
    {
        $variant = $selectedVariantId !== null
            ? $product->variants->firstWhere('id', $selectedVariantId)
            : $product->variants->first();

        return $variant ?? $product;
    }

    public function selectVariant(int $variantId, CartService $cart): void
    {
        $variant = $this->product->variants
            ->firstWhere('id', $variantId);

        if (!$variant) {
            return;
        }

        $this->selectedVariantId = $variant->id;

        // Состояние +/− только для текущей фасовки: в корзине — показать, иначе скрыть.
        $cartQuantity = (int) ($cart->get()[$variant->id]['quantity'] ?? 0);

        $this->addedToCart = $cartQuantity > 0;
        $this->quantity = $cartQuantity > 0 ? $cartQuantity : 1;
    }

    public function increment(CartService $cart): void
    {
        $variant = $this->product->variants
            ->firstWhere('id', $this->selectedVariantId);

        if (!$variant) {
            return;
        }

        if ($this->type === 'packs') {
            $this->quantity++;

            return;
        }

        if (!$this->addedToCart) {
            return;
        }

        $cartQuantity = (int) ($cart->get()[$variant->id]['quantity'] ?? 0);

        if ($cartQuantity <= 0) {
            return;
        }

        $cart->update($variant, $cartQuantity + 1);
        $this->syncQuantityFromCart($cart);
        $this->dispatch('cart-updated');
    }

    public function decrement(CartService $cart): void
    {
        $variant = $this->product->variants
            ->firstWhere('id', $this->selectedVariantId);

        if (!$variant) {
            return;
        }

        if ($this->type === 'packs') {
            if ($this->quantity > 1) {
                $this->quantity--;
            }

            return;
        }

        if (!$this->addedToCart) {
            return;
        }

        $cartQuantity = (int) ($cart->get()[$variant->id]['quantity'] ?? 0);

        if ($cartQuantity <= 0) {
            return;
        }

        $cart->update($variant, $cartQuantity - 1);
        $this->syncQuantityFromCart($cart);
        $this->dispatch('cart-updated');
    }

    public function addToCart(CartService $cart): void
    {
        $variant = $this->product->variants
            ->firstWhere('id', $this->selectedVariantId);

        if (!$variant) {
            return;
        }

        $quantityToAdd = $this->type === 'packs' ? $this->quantity : 1;

        $cart->add($variant, $quantityToAdd);

        $this->syncQuantityFromCart($cart);

        $this->dispatch('cart-updated');
    }

    #[On('cart-updated')]
    public function refreshQuantity(CartService $cart): void
    {
        $this->syncQuantityFromCart($cart);
    }

    private function syncQuantityFromCart(CartService $cart): void
    {
        if (!$this->selectedVariantId) {
            $this->quantity = 1;
            $this->addedToCart = false;

            return;
        }

        $cartQuantity = (int) ($cart->get()[$this->selectedVariantId]['quantity'] ?? 0);

        $this->addedToCart = $cartQuantity > 0;
        $this->quantity = $cartQuantity > 0 ? $cartQuantity : 1;
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
