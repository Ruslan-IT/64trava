<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(CartService $cart): void
    {
        $this->loadCount($cart);
    }

    #[On('cart-updated')]
    public function refresh(CartService $cart): void
    {
        $this->loadCount($cart);
    }

    private function loadCount(CartService $cart): void
    {
        $this->count = $cart->count();
    }

    public function render()
    {
        return view('livewire.cart-count');
    }
}
