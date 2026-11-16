<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ListTrashedProducts;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductTrashPageTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());
    }

    public function test_products_list_has_trash_button_with_count(): void
    {
        $brand = Brand::query()->create(['name' => 'Trash Button Brand '.uniqid()]);

        $active = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Active For Trash Button '.uniqid(),
            'slug' => 'active-trash-button-'.uniqid(),
            'price' => 100,
            'is_visible' => true,
        ]);

        $trashed = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Trashed For Button '.uniqid(),
            'slug' => 'trashed-for-button-'.uniqid(),
            'price' => 200,
            'is_visible' => true,
        ]);
        $trashed->delete();

        $trashUrl = ProductResource::getUrl('trash');

        Livewire::test(ListProducts::class)
            ->assertActionExists('trash')
            ->assertActionHasLabel('trash', 'Корзина ('.Product::onlyTrashed()->count().')')
            ->assertActionHasUrl('trash', $trashUrl)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$trashed]);
    }

    public function test_trash_page_lists_only_soft_deleted_products_with_deleted_at(): void
    {
        $brand = Brand::query()->create(['name' => 'Trash Page Brand '.uniqid()]);
        $category = Category::query()->create([
            'name' => 'Trash Page Category '.uniqid(),
            'slug' => 'trash-page-category-'.uniqid(),
        ]);

        $active = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Active On Trash Page '.uniqid(),
            'slug' => 'active-on-trash-page-'.uniqid(),
            'price' => 100,
            'is_visible' => true,
        ]);

        $trashed = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Visible In Trash '.uniqid(),
            'slug' => 'visible-in-trash-'.uniqid(),
            'price' => 300,
            'is_visible' => true,
        ]);
        $trashed->categories()->attach($category->id);
        $trashed->delete();
        $trashed->refresh();

        $this->assertNotNull($trashed->deleted_at);

        Livewire::test(ListTrashedProducts::class)
            ->assertSee('Корзина товаров')
            ->assertCanSeeTableRecords([$trashed])
            ->assertCanNotSeeTableRecords([$active])
            ->assertSee($trashed->name)
            ->assertSee($brand->name)
            ->assertSee($trashed->deleted_at->format('d.m.Y H:i'));
    }

    public function test_single_restore_returns_product_to_main_list_with_photos(): void
    {
        Storage::fake('public');

        $brand = Brand::query()->create(['name' => 'Single Restore Brand '.uniqid()]);
        $mainPath = 'images/products/trash-restore-'.uniqid().'.jpg';
        Storage::disk('public')->put($mainPath, 'photo');

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Single Restore Product '.uniqid(),
            'slug' => 'single-restore-'.uniqid(),
            'price' => 450,
            'is_visible' => true,
            'image' => $mainPath,
            'seo_title' => 'Keep SEO',
        ]);
        $slug = $product->slug;

        $product->delete();

        Livewire::test(ListTrashedProducts::class)
            ->callTableAction('restore', $product)
            ->assertCanNotSeeTableRecords([$product]);

        $restored = Product::query()->findOrFail($product->id);

        $this->assertNull($restored->deleted_at);
        $this->assertSame($slug, $restored->slug);
        $this->assertSame('Keep SEO', $restored->seo_title);
        $this->assertSame($mainPath, $restored->image);
        Storage::disk('public')->assertExists($mainPath);

        Livewire::test(ListProducts::class)
            ->assertCanSeeTableRecords([$restored]);
    }

    public function test_bulk_restore_restores_selected_products_and_updates_count(): void
    {
        $brand = Brand::query()->create(['name' => 'Bulk Restore Brand '.uniqid()]);

        $first = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Bulk Restore First '.uniqid(),
            'slug' => 'bulk-restore-first-'.uniqid(),
            'price' => 100,
            'is_visible' => true,
        ]);
        $second = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Bulk Restore Second '.uniqid(),
            'slug' => 'bulk-restore-second-'.uniqid(),
            'price' => 200,
            'is_visible' => true,
        ]);

        $first->delete();
        $second->delete();

        $countBefore = Product::onlyTrashed()->count();

        Livewire::test(ListTrashedProducts::class)
            ->callTableBulkAction('restore', [$first, $second])
            ->assertCanNotSeeTableRecords([$first, $second]);

        $this->assertNull(Product::query()->findOrFail($first->id)->deleted_at);
        $this->assertNull(Product::query()->findOrFail($second->id)->deleted_at);
        $this->assertSame($countBefore - 2, Product::onlyTrashed()->count());

        Livewire::test(ListProducts::class)
            ->assertActionHasLabel('trash', 'Корзина ('.Product::onlyTrashed()->count().')')
            ->assertCanSeeTableRecords([$first->fresh(), $second->fresh()]);
    }

    public function test_trash_route_is_registered_under_products(): void
    {
        $this->assertSame(
            url('/admin/products/trash'),
            ProductResource::getUrl('trash')
        );
    }
}
