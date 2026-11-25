<?php

namespace Tests\Feature;

use App\Livewire\AddToCart;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class AddToCartQuantitySyncTest extends TestCase
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

    public function test_no_variant_is_active_by_default(): void
    {
        [$product, $variant] = $this->createProductWithVariant();

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->assertSet('selectedVariantId', null)
            ->assertSet('addedToCart', false)
            ->assertDontSeeHtml('active"')
            ->call('selectVariant', $variant->id)
            ->assertSet('selectedVariantId', $variant->id)
            ->assertSeeHtml('active"');
    }

    public function test_empty_cart_shows_only_add_button_without_quantity_block(): void
    {
        [$product] = $this->createProductWithVariant();

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->assertSet('quantity', 1)
            ->assertSet('addedToCart', false)
            ->assertSee('В корзину')
            ->assertDontSee('В корзине')
            ->assertDontSeeHtml('class="quantity"');
    }

    public function test_add_to_cart_adds_one_and_shows_quantity_with_in_cart_link(): void
    {
        [$product, $variant] = $this->createProductWithVariant(stock: 10);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->call('selectVariant', $variant->id)
            ->call('addToCart')
            ->assertSet('quantity', 1)
            ->assertSet('addedToCart', true)
            ->assertSee('В корзине')
            ->assertSeeHtml('class="quantity"')
            ->assertSeeHtml('href="'.route('cart.index').'"')
            ->assertDontSee('В корзину');

        $this->assertSame(1, session('cart')[$variant->id]['quantity']);
    }

    public function test_increment_updates_cart_immediately(): void
    {
        [$product, $variant] = $this->createProductWithVariant(stock: 10);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->call('selectVariant', $variant->id)
            ->call('addToCart')
            ->call('increment')
            ->call('increment')
            ->assertSet('quantity', 3)
            ->assertSet('addedToCart', true);

        $this->assertSame(3, session('cart')[$variant->id]['quantity']);
    }

    public function test_decrement_updates_cart_and_removing_last_unit_hides_quantity(): void
    {
        [$product, $variant] = $this->createProductWithVariant(stock: 10);

        app(CartService::class)->add($variant, 3);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->call('selectVariant', $variant->id)
            ->assertSet('quantity', 3)
            ->call('decrement')
            ->assertSet('quantity', 2)
            ->call('decrement')
            ->assertSet('quantity', 1)
            ->call('decrement')
            ->assertSet('quantity', 1)
            ->assertSet('addedToCart', false)
            ->assertSee('В корзину')
            ->assertDontSee('В корзине')
            ->assertDontSeeHtml('class="quantity"');

        $this->assertArrayNotHasKey($variant->id, session('cart', []));
    }

    public function test_readd_after_removal_starts_from_one(): void
    {
        [$product, $variant] = $this->createProductWithVariant(stock: 10);

        app(CartService::class)->add($variant, 1);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->call('selectVariant', $variant->id)
            ->call('decrement')
            ->assertSet('addedToCart', false)
            ->call('addToCart')
            ->assertSet('quantity', 1)
            ->assertSet('addedToCart', true)
            ->assertSee('В корзине');

        $this->assertSame(1, session('cart')[$variant->id]['quantity']);
    }

    public function test_state_restores_from_cart_after_variant_reselected(): void
    {
        [$product, $variant] = $this->createProductWithVariant(stock: 10);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->call('selectVariant', $variant->id)
            ->call('addToCart')
            ->call('increment')
            ->call('increment')
            ->assertSet('quantity', 3);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->assertSet('selectedVariantId', null)
            ->assertSet('addedToCart', false)
            ->call('selectVariant', $variant->id)
            ->assertSet('quantity', 3)
            ->assertSet('addedToCart', true)
            ->assertSee('В корзине')
            ->assertSeeHtml('class="quantity"')
            ->assertDontSee('В корзину');

        $this->assertSame(3, session('cart')[$variant->id]['quantity']);
    }

    public function test_quantity_hides_after_external_cart_removal(): void
    {
        [$product, $variant] = $this->createProductWithVariant(stock: 10);

        app(CartService::class)->add($variant, 2);

        $component = Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->call('selectVariant', $variant->id)
            ->assertSet('addedToCart', true)
            ->assertSeeHtml('class="quantity"');

        app(CartService::class)->remove($variant->id);

        $component
            ->call('refreshQuantity')
            ->assertSet('quantity', 1)
            ->assertSet('addedToCart', false)
            ->assertSee('В корзину')
            ->assertDontSeeHtml('class="quantity"');
    }

    public function test_zero_stock_variant_can_be_ordered(): void
    {
        [$product, $variant] = $this->createProductWithVariant(stock: 0);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->assertSet('selectedVariantId', null)
            ->call('selectVariant', $variant->id)
            ->assertSet('selectedVariantId', $variant->id)
            ->assertSee('В корзину')
            ->call('addToCart')
            ->assertSet('addedToCart', true)
            ->assertSet('quantity', 1)
            ->call('increment')
            ->assertSet('quantity', 2);

        $this->assertSame(2, session('cart')[$variant->id]['quantity']);
    }

    public function test_quantity_block_follows_selected_variant_cart_state(): void
    {
        $brand = Brand::query()->create(['name' => 'Variant Switch Farm '.uniqid()]);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Variant Switch Strain '.uniqid(),
            'price' => 1000,
            'seed_type' => 'F',
        ]);
        $variant5 = ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'VS5-'.uniqid(),
            'package_size' => 5,
            'price' => 4200,
            'stock' => 0,
        ]);
        $variant10 = ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'VS10-'.uniqid(),
            'package_size' => 10,
            'price' => 4300,
            'stock' => 0,
        ]);

        app(CartService::class)->add($variant10, 2);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->assertSet('selectedVariantId', null)
            ->call('selectVariant', $variant10->id)
            ->assertSet('addedToCart', true)
            ->assertSet('quantity', 2)
            ->assertSeeHtml('class="quantity"')
            ->assertSee('В корзине')
            ->call('selectVariant', $variant5->id)
            ->assertSet('addedToCart', false)
            ->assertSet('quantity', 1)
            ->assertDontSeeHtml('class="quantity"')
            ->assertSee('В корзину')
            ->call('selectVariant', $variant10->id)
            ->assertSet('addedToCart', true)
            ->assertSet('quantity', 2)
            ->assertSeeHtml('class="quantity"')
            ->assertSee('В корзине');
    }

    /**
     * @return array{0: Product, 1: ProductVariant}
     */
    private function createProductWithVariant(int $stock = 10): array
    {
        $brand = Brand::query()->create(['name' => 'Qty Sync Farm '.uniqid()]);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Qty Sync Strain '.uniqid(),
            'price' => 1000,
            'seed_type' => 'F',
        ]);
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'sku' => 'QTY-'.uniqid(),
            'package_size' => 5,
            'price' => 1000,
            'stock' => $stock,
        ]);

        return [$product->fresh('variants'), $variant->fresh()];
    }
}
