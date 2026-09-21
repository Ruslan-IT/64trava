<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class PromoCodeService
{
    /**
     * Рассчитать сумму промокода по текущей корзине.
     *
     * Доставка не учитывается.
     */
    public function calculate(array $cart): float
    {
        if (empty($cart)) {
            return 0;
        }

        $variantIds = array_keys($cart);

        $variants = ProductVariant::with('product.brand')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $promoAmount = 0;

        foreach ($cart as $variantId => $item) {
            $variant = $variants->get($variantId);

            if (!$variant || !$variant->product) {
                continue;
            }

            $brand = $variant->product->brand;

            if (!$brand) {
                continue;
            }

            $promoPercent = (float) $brand->promo_percent;

            if ($promoPercent <= 0) {
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? $variant->price);

            if ($quantity <= 0 || $price <= 0) {
                continue;
            }

            $itemTotal = $price * $quantity;

            $promoAmount += $itemTotal * $promoPercent / 100;
        }

        return round($promoAmount, 2);
    }

    /**
     * Создать новый промокод.
     */
    public function create(array $cart): PromoCode
    {
        $amount = $this->calculate($cart);

        do {
            $code = 'TRAVA-' . strtoupper(Str::random(6));
        } while (PromoCode::where('code', $code)->exists());

        return PromoCode::create([
            'code' => $code,
            'amount' => $amount,
            'status' => 'active',
        ]);
    }


    private function getSelectedCart(): array
    {
        return array_filter(
            $this->cart,
            fn ($item, $variantId) => in_array(
                (int) $variantId,
                $this->selectedItems,
                true
            ),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
