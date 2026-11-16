<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\BrandSubcategory;
use App\Models\Product;
use App\Models\User;
use App\Policies\BrandPolicy;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandSoftDeleteTest extends TestCase
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

    public function test_soft_delete_keeps_logo_products_and_relations(): void
    {
        Storage::fake('public');

        $logo = 'images/brands/soft-brand-'.uniqid().'.png';
        Storage::disk('public')->put($logo, 'logo-bytes');

        $brand = Brand::query()->create([
            'name' => 'Soft Brand '.uniqid(),
            'slug' => 'soft-brand-'.uniqid(),
            'logo' => $logo,
            'seo_title' => 'Brand SEO',
            'sort_order' => 42,
        ]);

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Brand Product '.uniqid(),
            'slug' => 'brand-product-'.uniqid(),
            'price' => 500,
            'is_visible' => true,
        ]);

        $subcategory = BrandSubcategory::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Sub '.uniqid(),
            'slug' => 'sub-'.uniqid(),
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $brandId = $brand->id;
        $slug = $brand->slug;
        $sortOrder = $brand->sort_order;

        $brand->delete();

        $this->assertSoftDeleted('brands', ['id' => $brandId]);
        $this->assertNull(Brand::query()->find($brandId));

        $trashed = Brand::withTrashed()->findOrFail($brandId);
        $this->assertSame($logo, $trashed->logo);
        $this->assertSame('Brand SEO', $trashed->seo_title);
        $this->assertSame($slug, $trashed->slug);
        $this->assertSame($sortOrder, $trashed->sort_order);

        Storage::disk('public')->assertExists($logo);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'brand_id' => $brandId,
        ]);
        $this->assertDatabaseHas('brand_subcategories', [
            'id' => $subcategory->id,
            'brand_id' => $brandId,
        ]);

        $this->assertSame($brandId, $product->fresh()->brand_id);
        $this->assertSame($brandId, $product->fresh()->brand?->id);
    }

    public function test_trashed_brands_are_hidden_from_public_queries(): void
    {
        $brand = Brand::query()->create([
            'name' => 'Public Hide Brand '.uniqid(),
            'slug' => 'public-hide-brand-'.uniqid(),
        ]);

        $brand->delete();

        $this->assertFalse(Brand::query()->whereKey($brand->id)->exists());
        $this->assertFalse(Brand::query()->ordered()->whereKey($brand->id)->exists());
        $this->assertNull(Brand::query()->where('slug', $brand->slug)->first());
    }

    public function test_restore_returns_brand_with_logo_and_product_links(): void
    {
        Storage::fake('public');

        $logo = 'images/brands/restore-brand-'.uniqid().'.png';
        Storage::disk('public')->put($logo, 'logo-bytes');

        $brand = Brand::query()->create([
            'name' => 'Restore Brand '.uniqid(),
            'slug' => 'restore-brand-'.uniqid(),
            'logo' => $logo,
            'seo_title' => 'Restore SEO',
            'sort_order' => 7,
        ]);

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Restore Brand Product '.uniqid(),
            'slug' => 'restore-brand-product-'.uniqid(),
            'price' => 700,
            'is_visible' => true,
        ]);

        $brand->delete();
        $brand->restore();

        $restored = Brand::query()->findOrFail($brand->id);

        $this->assertNull($restored->deleted_at);
        $this->assertSame($logo, $restored->logo);
        $this->assertSame('Restore SEO', $restored->seo_title);
        $this->assertSame(7, $restored->sort_order);
        $this->assertSame($brand->id, $product->fresh()->brand_id);

        Storage::disk('public')->assertExists($logo);
    }

    public function test_force_delete_is_denied_for_regular_admin(): void
    {
        $policy = new BrandPolicy();
        $admin = new User(['email' => 'admin@example.com']);
        $owner = new User(['email' => 'owner@example.com']);
        config(['auth.owner_emails' => ['owner@example.com']]);

        $brand = new Brand();

        $this->assertFalse($policy->forceDelete($admin, $brand));
        $this->assertFalse($policy->forceDeleteAny($admin));
        $this->assertTrue($policy->forceDelete($owner, $brand));
        $this->assertTrue($policy->forceDeleteAny($owner));
    }

    public function test_force_delete_removes_unreferenced_logo_and_blocks_when_products_exist(): void
    {
        Storage::fake('public');

        $shared = 'images/brands/shared-'.uniqid().'.png';
        $unique = 'images/brands/unique-'.uniqid().'.png';
        Storage::disk('public')->put($shared, 'shared');
        Storage::disk('public')->put($unique, 'unique');

        $keep = Brand::query()->create([
            'name' => 'Keep Logo Brand '.uniqid(),
            'slug' => 'keep-logo-brand-'.uniqid(),
            'logo' => $shared,
        ]);

        $empty = Brand::query()->create([
            'name' => 'Empty Force Brand '.uniqid(),
            'slug' => 'empty-force-brand-'.uniqid(),
            'logo' => $shared,
        ]);

        $withProduct = Brand::query()->create([
            'name' => 'With Product Brand '.uniqid(),
            'slug' => 'with-product-brand-'.uniqid(),
            'logo' => $unique,
        ]);

        Product::query()->create([
            'brand_id' => $withProduct->id,
            'name' => 'Protected Product '.uniqid(),
            'slug' => 'protected-product-'.uniqid(),
            'price' => 100,
            'is_visible' => true,
        ]);

        $withProduct->delete();

        try {
            $withProduct->forceDelete();
            $this->fail('Expected RuntimeException when force deleting brand with products');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('товары', $exception->getMessage());
        }

        $this->assertNotNull(Brand::withTrashed()->find($withProduct->id));
        Storage::disk('public')->assertExists($unique);

        $empty->delete();
        $empty->forceDelete();

        $this->assertDatabaseMissing('brands', ['id' => $empty->id]);
        $this->assertNotNull(Brand::query()->find($keep->id));
        Storage::disk('public')->assertExists($shared);
    }

    public function test_import_restores_trashed_brand_instead_of_duplicating_slug(): void
    {
        $brandName = 'Import Soft Brand '.uniqid();
        $brandSlug = \Illuminate\Support\Str::slug($brandName);

        $brand = Brand::query()->create([
            'name' => $brandName,
            'slug' => $brandSlug,
        ]);
        $brandId = $brand->id;
        $brand->delete();

        $service = app(ProductImportService::class);
        $product = $service->import([
            'strain' => 'Import Soft Strain '.uniqid(),
            'brand' => $brandName,
            'seed_type' => 'F',
            'price' => 1111,
            'variants' => [
                [
                    'package_size' => 3,
                    'sku' => 'BRAND-SOFT-'.uniqid(),
                    'price' => 1111,
                    'stock' => 2,
                ],
            ],
        ]);

        $this->assertSame($brandId, $product->brand_id);
        $this->assertNull(Brand::query()->findOrFail($brandId)->deleted_at);
        $this->assertSame(1, Brand::withTrashed()->where('slug', $brandSlug)->count());
    }
}
