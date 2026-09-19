<?php

namespace App\Services;

use App\Models\BonusProduct;
use App\Models\ProductVariant;

class BonusService
{
    public function calculate(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }

        /*
         * Получаем варианты товаров из корзины.
         */
        $variantIds = array_keys($cart);

        $variants = ProductVariant::query()
            ->with('product.brand')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        /*
         * Общая сумма заказа.
         */
        $orderTotal = 0;

        /*
         * Сколько денег потрачено на каждый бренд.
         *
         * Например:
         *
         * [
         *     2 => 5000,
         *     5 => 3200,
         *     8 => 7000,
         * ]
         */
        $purchasedBrandTotals = [];

        /*
         * ID брендов, которые покупались.
         */
        $purchasedBrandIds = [];

        foreach ($cart as $variantId => $item) {
            $variant = $variants->get($variantId);

            if (!$variant || !$variant->product) {
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);

            if ($quantity <= 0 || $price < 0) {
                continue;
            }

            $itemTotal = $price * $quantity;

            /*
             * Общая сумма заказа.
             */
            $orderTotal += $itemTotal;

            /*
             * Бренд товара.
             */
            $brandId = (int) ($variant->product->brand_id ?? 0);

            if ($brandId <= 0) {
                continue;
            }

            $purchasedBrandIds[] = $brandId;

            /*
             * Суммируем стоимость покупок отдельно
             * по каждому бренду.
             */
            if (!isset($purchasedBrandTotals[$brandId])) {
                $purchasedBrandTotals[$brandId] = 0;
            }

            $purchasedBrandTotals[$brandId] += $itemTotal;
        }

        $purchasedBrandIds = array_values(
            array_unique($purchasedBrandIds)
        );

        if ($orderTotal <= 0) {
            return [];
        }

        /*
         * Получаем доступные бонусные товары.
         */
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

            $bonusBrandId = (int) $product->brand_id;

            /*
             * Сумма, от которой будем рассчитывать бонус.
             */
            $bonusCalculationTotal = 0;

            /*
             * =====================================================
             * DUTCH BULK
             * =====================================================
             *
             * Dutch Bulk (brand_id = 2) доступен всегда.
             *
             * Для него сохраняем существующую логику:
             * количество бонусов считается от всей суммы заказа.
             */
            $isDutchBulk = $bonusBrandId === 2;

            if ($isDutchBulk) {
                $bonusCalculationTotal = $orderTotal;
            } else {
                /*
                 * =================================================
                 * ОСТАЛЬНЫЕ БРЕНДЫ
                 * =================================================
                 *
                 * Получаем бренды, при покупке которых разрешён
                 * данный бонусный товар.
                 */
                $allowedBrandIds = $bonusProduct->brands
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->unique()
                    ->values()
                    ->toArray();

                /*
                 * Если для бонуса не указаны разрешённые бренды,
                 * бонус не показываем.
                 */
                if (empty($allowedBrandIds)) {
                    continue;
                }

                /*
                 * Проверяем, покупал ли клиент хотя бы один
                 * разрешённый бренд.
                 */
                $isAllowed = !empty(
                array_intersect(
                    $purchasedBrandIds,
                    $allowedBrandIds
                )
                );

                if (!$isAllowed) {
                    continue;
                }

                /*
                 * ВАЖНО:
                 *
                 * Теперь считаем сумму только по разрешённым
                 * брендам, а не по всей корзине.
                 *
                 * Например:
                 *
                 * Barneys Farm = 3000 ₽
                 * Dutch Bulk   = 5000 ₽
                 *
                 * Для бонуса Barneys Farm:
                 *
                 * $bonusCalculationTotal = 3000 ₽
                 *
                 * а не 8000 ₽.
                 */


                /*
                  * Если клиент покупает хотя бы один
                  * разрешённый бренд, бонус доступен.
                  *
                  * Количество бонусов считается
                  * от всей суммы заказа.
                  */
                $bonusCalculationTotal = $orderTotal;
            }

            /*
             * Если сумма недостаточная для получения хотя бы
             * одного бонуса — пропускаем товар.
             */
            $threshold = (int) $product->brand->bonus_threshold;

            if ($threshold <= 0 || $bonusCalculationTotal <= 0) {
                continue;
            }

            /*
             * Сколько бонусных семян положено.
             *
             * 4000 / 4000 = 1
             * 8000 / 4000 = 2
             * 3999 / 4000 = 0
             */
            $quantity = intdiv(
                (int) floor($bonusCalculationTotal),
                $threshold
            );

            if ($quantity <= 0) {
                continue;
            }

            /*
             * Нельзя выдать больше бонусных семян,
             * чем есть на складе.
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
            if (!isset($result[$bonusBrandId])) {
                $result[$bonusBrandId] = [
                    'brand_id' => $bonusBrandId,
                    'brand_name' => $product->brand->name,

                    /*
                     * Общее количество бонусов этой группы.
                     */
                    'quantity' => $quantity,

                    'products' => [],
                ];
            } else {
                /*
                 * Если в одной группе несколько бонусных товаров,
                 * не увеличиваем количество бонусов.
                 *
                 * Берём максимальное доступное количество.
                 */
                $result[$bonusBrandId]['quantity'] = max(
                    $result[$bonusBrandId]['quantity'],
                    $quantity
                );
            }

            /*
             * Добавляем бонусный товар в группу.
             */
            $result[$bonusBrandId]['products'][] = [
                'bonus_product_id' => $bonusProduct->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'stock' => (int) $bonusProduct->stock,
                'threshold' => $threshold,
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

       /* dd(
            $bonusProducts
                ->filter(fn ($bonusProduct) =>
                    (int) ($bonusProduct->product?->brand_id ?? 0) === 1
                )
                ->map(function ($bonusProduct) {
                    return [
                        'bonus_product_id' => $bonusProduct->id,
                        'product' => $bonusProduct->product?->name,
                        'brand' => $bonusProduct->product?->brand?->name,
                        'brand_id' => $bonusProduct->product?->brand_id,
                        'threshold' => $bonusProduct->threshold,
                        'stock' => $bonusProduct->stock,
                    ];
                })
                ->values()
                ->toArray()
        );*/

        return [
            'order_total' => $orderTotal,

            /*
             * ID брендов, которые покупались.
             */
            'purchased_brand_ids' => $purchasedBrandIds,

            /*
             * Сумма покупок по каждому бренду.
             *
             * Это также удобно для проверки и отладки.
             */
            'purchased_brand_totals' => $purchasedBrandTotals,

            /*
             * Доступные бонусы.
             */
            'groups' => array_values($result),
        ];
    }
}
