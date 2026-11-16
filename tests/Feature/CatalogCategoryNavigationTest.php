<?php

namespace Tests\Feature;

use App\Livewire\CatalogFilter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogCategoryNavigationTest extends TestCase
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

    public function test_catalog_category_routes_render_active_button_products_and_seo(): void
    {
        $brand = Brand::query()->create(['name' => 'Nav Brand']);

        $categories = [
            'autoflower' => 'Автоцветущие',
            'feminised' => 'Феминизированные',
            'regular' => 'Регулярные',
            'miksy-semian' => 'Миксы семян',
            'akcii' => 'Семена по акции',
            'krupnye-packi' => 'Крупные пачки',
            'naibolee-populiarnye' => 'Наиболее популярные',
            'novinki' => 'Новинки',
        ];

        foreach ($categories as $slug => $name) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => 'Описание '.$name,
                    'seo_title' => 'SEO '.$name,
                    'seo_description' => 'Desc '.$name,
                    'seo_keywords' => 'kw '.$slug,
                ]
            );

            $product = $this->product($brand, $name.' Product', 'F');
            $product->categories()->sync([$category->id]);
        }

        $index = $this->get(route('catalog.index'))->assertOk();
        $indexButtons = $this->catalogButtons($index->getContent());
        $this->assertStringNotContainsString('wire:navigate', $indexButtons);
        $this->assertStringContainsString('wire:click.prevent="selectCategory(null)"', $indexButtons);
        $this->assertMatchesRegularExpression(
            '/href="[^"]*\/vsesemena"[^>]*class="category-button active"/',
            $indexButtons
        );
        $this->assertStringContainsString('<title>Все семена | Dutch Seeds</title>', $index->getContent());

        foreach ($categories as $slug => $name) {
            $response = $this->get(route('catalog.category', $slug))->assertOk();
            $html = $response->getContent();
            $buttons = $this->catalogButtons($html);

            $this->assertStringNotContainsString('wire:navigate', $buttons);
            $this->assertStringContainsString("wire:click.prevent=\"selectCategory('".$slug."')\"", $buttons);
            $this->assertMatchesRegularExpression(
                '/href="[^"]*\/cat\/'.preg_quote($slug, '/').'"[^>]*class="category-button active"/',
                $buttons
            );
            $this->assertStringContainsString('<title>SEO '.$name.' | Dutch Seeds</title>', $html);
            $this->assertStringContainsString('<meta name="description" content="Desc '.$name.'">', $html);
            $this->assertStringContainsString('<link rel="canonical" href="'.route('catalog.category', $slug).'">', $html);
            $this->assertStringContainsString('Описание '.$name, $html);
            $this->assertStringContainsString($name.' Product', $html);
            $this->assertStringContainsString('<h1 style="margin:0;padding:0;font:inherit;line-height:inherit;">'.$name.'</h1>', $html);
        }
    }

    public function test_livewire_select_category_switches_heading_seo_payload_and_resets_filters(): void
    {
        Category::query()->updateOrCreate(
            ['slug' => 'feminised'],
            [
                'name' => 'Феминизированные',
                'description' => 'Текст феминизированных',
                'seo_title' => 'SEO Феминизированные',
                'seo_description' => 'Desc феминизированные',
                'seo_keywords' => 'kw feminised',
            ]
        );
        Category::query()->updateOrCreate(
            ['slug' => 'novinki'],
            [
                'name' => 'Новинки',
                'description' => 'Текст новинок',
                'seo_title' => 'SEO Новинки',
                'seo_description' => 'Desc новинки',
                'seo_keywords' => 'kw novinki',
            ]
        );

        $component = Livewire::test(CatalogFilter::class, [
            'currentCategory' => null,
            'catalogInfo' => null,
        ])
            ->assertSeeHtml('wire:click.prevent="selectCategory(null)"')
            ->assertDontSeeHtml('wire:navigate')
            ->set('thcMin', 20)
            ->set('thcMax', 30)
            ->set('sort', 'name_desc')
            ->call('selectCategory', 'feminised')
            ->assertSet('currentCategory.slug', 'feminised')
            ->assertSet('thcMin', 1)
            ->assertSet('thcMax', 60)
            ->assertSet('sort', '')
            ->assertSeeHtml('<h1 style="margin:0;padding:0;font:inherit;line-height:inherit;">Феминизированные</h1>')
            ->assertSee('Текст феминизированных');

        $effects = json_encode($component->effects);
        $this->assertStringContainsString('__catalogApplyCategory', $effects);
        $this->assertStringContainsString('cat\\/feminised', $effects);
        $this->assertStringContainsString('pushState', $effects);

        $component
            ->call('selectCategory', 'novinki')
            ->assertSet('currentCategory.slug', 'novinki')
            ->assertSeeHtml('<h1 style="margin:0;padding:0;font:inherit;line-height:inherit;">Новинки</h1>')
            ->assertSee('Текст новинок')
            ->call('selectCategory', null)
            ->assertSet('currentCategory', null)
            ->assertSeeHtml('<h1 style="margin:0;padding:0;font:inherit;line-height:inherit;">Все семена</h1>');
    }

    public function test_filters_sort_and_pagination_still_work_on_category_pages(): void
    {
        $brand = Brand::query()->create(['name' => 'Filter Nav Brand']);
        $category = Category::query()->updateOrCreate(
            ['slug' => 'feminised'],
            ['name' => 'Феминизированные']
        );

        $visible = $this->product($brand, 'Visible Feminised Alpha', 'F', [
            'thc' => '25',
            'flowering' => '60',
            'height' => '120',
        ]);
        $visible->categories()->sync([$category->id]);

        $other = $this->product($brand, 'Other Feminised Beta', 'F', [
            'thc' => '10',
            'flowering' => '60',
            'height' => '120',
        ]);
        $other->categories()->sync([$category->id]);

        $this->get(route('catalog.category', [
            'category' => 'feminised',
            'thc_min' => 20,
            'thc_max' => 30,
        ]))
            ->assertOk()
            ->assertSee('Visible Feminised Alpha')
            ->assertDontSee('Other Feminised Beta');

        $this->get(route('catalog.category', [
            'category' => 'feminised',
            'sort' => 'name_desc',
        ]))
            ->assertOk()
            ->assertSee('Visible Feminised Alpha')
            ->assertSee('Other Feminised Beta');

        $this->get(route('catalog.category', [
            'category' => 'feminised',
            'page' => 1,
        ]))
            ->assertOk()
            ->assertSee('Visible Feminised Alpha')
            ->assertSee('Other Feminised Beta');
    }

    private function catalogButtons(string $html): string
    {
        if (! preg_match('/<div class="catalog-category-buttons">(.*?)(?:<div\s+class="[^"]*catalog-welcome|$)/s', $html, $matches)) {
            return '';
        }

        return $matches[1];
    }

    private function product(Brand $brand, string $name, string $seedType, array $extra = []): Product
    {
        return Product::query()->create(array_merge([
            'brand_id' => $brand->id,
            'name' => $name,
            'price' => 1500,
            'seed_type' => $seedType,
            'thc' => '20',
            'flowering' => '60',
            'height' => '100',
            'is_visible' => true,
        ], $extra));
    }
}
