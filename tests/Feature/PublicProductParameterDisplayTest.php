<?php

namespace Tests\Feature;

use App\Livewire\AddToCart;
use App\Livewire\ProductSearch;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class PublicProductParameterDisplayTest extends TestCase
{
    use DatabaseTransactions;

    public function createApplication()
    {
        $app = parent::createApplication();

        $mysql = $app['config']->get('database.connections.mysql');
        $mysql['url'] = null;
        $mysql['host'] = 'MySQL-8.0';
        $mysql['port'] = '3306';
        $mysql['database'] = '64trava_testing';
        $mysql['username'] = 'root';
        $mysql['password'] = '';

        $app['config']->set('database.connections.mysql', $mysql);
        $app['config']->set('database.default', 'mysql');

        return $app;
    }

    public function test_filled_parameters_stay_in_the_product_page_and_card(): void
    {
        [$product] = $this->catalogProduct(filled: true);

        $page = $this->get(route('product.show', $product->slug));
        $page->assertOk();
        $html = $page->getContent();

        $this->assertStringContainsString('product-detail-brand', $html);
        $this->assertStringContainsString('product-detail-old-price', $html);
        $this->assertStringContainsString('product-detail-current-price', $html);
        $this->assertStringContainsString('product-detail-features', $html);
        $this->assertStringContainsString('ГЕНЕТИКА', $html);
        $this->assertStringContainsString('ТГK', $html);
        $this->assertStringContainsString('ТИП ЦВЕТЕНИЯ', $html);
        $this->assertStringContainsString('ГЕНОТИП', $html);
        $this->assertStringContainsString('АРОМАТ', $html);
        $this->assertStringContainsString('ХАРВЕРСТ', $html);
        $this->assertStringContainsString('ВЫСОТА В ПОМЕЩЕНИИ', $html);
        $this->assertStringContainsString('УРОЖАЙНОСТЬ В ПОМЕШЕНИИ', $html);
        $this->assertStringContainsString('ВЫСОТА НА УЛИЦЕ', $html);
        $this->assertStringContainsString('УРОЖАЙНОСТЬ НА УЛИЦЕ', $html);
        $this->assertStringContainsString('product-pack-price-old-price', $html);
        $this->assertStringContainsString('product-pack-old-price', $html);

        $card = view('components.product-card', ['product' => $product->fresh('brand')])->render();
        $this->assertStringContainsString('product-info', $card);
        $this->assertStringContainsString('22', $card);
        $this->assertStringContainsString('Тип семян', $card);
        $this->assertStringContainsString('80-120', $card);
        $this->assertStringContainsString('Быстрый старт', $card);
    }

    public function test_missing_null_empty_and_zero_parameters_leave_no_markup(): void
    {
        [$product, $variant, $brand] = $this->catalogProduct(filled: false);

        foreach ([null, '', '0', 0] as $empty) {
            $numeric = $empty === '' ? null : $empty;

            $product->forceFill([
                'old_price' => $numeric,
                'cbd' => $numeric,
                'sativa_percent' => $numeric,
                'indica_percent' => $numeric,
                'rating' => ($empty === null || $empty === '') ? 0 : $empty,
                'thc' => $empty,
                'height' => $empty,
                'indoor_height' => $empty,
                'yield' => $empty,
                'outdoor_yield' => $empty,
                'flowering' => $empty,
                'genetics' => $empty,
                'effect' => $empty,
                'aroma' => $empty,
                'harvest' => $empty,
                'description' => $empty,
                'advantages' => $empty,
                'seed_type' => null,
            ])->save();

            $variant->forceFill(['old_price' => $numeric])->save();
            $brand->forceFill(['name' => ' '])->save();

            $product->refresh();
            $html = $this->get(route('product.show', $product->slug))->assertOk()->getContent();

            $this->assertStringNotContainsString('product-detail-old-price', $html, 'old price for '.var_export($empty, true));
            $this->assertStringNotContainsString('product-detail-brand', $html);
            $this->assertStringNotContainsString('product-detail-features', $html);
            $this->assertStringNotContainsString('product-detail-feature', $html);
            $this->assertStringNotContainsString('product-description', $html);
            $this->assertStringNotContainsString('product-detail-rating', $html);
            $this->assertStringNotContainsString('product-pack-price-old-price', $html);
            $this->assertStringNotContainsString('product-pack-old-price', $html);
            $this->assertStringContainsString('product-detail-current-price', $html);
            $this->assertStringContainsString('1500.00', $html);
            $this->assertStringNotContainsString('Цена не указана', $html);

            $card = view('components.product-card', ['product' => $product->fresh()])->render();
            $this->assertStringNotContainsString('product-info', $card, 'card info for '.var_export($empty, true));
            $this->assertStringNotContainsString('Тип семян', $card);
            $this->assertStringNotContainsString('Преимущества', $card);
        }

        $product->forceFill([
            'brand_id' => $brand->id,
            'price' => 1500,
        ])->save();
        $brand->forceFill(['name' => ''])->save();

        $search = Livewire::test(ProductSearch::class)
            ->set('search', $product->name)
            ->html();

        $this->assertStringNotContainsString('search-result-brand', $search);
        $this->assertStringContainsString('1500.00', $search);

        $packs = Livewire::test(AddToCart::class, ['product' => $product->fresh('variants'), 'type' => 'packs'])->html();
        $this->assertStringNotContainsString('product-pack-price-old-price', $packs);
        $this->assertStringNotContainsString('product-pack-old-price', $packs);
        $this->assertStringContainsString('product-pack', $packs);
        $this->assertStringContainsString('Количество семян в пачке', $packs);
    }

    /**
     * @return array{0: Product, 1: ProductVariant, 2: Brand}
     */
    private function catalogProduct(bool $filled): array
    {
        $suffix = $filled ? 'filled' : 'empty';
        $brand = Brand::query()->create([
            'name' => 'Param Farm '.$suffix.' '.uniqid(),
        ]);

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Param Strain '.$suffix.' '.uniqid(),
            'price' => 1500,
            'old_price' => $filled ? 1800 : null,
            'is_visible' => true,
            'seed_type' => $filled ? 'F' : null,
            'thc' => $filled ? '22' : null,
            'cbd' => $filled ? '1' : null,
            'height' => $filled ? '80-120' : null,
            'indoor_height' => $filled ? '70-90' : null,
            'yield' => $filled ? '500' : null,
            'outdoor_yield' => $filled ? '700' : null,
            'flowering' => $filled ? '8 недель' : null,
            'genetics' => $filled ? 'Gelato x OG' : null,
            'effect' => $filled ? 'Расслабление' : null,
            'aroma' => $filled ? 'Цитрус' : null,
            'harvest' => $filled ? 'Октябрь' : null,
            'sativa_percent' => $filled ? 60 : null,
            'indica_percent' => $filled ? 40 : null,
            'advantages' => $filled ? 'Быстрый старт' : null,
            'description' => $filled ? 'Короткое описание' : null,
            'rating' => $filled ? 4.8 : 0,
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'PARAM-'.$product->id,
            'package_size' => 3,
            'price' => 1500,
            'old_price' => $filled ? 1800 : null,
            'stock' => 5,
        ]);

        return [$product, $variant, $brand];
    }
}
