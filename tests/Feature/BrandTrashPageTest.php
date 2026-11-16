<?php

namespace Tests\Feature;

use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Filament\Resources\Brands\Pages\ListTrashedBrands;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BrandTrashPageTest extends TestCase
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

    public function test_brands_list_has_trash_button_with_count(): void
    {
        $active = Brand::query()->create([
            'name' => 'Active Brand Button '.uniqid(),
            'slug' => 'active-brand-button-'.uniqid(),
        ]);

        $trashed = Brand::query()->create([
            'name' => 'Trashed Brand Button '.uniqid(),
            'slug' => 'trashed-brand-button-'.uniqid(),
        ]);
        $trashed->delete();

        Livewire::test(ListBrands::class)
            ->assertActionExists('trash')
            ->assertActionHasLabel('trash', 'Корзина ('.Brand::onlyTrashed()->count().')')
            ->assertActionHasUrl('trash', BrandResource::getUrl('trash'))
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$trashed]);
    }

    public function test_trash_page_lists_only_soft_deleted_brands_with_deleted_at(): void
    {
        $active = Brand::query()->create([
            'name' => 'Active On Brand Trash '.uniqid(),
            'slug' => 'active-on-brand-trash-'.uniqid(),
        ]);

        $trashed = Brand::query()->create([
            'name' => 'Visible Brand In Trash '.uniqid(),
            'slug' => 'visible-brand-in-trash-'.uniqid(),
        ]);
        $trashed->delete();
        $trashed->refresh();

        $this->assertNotNull($trashed->deleted_at);

        Livewire::test(ListTrashedBrands::class)
            ->assertSee('Корзина брендов')
            ->assertCanSeeTableRecords([$trashed])
            ->assertCanNotSeeTableRecords([$active])
            ->assertSee($trashed->name)
            ->assertSee($trashed->slug)
            ->assertSee($trashed->deleted_at->format('d.m.Y H:i'));
    }

    public function test_single_restore_returns_brand_to_main_list_with_logo(): void
    {
        Storage::fake('public');

        $logo = 'images/brands/trash-restore-'.uniqid().'.png';
        Storage::disk('public')->put($logo, 'logo');

        $brand = Brand::query()->create([
            'name' => 'Single Restore Brand Page '.uniqid(),
            'slug' => 'single-restore-brand-page-'.uniqid(),
            'logo' => $logo,
            'seo_title' => 'Keep Brand SEO',
            'sort_order' => 15,
        ]);

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Linked Product '.uniqid(),
            'slug' => 'linked-product-'.uniqid(),
            'price' => 120,
            'is_visible' => true,
        ]);

        $slug = $brand->slug;
        $brand->delete();

        Livewire::test(ListTrashedBrands::class)
            ->callTableAction('restore', $brand)
            ->assertCanNotSeeTableRecords([$brand]);

        $restored = Brand::query()->findOrFail($brand->id);

        $this->assertNull($restored->deleted_at);
        $this->assertSame($slug, $restored->slug);
        $this->assertSame($logo, $restored->logo);
        $this->assertSame('Keep Brand SEO', $restored->seo_title);
        $this->assertSame(15, $restored->sort_order);
        $this->assertSame($brand->id, $product->fresh()->brand_id);
        Storage::disk('public')->assertExists($logo);

        Livewire::test(ListBrands::class)
            ->assertCanSeeTableRecords([$restored]);
    }

    public function test_bulk_restore_restores_selected_brands_and_updates_count(): void
    {
        $first = Brand::query()->create([
            'name' => 'Bulk Brand First '.uniqid(),
            'slug' => 'bulk-brand-first-'.uniqid(),
        ]);
        $second = Brand::query()->create([
            'name' => 'Bulk Brand Second '.uniqid(),
            'slug' => 'bulk-brand-second-'.uniqid(),
        ]);

        $first->delete();
        $second->delete();

        $countBefore = Brand::onlyTrashed()->count();

        Livewire::test(ListTrashedBrands::class)
            ->callTableBulkAction('restore', [$first, $second])
            ->assertCanNotSeeTableRecords([$first, $second]);

        $this->assertNull(Brand::query()->findOrFail($first->id)->deleted_at);
        $this->assertNull(Brand::query()->findOrFail($second->id)->deleted_at);
        $this->assertSame($countBefore - 2, Brand::onlyTrashed()->count());

        Livewire::test(ListBrands::class)
            ->assertActionHasLabel('trash', 'Корзина ('.Brand::onlyTrashed()->count().')')
            ->assertCanSeeTableRecords([$first->fresh(), $second->fresh()]);
    }

    public function test_trash_route_is_registered_under_brands(): void
    {
        $this->assertSame(
            url('/admin/brands/trash'),
            BrandResource::getUrl('trash')
        );
    }
}
