<?php

namespace Tests\Feature;

use App\Livewire\CatalogFilter;
use App\Models\Brand;
use App\Models\BrandSubcategory;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class BrandSubcategoryTest extends TestCase
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

    public function test_general_category_lists_products_from_different_brands(): void
    {
        $first = Brand::query()->create(['name' => 'General Brand One']);
        $second = Brand::query()->create(['name' => 'General Brand Two']);
        $category = Category::query()->create(['name' => 'General Shared Category']);

        $one = $this->product($first, 'General Shared One', 'F');
        $two = $this->product($second, 'General Shared Two', 'R');
        $one->categories()->attach($category->id);
        $two->categories()->attach($category->id);

        $this->get(route('catalog.category', $category->slug))
            ->assertOk()
            ->assertSee('General Shared One')
            ->assertSee('General Shared Two');

        Livewire::withQueryParams(['seed_types' => ['R']])
            ->test(CatalogFilter::class)
            ->assertSee('General Shared Two')
            ->assertDontSee('General Shared One');
    }

    public function test_brand_page_lists_only_its_products_and_assigned_subcategories(): void
    {
        $brand = Brand::query()->create(['name' => 'Menu Brand']);
        $other = Brand::query()->create(['name' => 'Other Menu Brand']);
        $this->product($brand, 'Menu Brand Strain', 'F');
        $this->product($other, 'Other Menu Strain', 'F');

        BrandSubcategory::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Second Subcategory',
            'slug' => 'second-sub',
            'seed_type' => 'A',
            'sort_order' => 2,
            'is_published' => true,
        ]);
        BrandSubcategory::query()->create([
            'brand_id' => $brand->id,
            'name' => 'First Subcategory',
            'slug' => 'first-sub',
            'seed_type' => 'F',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        BrandSubcategory::query()->create([
            'brand_id' => $other->id,
            'name' => 'Foreign Subcategory',
            'slug' => 'foreign-sub',
            'seed_type' => 'F',
            'is_published' => true,
        ]);

        $category = Category::query()->create(['name' => 'Menu Brand General Category']);

        $response = $this->get(route('catalog.brand', $brand))
            ->assertOk()
            ->assertSee('Menu Brand Strain')
            ->assertDontSee('Other Menu Strain')
            ->assertDontSee('Foreign Subcategory')
            ->assertSeeInOrder(['First Subcategory', 'Second Subcategory'])
            ->assertSee('<link rel="canonical" href="'.route('catalog.brand', $brand).'">', false);

        $buttons = $this->catalogButtons($response->getContent());
        $this->assertStringContainsString('First Subcategory', $buttons);
        $this->assertStringContainsString('Second Subcategory', $buttons);
        $this->assertStringNotContainsString($category->name, $buttons);
        $this->assertStringNotContainsString('Foreign Subcategory', $buttons);
    }

    public function test_subcategory_page_keeps_brand_and_type_constraints(): void
    {
        $brand = Brand::query()->create(['name' => 'Scoped Brand']);
        $other = Brand::query()->create(['name' => 'Outside Brand']);
        $page = BrandSubcategory::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Feminized Scoped',
            'slug' => 'feminized',
            'h1' => 'Feminized Scoped H1',
            'seo_title' => 'Feminized Scoped Title',
            'seo_description' => 'Feminized scoped description',
            'seo_text' => 'Feminized scoped seo text',
            'seed_type' => 'F',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $match = $this->product($brand, 'Scoped Filtertoken Feminised', 'F');
        $sameBrandOtherType = $this->product($brand, 'Scoped Auto Strain', 'A');
        $otherBrand = $this->product($other, 'Scoped Filtertoken Foreign', 'F');

        $response = $this->get(route('catalog.brand.subcategory', ['brand' => $brand, 'subcategory' => 'feminized']))
            ->assertOk()
            ->assertSee('Feminized Scoped Title')
            ->assertSee('Feminized scoped description')
            ->assertSee('Feminized Scoped H1')
            ->assertSee('Feminized scoped seo text')
            ->assertSee('href="'.route('catalog.brand.subcategory', ['brand' => $brand, 'subcategory' => 'feminized']).'"', false)
            ->assertSee('<link rel="canonical" href="'.route('catalog.brand.subcategory', ['brand' => $brand, 'subcategory' => 'feminized']).'">', false)
            ->assertDontSee('Scoped Auto Strain')
            ->assertDontSee('Scoped Filtertoken Foreign');

        $this->assertStringContainsString('Feminized Scoped', $this->catalogButtons($response->getContent()));

        for ($index = 1; $index <= 24; $index++) {
            $this->product($brand, sprintf('Scoped Page Strain %02d', $index), 'F');
        }

        Livewire::test(CatalogFilter::class, [
            'brand' => $brand,
            'brandSubcategory' => $page,
            'catalogInfo' => $brand,
        ])
            ->assertSee('Scoped Filtertoken Feminised')
            ->assertDontSee('Scoped Auto Strain')
            ->assertDontSee('Scoped Filtertoken Foreign');

        Livewire::withQueryParams(['search' => 'Filtertoken'])
            ->test(CatalogFilter::class, [
                'brand' => $brand,
                'brandSubcategory' => $page,
                'catalogInfo' => $brand,
            ])
            ->assertSee('Scoped Filtertoken Feminised')
            ->assertDontSee('Scoped Page Strain 01')
            ->assertDontSee('Scoped Filtertoken Foreign');

        Livewire::withQueryParams(['sort' => 'name_asc'])
            ->test(CatalogFilter::class, [
                'brand' => $brand,
                'brandSubcategory' => $page,
                'catalogInfo' => $brand,
            ])
            ->assertSeeInOrder(['Scoped Filtertoken Feminised', 'Scoped Page Strain 01']);

        Livewire::withQueryParams(['sort' => 'name_asc', 'page' => 2])
            ->test(CatalogFilter::class, [
                'brand' => $brand,
                'brandSubcategory' => $page,
                'catalogInfo' => $brand,
            ])
            ->assertSee('Scoped Page Strain 24')
            ->assertDontSee($match->name)
            ->assertDontSee($sameBrandOtherType->name)
            ->assertDontSee($otherBrand->name);
    }

    public function test_f1_subcategory_uses_the_name_token_and_not_every_feminised_product(): void
    {
        $brand = Brand::query()->create(['name' => 'Hybrid Brand']);
        $page = BrandSubcategory::query()->create([
            'brand_id' => $brand->id,
            'name' => 'F1 Hybrids',
            'slug' => 'f1-hybrids',
            'name_token' => 'F1',
            'is_published' => true,
        ]);

        $this->product($brand, 'Banana Runtz F1 Feminised', 'F');
        $this->product($brand, 'Plain Feminised Strain', 'F');

        Livewire::test(CatalogFilter::class, [
            'brand' => $brand,
            'brandSubcategory' => $page,
            'catalogInfo' => $brand,
        ])
            ->assertSee('Banana Runtz F1 Feminised')
            ->assertDontSee('Plain Feminised Strain');
    }

    public function test_brand_without_subcategories_stays_available(): void
    {
        $brand = Brand::query()->create(['name' => 'Empty Menu Brand']);
        $this->product($brand, 'Empty Menu Strain', 'F');

        $response = $this->get(route('catalog.brand', $brand))
            ->assertOk()
            ->assertSee('Empty Menu Strain');

        $this->assertSame('', $this->catalogButtons($response->getContent()));
    }

    public function test_brand_with_only_unpublished_subcategories_has_no_menu(): void
    {
        $brand = Brand::query()->create(['name' => 'Unpublished Menu Brand']);
        $this->product($brand, 'Unpublished Menu Strain', 'F');
        BrandSubcategory::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Unpublished Menu Subcategory',
            'slug' => 'unpublished-menu',
            'seed_type' => 'F',
            'is_published' => false,
        ]);

        $response = $this->get(route('catalog.brand', $brand))
            ->assertOk()
            ->assertSee('Unpublished Menu Strain')
            ->assertDontSee('Unpublished Menu Subcategory');

        $this->assertSame('', $this->catalogButtons($response->getContent()));
    }

    public function test_general_catalog_keeps_its_menu_and_canonical(): void
    {
        $category = Category::query()->create(['name' => 'Canonical Shared Category']);

        $index = $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('catalog.index').'">', false);

        $indexButtons = $this->catalogButtons($index->getContent());
        $this->assertStringContainsString('Все товары', $indexButtons);
        $this->assertStringContainsString($category->name, $indexButtons);
        $this->assertStringContainsString(route('catalog.index'), $indexButtons);

        $categoryPage = $this->get(route('catalog.category', $category->slug))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('catalog.category', $category->slug).'">', false);

        $categoryButtons = $this->catalogButtons($categoryPage->getContent());
        $this->assertStringContainsString('Все товары', $categoryButtons);
        $this->assertStringContainsString($category->name, $categoryButtons);
        $this->assertStringContainsString(route('catalog.category', $category->slug), $categoryButtons);
    }

    public function test_unpublished_and_missing_subcategories_are_not_found(): void
    {
        $brand = Brand::query()->create(['name' => 'Hidden Page Brand']);
        BrandSubcategory::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Hidden Page',
            'slug' => 'hidden-page',
            'seed_type' => 'F',
            'is_published' => false,
        ]);

        $this->get(route('catalog.brand.subcategory', ['brand' => $brand, 'subcategory' => 'hidden-page']))
            ->assertNotFound();

        $this->get(route('catalog.brand.subcategory', ['brand' => $brand, 'subcategory' => 'missing-page']))
            ->assertNotFound();

        $this->get('/brands/missing-brand-slug')->assertNotFound();
    }

    public function test_product_import_does_not_require_a_subcategory(): void
    {
        $service = app(ProductImportService::class);
        $brandName = 'Import Without Subcategory '.uniqid();

        $product = $service->import([
            'strain' => 'Import Strain',
            'brand' => $brandName,
            'seed_type' => 'F',
            'price' => 1000,
            'variants' => [[
                'package_size' => 5,
                'sku' => 'subcat-import-'.uniqid(),
                'price' => 1000,
                'stock' => 3,
            ]],
        ]);

        $this->assertNotNull($product->id);
        $this->assertSame('F', $product->seed_type);
        $this->assertSame(0, BrandSubcategory::query()->where('brand_id', $product->brand_id)->count());
    }

    private function catalogButtons(string $html): string
    {
        if (! preg_match('/<div class="catalog-category-buttons">(.*?)<\/div>/s', $html, $matches)) {
            return '';
        }

        return $matches[1];
    }

    private function product(Brand $brand, string $name, string $seedType): Product
    {
        return Product::query()->create([
            'brand_id' => $brand->id,
            'name' => $name,
            'price' => 1500,
            'seed_type' => $seedType,
            'thc' => '20',
            'flowering' => '60',
            'height' => '100',
            'is_visible' => true,
        ]);
    }
}
