<?php

namespace App\Providers;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.footer', function ($view) {

            $cart = session()->get('cart', []);

            $variantIds = array_keys($cart);

            $variants = ProductVariant::with('product')
                ->whereIn('id', $variantIds)
                ->get()
                ->keyBy('id');

            $cartTotal = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $cartCount = collect($cart)->sum('quantity');

            $view->with([
                'cart' => $cart,
                'variants' => $variants,
                'cartTotal' => $cartTotal,
                'cartCount' => $cartCount,
            ]);
        });
    }
}
