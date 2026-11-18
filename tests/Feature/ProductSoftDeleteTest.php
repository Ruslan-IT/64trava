<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Policies\ProductPolicy;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductSoftDeleteTest extends TestCase
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

    public function test_soft_delete_keeps_row_relations_and_image_files(): void
    {
        Storage::fake('public');

        $brand = Brand::query()->create(['name' => 'Soft Delete Brand '.uniqid()]);
        $category = Category::query()->create([
            'name' => 'Soft Delete Category '.uniqid(),
            'slug' => 'soft-delete-category-'.uniqid(),
        ]);

        $mainPath = 'images/products/soft-delete-main-'.uniqid().'.jpg';
        $galleryPath = 'images/products/gallery/soft-delete-gallery-'.uniqid().'.jpg';
        Storage::disk('public')->put($mainPath, 'main-image');
        Storage::disk('public')->put($galleryPath, 'gallery-image');

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Soft Delete Product '.uniqid(),
            'slug' => 'soft-delete-product-'.uniqid(),
            'price' => 1500,
            'is_visible' => true,
            'image' => $mainPath,
            'gallery' => [$galleryPath],
            'seo_title' => 'SEO title',
            'seo_description' => 'SEO description',
        ]);

        $product->categories()->attach($category->id);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'package_size' => 5,
            'sku' => 'SOFT-DEL-'.uniqid(),
            'price' => 1500,
            'stock' => 3,
        ]);

        $productId = $product->id;
        $slug = $product->slug;
        $seoTitle = $product->seo_title;

        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $productId]);
        $this->assertNull(Product::query()->find($productId));
        $this->assertNotNull(Product::withTrashed()->find($productId));

        $trashed = Product::withTrashed()->findOrFail($productId);
        $this->assertSame($mainPath, $trashed->image);
        $this->assertSame([$galleryPath], $trashed->gallery);
        $this->assertSame($seoTitle, $trashed->seo_title);
        $this->assertSame($slug, $trashed->slug);
        $this->assertTrue($trashed->categories()->whereKey($category->id)->exists());
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'product_id' => $productId,
        ]);

        Storage::disk('public')->assertExists($mainPath);
        Storage::disk('public')->assertExists($galleryPath);

        $this->assertFalse(
            Product::visibleInCatalog()->whereKey($productId)->exists()
        );
        $this->assertFalse(
            Product::query()->where('slug', $slug)->exists()
        );
    }

    public function test_restore_returns_product_with_photos_and_relations(): void
    {
        Storage::fake('public');

        $brand = Brand::query()->create(['name' => 'Restore Brand '.uniqid()]);
        $category = Category::query()->create([
            'name' => 'Restore Category '.uniqid(),
            'slug' => 'restore-category-'.uniqid(),
        ]);

        $mainPath = 'images/products/restore-main-'.uniqid().'.jpg';
        $galleryPath = 'images/products/gallery/restore-gallery-'.uniqid().'.jpg';
        Storage::disk('public')->put($mainPath, 'main-image');
        Storage::disk('public')->put($galleryPath, 'gallery-image');

        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Restore Product '.uniqid(),
            'slug' => 'restore-product-'.uniqid(),
            'price' => 2000,
            'is_visible' => true,
            'image' => $mainPath,
            'gallery' => [$galleryPath],
            'seo_title' => 'Restore SEO',
        ]);
        $product->categories()->attach($category->id);

        $product->delete();
        $product->restore();

        $restored = Product::query()->findOrFail($product->id);

        $this->assertNull($restored->deleted_at);
        $this->assertSame($mainPath, $restored->image);
        $this->assertSame([$galleryPath], $restored->gallery);
        $this->assertSame('Restore SEO', $restored->seo_title);
        $this->assertTrue($restored->categories()->whereKey($category->id)->exists());
        $this->assertTrue($restored->isListedInCatalog());

        Storage::disk('public')->assertExists($mainPath);
        Storage::disk('public')->assertExists($galleryPath);
    }

    public function test_bulk_restore_works_for_multiple_trashed_products(): void
    {
        $brand = Brand::query()->create(['name' => 'Bulk Restore Brand '.uniqid()]);

        $first = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Bulk One '.uniqid(),
            'slug' => 'bulk-one-'.uniqid(),
            'price' => 100,
            'is_visible' => true,
        ]);
        $second = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Bulk Two '.uniqid(),
            'slug' => 'bulk-two-'.uniqid(),
            'price' => 200,
            'is_visible' => true,
        ]);

        $first->delete();
        $second->delete();

        Product::onlyTrashed()
            ->whereIn('id', [$first->id, $second->id])
            ->restore();

        $this->assertDatabaseHas('products', [
            'id' => $first->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $second->id,
            'deleted_at' => null,
        ]);
    }

    public function test_force_delete_is_denied_for_regular_admin_and_allowed_for_owner(): void
    {
        $policy = new ProductPolicy();

        $admin = new User(['email' => 'admin@example.com']);
        $owner = new User(['email' => 'owner@example.com']);

        config(['auth.owner_emails' => ['owner@example.com']]);

        $product = new Product();

        $this->assertFalse($policy->forceDelete($admin, $product));
        $this->assertFalse($policy->forceDeleteAny($admin));
        $this->assertTrue($policy->forceDelete($owner, $product));
        $this->assertTrue($policy->forceDeleteAny($owner));
    }

    public function test_force_delete_removes_unreferenced_files_only(): void
    {
        Storage::fake('public');

        $brand = Brand::query()->create(['name' => 'Force Brand '.uniqid()]);

        $sharedPath = 'images/products/shared-'.uniqid().'.jpg';
        $uniquePath = 'images/products/unique-'.uniqid().'.jpg';
        Storage::disk('public')->put($sharedPath, 'shared');
        Storage::disk('public')->put($uniquePath, 'unique');

        $keep = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Keep Product '.uniqid(),
            'slug' => 'keep-product-'.uniqid(),
            'price' => 100,
            'image' => $sharedPath,
        ]);

        $remove = Product::query()->create([
            'brand_id' => $brand->id,
            'name' => 'Remove Product '.uniqid(),
            'slug' => 'remove-product-'.uniqid(),
            'price' => 100,
            'image' => $sharedPath,
            'gallery' => [$uniquePath],
        ]);

        $remove->forceDelete();

        $this->assertDatabaseMissing('products', ['id' => $remove->id]);
        $this->assertNotNull(Product::query()->find($keep->id));

        Storage::disk('public')->assertExists($sharedPath);
        Storage::disk('public')->assertMissing($uniquePath);
    }

    public function test_import_restores_trashed_product_instead_of_duplicating_slug(): void
    {
        $brandName = 'Import Soft Brand '.uniqid();
        $strain = 'Import Soft Strain '.uniqid();

        $service = app(ProductImportService::class);

        $product = $service->import([
            'strain' => $strain,
            'brand' => $brandName,
            'seed_type' => 'F',
            'price' => 1111,
            'variants' => [
                [
                    'package_size' => 3,
                    'sku' => 'IMP-SOFT-'.uniqid(),
                    'price' => 1111,
                    'stock' => 2,
                ],
            ],
        ]);

        $productId = $product->id;
        $slug = $product->slug;

        $product->delete();
        $this->assertSoftDeleted('products', ['id' => $productId]);

        $reimported = $service->import([
            'strain' => $strain,
            'brand' => $brandName,
            'seed_type' => 'F',
            'price' => 2222,
            'variants' => [
                [
                    'package_size' => 3,
                    'sku' => 'IMP-SOFT-'.uniqid(),
                    'price' => 2222,
                    'stock' => 5,
                ],
            ],
        ]);

        $this->assertSame($productId, $reimported->id);
        $this->assertNull($reimported->deleted_at);
        $this->assertSame($slug, $reimported->slug);
        $this->assertEquals(2222, (float) $reimported->price);
        $this->assertSame(1, Product::withTrashed()->where('slug', $slug)->count());
    }

    public function test_import_matches_trashed_product_by_sku_when_slug_differs(): void
    {
        $brandName = 'Sku Soft Brand '.uniqid();
        $service = app(ProductImportService::class);
        $sku = 'SKU-SOFT-MATCH-'.uniqid();

        $product = $service->import([
            'strain' => 'Original Soft Strain '.uniqid(),
            'brand' => $brandName,
            'seed_type' => 'F',
            'price' => 100,
            'variants' => [[
                'package_size' => 3,
                'sku' => $sku,
                'price' => 100,
                'stock' => 1,
            ]],
        ]);

        $productId = $product->id;
        $product->delete();

        $reimported = $service->import([
            'strain' => 'Renamed Soft Strain '.uniqid(),
            'brand' => $brandName,
            'seed_type' => 'A',
            'price' => 250,
            'variants' => [[
                'package_size' => 3,
                'sku' => $sku,
                'price' => 250,
                'stock' => 4,
            ]],
        ]);

        $this->assertSame($productId, $reimported->id);
        $this->assertNull($reimported->deleted_at);
        $this->assertEquals(250, (float) $reimported->price);
        $this->assertSame(1, ProductVariant::query()->where('sku', $sku)->count());
        $this->assertSame($productId, ProductVariant::query()->where('sku', $sku)->value('product_id'));
    }

    public function test_import_updates_active_product_matched_by_sku_instead_of_duplicating(): void
    {
        $brandName = 'Active Sku Brand '.uniqid();
        $service = app(ProductImportService::class);
        $sku = 'SKU-ACTIVE-'.uniqid();

        $owner = $service->import([
            'strain' => 'Owner Soft Strain '.uniqid(),
            'brand' => $brandName,
            'seed_type' => 'F',
            'price' => 100,
            'variants' => [[
                'package_size' => 3,
                'sku' => $sku,
                'price' => 100,
                'stock' => 1,
            ]],
        ]);

        $updated = $service->import([
            'strain' => 'Intruder Soft Strain '.uniqid(),
            'brand' => $brandName,
            'seed_type' => 'R',
            'price' => 200,
            'variants' => [[
                'package_size' => 1,
                'sku' => $sku,
                'price' => 200,
                'stock' => 1,
            ]],
        ]);

        $this->assertSame($owner->id, $updated->id);
        $this->assertEquals(200, (float) $updated->price);
        $this->assertSame(1, Product::query()->where('brand_id', $owner->brand_id)->count());
        $this->assertSame(1, ProductVariant::query()->where('sku', $sku)->count());
    }
}
