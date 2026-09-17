<?php

namespace App\Services;

use App\Models\BonusProduct;
use App\Models\ProductVariant;

class BonusService
{
    /**
     * Рассчитать доступные бонусы для текущей корзины.
     *
     * @param array $cart
     * @return array
     */
    public function calculate(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }

        $variantIds = array_keys($cart);

        $variants = ProductVariant::query()
            ->with('product.brand')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $orderTotal = 0;
        $purchasedBrandIds = [];

        foreach ($cart as $variantId => $item) {
            $variant = $variants->get($variantId);

            if (!$variant || !$variant->product) {
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);

            $orderTotal += $price * $quantity;

            if ($variant->product->brand_id) {
                $purchasedBrandIds[] = (int) $variant->product->brand_id;
            }
        }

        $purchasedBrandIds = array_unique($purchasedBrandIds);

        if ($orderTotal <= 0) {
            return [];
        }

        $bonusProducts = BonusProduct::query()
            ->with([
                'product.brand',
                'brands',
            ])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('sort_order')
            ->get();

        $result = [];

        foreach ($bonusProducts as $bonusProduct) {
            $product = $bonusProduct->product;

            if (!$product || !$product->brand) {
                continue;
            }

            $brandId = (int) $product->brand_id;

            /*
             * Dutch Bulk доступен всегда.
             */
            $isDutchBulk = $brandId === 2;

            /*
             * Для остальных брендов проверяем,
             * покупал ли клиент разрешённый бренд.
             */
            if (!$isDutchBulk) {
                $allowedBrandIds = $bonusProduct->brands
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->toArray();

                $isAllowed = !empty(
                array_intersect($purchasedBrandIds, $allowedBrandIds)
                );

                if (!$isAllowed) {
                    continue;
                }
            }

            /*
             * Сколько бонусов положено по этому правилу.
             */
            $quantity = intdiv(
                (int) floor($orderTotal),
                (int) $bonusProduct->threshold
            );

            if ($quantity <= 0) {
                continue;
            }

            /*
             * Ограничиваем количество доступным остатком
             * конкретного бонусного товара.
             */
            $quantity = min(
                $quantity,
                (int) $bonusProduct->stock
            );

            if ($quantity <= 0) {
                continue;
            }

            /*
             * Создаём группу бренда только один раз.
             */
            if (!isset($result[$brandId])) {
                $result[$brandId] = [
                    'brand_id' => $brandId,
                    'brand_name' => $product->brand->name,

                    // Общее количество бонусов категории.
                    'quantity' => $quantity,

                    'products' => [],
                ];
            } else {
                /*
                 * Если в одной категории несколько бонусных товаров,
                 * количество не дублируем.
                 *
                 * Пока берём максимальное доступное количество.
                 */
                $result[$brandId]['quantity'] = max(
                    $result[$brandId]['quantity'],
                    $quantity
                );
            }

            /*
             * В товаре больше НЕ передаём quantity.
             *
             * Товар — это вариант, который клиент может выбрать
             * для распределения общего количества бонусов категории.
             */
            $result[$brandId]['products'][] = [
                'bonus_product_id' => $bonusProduct->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'stock' => (int) $bonusProduct->stock,
                'threshold' => (int) $bonusProduct->threshold,
                'sort_order' => (int) $bonusProduct->sort_order,
            ];
        }

        /*
         * Dutch Bulk всегда первым.
         */
        uasort($result, function ($a, $b) {
            if ($a['brand_id'] === 2) {
                return -1;
            }

            if ($b['brand_id'] === 2) {
                return 1;
            }

            return strcasecmp(
                $a['brand_name'],
                $b['brand_name']
            );
        });

        return [
            'order_total' => $orderTotal,
            'purchased_brand_ids' => $purchasedBrandIds,
            'groups' => array_values($result),
        ];
    }
}
