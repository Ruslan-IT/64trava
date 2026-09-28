<?php

namespace Tests\Feature;

use App\Livewire\AddToCart;
use App\Livewire\ProductSearch;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class MissingProductPriceDisplayTest extends TestCase
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

    public function test_product_without_price_and_variants_shows_missing_price_and_cannot_be_added(): void
    {
        $brand = Brand::query()->create(['name' => 'No Price Farm']);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Null Price Display Strain',
            'price' => null,
            'seed_type' => 'F',
        ]);

        $this->assertNull($product->fresh()->price);
        $this->assertTrue($product->variants->isEmpty());

        Livewire::test(AddToCart::class, ['product' => $product])
            ->assertSee('Цена не указана')
            ->assertDontSee('0 р')
            ->assertDontSee('В корзину')
            ->call('addToCart')
            ->assertSet('addedToCart', false);

        $this->assertSame([], session('cart', []));

        Livewire::test(ProductSearch::class)
            ->set('search', 'Null Price Display')
            ->assertDontSee('Null Price Display Strain')
            ->assertDontSee('0 ₽');

        $this->assertNotNull(Product::query()->find($product->id));
        $this->get(route('product.show', $product->slug))->assertNotFound();
    }
}
