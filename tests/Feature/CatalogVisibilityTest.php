<?php

namespace Tests\Feature;

use App\Livewire\MobileProductSearch;
use App\Livewire\ProductSearch;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogVisibilityTest extends TestCase
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

    public function test_public_catalog_requires_visibility_flag_and_price(): void
    {
        $brand = Brand::query()->create(['name' => 'Visibility Farm']);
        $listed = $this->product($brand, 'Listed Catalog Strain', 1000, true);
        $noPrice = $this->product($brand, 'No Price Catalog Strain', null, true);
        $hidden = $this->product($brand, 'Hidden Catalog Strain', 1000, false);
        $hiddenWithoutPrice = $this->product($brand, 'Hidden No Price Catalog Strain', null, false);

        $this->assertSame(
            [$listed->id],
            Product::visibleInCatalog()->whereIn('id', [
                $listed->id,
                $noPrice->id,
                $hidden->id,
                $hiddenWithoutPrice->id,
            ])->orderBy('id')->pluck('id')->all()
        );

        foreach ([$noPrice, $hidden, $hiddenWithoutPrice] as $product) {
            $this->assertNotNull(Product::query()->find($product->id));
            $this->get(route('product.show', $product->slug))->assertNotFound();
        }

        $this->get(route('product.show', $listed->slug))->assertOk();

        Livewire::test(ProductSearch::class)
            ->set('search', 'Catalog Strain')
            ->assertSee('Listed Catalog Strain')
            ->assertDontSee('No Price Catalog Strain')
            ->assertDontSee('Hidden Catalog Strain')
            ->assertDontSee('Hidden No Price Catalog Strain');

        Livewire::test(MobileProductSearch::class)
            ->set('search', 'Catalog Strain')
            ->assertSee('Listed Catalog Strain')
            ->assertDontSee('No Price Catalog Strain')
            ->assertDontSee('Hidden Catalog Strain')
            ->assertDontSee('Hidden No Price Catalog Strain');
    }

    public function test_admin_catalog_column_shows_the_actual_listing_state(): void
    {
        $brand = Brand::query()->create(['name' => 'Listing Status Farm']);
        $listed = $this->product($brand, 'Status Listed', 1000, true);
        $noPrice = $this->product($brand, 'Status No Price', null, true);
        $hidden = $this->product($brand, 'Status Hidden', 1000, false);
        $hiddenWithoutPrice = $this->product($brand, 'Status Hidden No Price', null, false);

        $this->assertTrue($listed->isListedInCatalog());
        $this->assertSame('В каталоге', $listed->catalogListingLabel());

        $this->assertFalse($noPrice->isListedInCatalog());
        $this->assertSame('Нет цены', $noPrice->catalogListingLabel());

        $this->assertFalse($hidden->isListedInCatalog());
        $this->assertSame('Скрыт вручную', $hidden->catalogListingLabel());

        $this->assertFalse($hiddenWithoutPrice->isListedInCatalog());
        $this->assertSame('Скрыт вручную, нет цены', $hiddenWithoutPrice->catalogListingLabel());
    }

    private function product(Brand $brand, string $name, mixed $price, bool $visible): Product
    {
        return Product::query()->create([
            'brand_id' => $brand->id,
            'name' => $name,
            'price' => $price,
            'seed_type' => 'F',
            'is_visible' => $visible,
        ]);
    }
}
