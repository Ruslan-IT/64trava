<?php

namespace Tests\Feature;

use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
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

    public function test_filament_reorder_persists_sort_order_in_database(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());

        $alpha = Brand::query()->create([
            'name' => 'Reorder Alpha',
            'sort_order' => 1,
        ]);
        $beta = Brand::query()->create([
            'name' => 'Reorder Beta',
            'sort_order' => 2,
        ]);
        $gamma = Brand::query()->create([
            'name' => 'Reorder Gamma',
            'sort_order' => 3,
        ]);

        Livewire::test(ListBrands::class)
            ->call('reorderTable', [
                (string) $gamma->id,
                (string) $alpha->id,
                (string) $beta->id,
            ]);

        $this->assertSame(1, $gamma->fresh()->sort_order);
        $this->assertSame(2, $alpha->fresh()->sort_order);
        $this->assertSame(3, $beta->fresh()->sort_order);
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
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->all()
        );
    }

    public function test_doctors_choice_id_28_is_first_after_initial_ordering(): void
    {
        DB::table('brands')->where('id', 28)->delete();

        $otherEarly = Brand::query()->create([
            'name' => 'Early Other Brand',
            'slug' => 'early-other-brand-sort',
        ]);
        $otherLate = Brand::query()->create([
            'name' => 'Late Other Brand',
            'slug' => 'late-other-brand-sort',
        ]);

        DB::table('brands')->insert([
            'id' => 28,
            'name' => "Doctor's Choice",
            'slug' => 'doctors-choice-sort-test',
            'sort_order' => 999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->applyInitialBrandSortOrder();

        $ordered = Brand::query()
            ->whereIn('id', [$otherEarly->id, $otherLate->id, 28])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->assertSame(28, $ordered->first()->id);
        $this->assertSame(1, (int) $ordered->first()->sort_order);
        $this->assertTrue($ordered->get(1)->sort_order < $ordered->get(2)->sort_order);
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
            ->orderBy('sort_order')
            ->orderBy('id')
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

    protected function applyInitialBrandSortOrder(): void
    {
        $doctorsChoiceId = DB::table('brands')
            ->where('id', 28)
            ->orWhere('slug', 'doctors-choice')
            ->orWhere('name', "Doctor's Choice")
            ->orderByRaw('CASE WHEN id = 28 THEN 0 ELSE 1 END')
            ->value('id');

        $orderedIds = [];

        if ($doctorsChoiceId !== null) {
            $orderedIds[] = (int) $doctorsChoiceId;
        }

        $remainingIds = DB::table('brands')
            ->when($doctorsChoiceId !== null, fn ($query) => $query->where('id', '!=', $doctorsChoiceId))
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach (array_merge($orderedIds, $remainingIds) as $index => $id) {
            DB::table('brands')
                ->where('id', $id)
                ->update(['sort_order' => $index + 1]);
        }
    }
}
