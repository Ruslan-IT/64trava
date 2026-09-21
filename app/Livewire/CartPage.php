<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use App\Services\BonusService;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPage extends Component
{
    public array $cart = [];

    public int|float $cartTotal = 0;

    public int $cartCount = 0;

    public array $bonuses = [];

    public array $selectedItems = [];

    public function mount(CartService $cart): void
    {
        $this->loadCart($cart);

        $selected = session('cart_selected');

        if (!is_array($selected) || empty($selected)) {
            $selected = array_map(
                'intval',
                array_keys($this->cart)
            );

            session()->put('cart_selected', $selected);
        }

        $this->selectedItems = array_map('intval', $selected);

        $this->calculateSelectedTotal();
    }

    #[On('cart-updated')]
    public function refreshCart(CartService $cart): void
    {
        $this->loadCart($cart);

        $cartIds = array_map(
            'intval',
            array_keys($this->cart)
        );

        $this->selectedItems = array_values(
            array_intersect(
                array_map('intval', $this->selectedItems),
                $cartIds
            )
        );

        session()->put('cart_selected', $this->selectedItems);

        $this->calculateSelectedTotal();
    }

    private function loadCart(CartService $cart): void
    {
        $this->cart = $cart->get();

        $this->cartTotal = 0;
        $this->cartCount = 0;

        $this->bonuses = app(BonusService::class)
            ->calculate($this->getSelectedCart());
    }

    private function calculateSelectedTotal(): void
    {
        $total = 0;
        $count = 0;

        foreach ($this->cart as $variantId => $item) {

            if (!in_array(
                (int) $variantId,
                $this->selectedItems,
                true
            )) {
                continue;
            }

            $quantity = (int) $item['quantity'];
            $price = (float) $item['price'];

            $total += $quantity * $price;
            $count += $quantity;
        }

        $this->cartTotal = $total;
        $this->cartCount = $count;

        // Бонусы считаем только по выбранным товарам
        $selectedCart = $this->getSelectedCart();

        $this->bonuses = app(BonusService::class)
            ->calculate($selectedCart);
    }

    #[On('setSelectedItems')]
    public function setSelectedItems(
        array $variantIds,
        CartService $cart
    ): void {
        $cartItems = $cart->get();

        $selected = [];

        foreach ($variantIds as $variantId) {

            $variantId = (int) $variantId;

            if (isset($cartItems[$variantId])) {
                $selected[] = $variantId;
            }
        }

        $this->selectedItems = $selected;

        session()->put(
            'cart_selected',
            $this->selectedItems
        );

        $this->calculateSelectedTotal();
    }

    public function increment(
        int $variantId,
        CartService $cart
    ): void {
        $variant = ProductVariant::find($variantId);

        if (!$variant || $variant->stock <= 0) {
            return;
        }

        $items = $cart->get();

        if (!isset($items[$variantId])) {
            return;
        }

        $quantity = $items[$variantId]['quantity'];

        if ($quantity >= $variant->stock) {
            return;
        }

        $cart->update($variant, $quantity + 1);

        $this->loadCart($cart);
        $this->calculateSelectedTotal();

        $this->dispatch('cart-updated');
    }

    public function decrement(
        int $variantId,
        CartService $cart
    ): void {
        $variant = ProductVariant::find($variantId);

        if (!$variant) {
            return;
        }

        $items = $cart->get();

        if (!isset($items[$variantId])) {
            return;
        }

        $quantity = $items[$variantId]['quantity'];

        if ($quantity <= 1) {
            return;
        }

        $cart->update($variant, $quantity - 1);

        $this->loadCart($cart);
        $this->calculateSelectedTotal();

        $this->dispatch('cart-updated');
    }

    public function remove(
        int $variantId,
        CartService $cart
    ): void {
        $cart->remove($variantId);

        $this->selectedItems = array_values(
            array_filter(
                $this->selectedItems,
                fn ($id) => (int) $id !== $variantId
            )
        );

        session()->put(
            'cart_selected',
            $this->selectedItems
        );

        $this->loadCart($cart);
        $this->calculateSelectedTotal();

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $variantIds = array_keys($this->cart);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        return view('livewire.cart-page', [
            'variants' => $variants,
        ]);
    }

    public function selectionChanged(): void
    {
        $this->selectedItems = array_map(
            'intval',
            $this->selectedItems
        );

        session()->put(
            'cart_selected',
            $this->selectedItems
        );

        $this->calculateSelectedTotal();
    }


    private function getSelectedCart(): array
    {
        $selectedCart = [];

        foreach ($this->cart as $variantId => $item) {
            if (in_array(
                (int) $variantId,
                $this->selectedItems,
                true
            )) {
                $selectedCart[$variantId] = $item;
            }
        }

        return $selectedCart;
    }
}
