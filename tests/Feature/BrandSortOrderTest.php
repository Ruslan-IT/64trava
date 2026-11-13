<?php

namespace Tests\Feature;

use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class BrandSortOrderTest extends TestCase
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

    public function test_filament_list_shows_editable_sort_order_column(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());

        $brand = Brand::query()->create([
            'name' => 'Editable Sort Column Brand',
            'sort_order' => 7,
        ]);

        Livewire::test(ListBrands::class)
            ->assertSee('Порядок')
            ->assertTableColumnExists('sort_order')
            ->assertCanSeeTableRecords([$brand]);
    }

    public function test_filament_list_follows_saved_manual_sort_order(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());

        $second = Brand::query()->create([
            'name' => 'Sort Brand Second',
            'sort_order' => 20,
        ]);
        $first = Brand::query()->create([
            'name' => 'Sort Brand First',
            'sort_order' => 10,
        ]);
        $third = Brand::query()->create([
            'name' => 'Sort Brand Third',
            'sort_order' => 30,
        ]);

        Livewire::test(ListBrands::class)
            ->assertCanSeeTableRecords([$first, $second, $third], inOrder: true);
    }

    public function test_sort_order_can_be_updated_inline_from_brands_table(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());

        $alpha = Brand::query()->create([
            'name' => 'Inline Alpha',
            'sort_order' => 5,
        ]);
        $beta = Brand::query()->create([
            'name' => 'Inline Beta',
            'sort_order' => 10,
        ]);

        Livewire::test(ListBrands::class)
            ->call('updateTableColumnState', 'sort_order', (string) $beta->getKey(), 1)
            ->assertHasNoErrors();

        $this->assertSame(1, $beta->fresh()->sort_order);
        $this->assertSame(5, $alpha->fresh()->sort_order);

        Livewire::test(ListBrands::class)
            ->assertCanSeeTableRecords([$beta->fresh(), $alpha->fresh()], inOrder: true);
    }

    public function test_negative_sort_order_is_rejected_inline(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());

        $brand = Brand::query()->create([
            'name' => 'Invalid Sort Brand',
            'sort_order' => 4,
        ]);

        $response = Livewire::test(ListBrands::class)
            ->call('updateTableColumnState', 'sort_order', (string) $brand->getKey(), -1);

        $this->assertSame(4, $brand->fresh()->sort_order);
        $this->assertIsArray($response->effects['returns'][0] ?? null);
        $this->assertArrayHasKey('error', $response->effects['returns'][0]);
    }

    public function test_home_brands_section_matches_manual_sort_order(): void
    {
        $second = Brand::query()->create([
            'name' => 'Home Brand Second',
            'sort_order' => 200,
        ]);
        $first = Brand::query()->create([
            'name' => 'Home Brand First',
            'sort_order' => 100,
        ]);
        $third = Brand::query()->create([
            'name' => 'Home Brand Third',
            'sort_order' => 300,
        ]);

        $html = $this->get(route('home'))
            ->assertOk()
            ->assertSee('brands-section', false)
            ->getContent();

        $section = $this->brandsSectionHtml($html);
        $firstPos = strpos($section, 'Home Brand First');
        $secondPos = strpos($section, 'Home Brand Second');
        $thirdPos = strpos($section, 'Home Brand Third');

        $this->assertNotFalse($firstPos);
        $this->assertNotFalse($secondPos);
        $this->assertNotFalse($thirdPos);
        $this->assertTrue($firstPos < $secondPos);
        $this->assertTrue($secondPos < $thirdPos);
        $this->assertSame(
            [$first->id, $second->id, $third->id],
            Brand::query()
                ->whereIn('id', [$first->id, $second->id, $third->id])
                ->ordered()
                ->pluck('id')
                ->all()
        );
    }

    public function test_seedbanks_page_matches_manual_sort_order(): void
    {
        $second = Brand::query()->create([
            'name' => 'Seedbank Brand Second',
            'sort_order' => 200,
        ]);
        $first = Brand::query()->create([
            'name' => 'Seedbank Brand First',
            'sort_order' => 100,
        ]);
        $third = Brand::query()->create([
            'name' => 'Seedbank Brand Third',
            'sort_order' => 300,
        ]);

        $html = $this->get(route('seedbanks.index'))
            ->assertOk()
            ->getContent();

        $ordered = Brand::query()
            ->whereIn('id', [$first->id, $second->id, $third->id])
            ->ordered()
            ->pluck('slug')
            ->all();

        $this->assertSame(
            [$first->slug, $second->slug, $third->slug],
            $ordered
        );

        $positions = array_map(
            fn (string $slug) => strpos($html, 'brands/'.$slug),
            $ordered
        );

        $this->assertTrue($positions[0] < $positions[1]);
        $this->assertTrue($positions[1] < $positions[2]);
    }

    public function test_equal_sort_order_is_stable_by_id(): void
    {
        $earlier = Brand::query()->create([
            'name' => 'Equal Sort Earlier',
            'sort_order' => 50,
        ]);
        $later = Brand::query()->create([
            'name' => 'Equal Sort Later',
            'sort_order' => 50,
        ]);

        $this->assertSame(
            [$earlier->id, $later->id],
            Brand::query()
                ->whereIn('id', [$earlier->id, $later->id])
                ->ordered()
                ->pluck('id')
                ->all()
        );

        $home = $this->brandsSectionHtml($this->get(route('home'))->assertOk()->getContent());
        $this->assertTrue(
            strpos($home, 'Equal Sort Earlier') < strpos($home, 'Equal Sort Later')
        );

        $seedbanks = $this->get(route('seedbanks.index'))->assertOk()->getContent();
        $this->assertTrue(
            strpos($seedbanks, 'brands/'.$earlier->slug) < strpos($seedbanks, 'brands/'.$later->slug)
        );
    }

    public function test_new_brands_are_appended_to_the_end(): void
    {
        $maxSortOrder = (int) Brand::query()->max('sort_order');

        $existing = Brand::query()->create([
            'name' => 'Existing Sorted Brand',
            'sort_order' => $maxSortOrder + 10,
        ]);

        $created = Brand::query()->create([
            'name' => 'Fresh Sorted Brand',
        ]);

        $this->assertSame($existing->sort_order + 1, $created->sort_order);
        $this->assertGreaterThan($existing->sort_order, $created->sort_order);
    }

    public function test_product_count_does_not_affect_brand_order(): void
    {
        $fewProducts = Brand::query()->create([
            'name' => 'Few Products Brand',
            'sort_order' => 1,
        ]);
        $manyProducts = Brand::query()->create([
            'name' => 'Many Products Brand',
            'sort_order' => 2,
        ]);

        Product::query()->create([
            'brand_id' => $fewProducts->id,
            'name' => 'Few Product One',
            'price' => 100,
            'is_visible' => true,
        ]);

        foreach (range(1, 5) as $index) {
            Product::query()->create([
                'brand_id' => $manyProducts->id,
                'name' => "Many Product {$index}",
                'price' => 100,
                'is_visible' => true,
            ]);
        }

        $orderedIds = Brand::query()
            ->whereIn('id', [$fewProducts->id, $manyProducts->id])
            ->ordered()
            ->pluck('id')
            ->all();

        $this->assertSame([$fewProducts->id, $manyProducts->id], $orderedIds);

        $html = $this->get(route('home'))->assertOk()->getContent();
        $section = $this->brandsSectionHtml($html);

        $this->assertTrue(
            strpos($section, 'Few Products Brand') < strpos($section, 'Many Products Brand')
        );
    }

    protected function brandsSectionHtml(string $html): string
    {
        if (! preg_match('/<section class="brands-section">(.*?)<\/section>/s', $html, $matches)) {
            $this->fail('brands-section not found on home page');
        }

        return $matches[1];
    }
}
