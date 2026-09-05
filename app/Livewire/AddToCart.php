<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;

    public ?int $selectedVariantId = null;

    public int $quantity = 1;


    public bool $addedToCart = false;

    public function mount(Product $product): void
    {
        $this->product = $product;

        $variant = $product->variants
            ->first(fn (ProductVariant $variant) => $variant->stock > 0);

        $this->selectedVariantId = $variant?->id;
    }

    public function selectVariant(int $variantId): void
    {
        $variant = $this->product->variants
            ->firstWhere('id', $variantId);

        if (!$variant || $variant->stock <= 0) {
            return;
        }

        $this->selectedVariantId = $variant->id;
        $this->quantity = 1;
    }

    public function increment(): void
    {


        $variant = $this->product->variants
            ->firstWhere('id', $this->selectedVariantId);

        if (!$variant) {
            return;
        }



        if ($this->quantity < $variant->stock) {
            $this->quantity++;
        }


    }

    public function decrement(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(CartService $cart): void
    {
        $variant = $this->product->variants
            ->firstWhere('id', $this->selectedVariantId);

        if (!$variant || $variant->stock <= 0) {
            return;
        }

        $cart->add($variant, $this->quantity);

        $this->addedToCart = true;

        $this->dispatch('cart-updated');

        $this->dispatch('reset-add-button');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
