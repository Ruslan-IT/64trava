<?php

namespace Tests\Feature;

use App\Livewire\CatalogFilter;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogWelcomeTest extends TestCase
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

    public function test_description_renders_after_heading_with_welcome_text_classes(): void
    {
        $category = Category::query()->updateOrCreate(
            ['slug' => 'autoflower'],
            [
                'name' => 'Автоцветущие',
                'description' => '<p>Длинное видимое описание категории для блока приветствия.</p>',
                'seo_description' => 'SEO description must stay unused in welcome text',
            ]
        );

        $html = $this->get(route('catalog.category', $category->slug))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/catalog-welcome-title[\s\S]*?<h1[^>]*>Автоцветущие<\/h1>[\s\S]*?catalog-welcome-text[\s\S]*?Длинное видимое описание категории для блока приветствия\./u',
            $html
        );
        $this->assertStringContainsString('class="catalog-welcome-text"', $html);
        $this->assertStringContainsString('x-data="catalogWelcomeText()"', $html);
        $this->assertStringContainsString('class="catalog-welcome-more"', $html);
        $this->assertStringNotContainsString('SEO description must stay unused in welcome text', $this->welcomeSection($html));
        $this->assertStringNotContainsString('href="#"', $this->welcomeSection($html));
    }

    public function test_long_description_keeps_toggle_button_markup(): void
    {
        $category = Category::query()->updateOrCreate(
            ['slug' => 'feminised'],
            [
                'name' => 'Феминизированные',
                'description' => '<p>'.str_repeat('Очень длинное описание категории. ', 40).'</p>',
            ]
        );

        $html = Livewire::test(CatalogFilter::class, [
            'currentCategory' => $category,
            'catalogInfo' => $category,
        ])->html();

        $welcome = $this->welcomeSection($html);

        $this->assertStringContainsString('catalog-welcome-text', $welcome);
        $this->assertStringContainsString('type="button"', $welcome);
        $this->assertStringContainsString('x-show="showMore"', $welcome);
        $this->assertStringContainsString('aria-expanded', $welcome);
        $this->assertStringContainsString('x-text="expanded ? \'Свернуть\' : \'Читать далее\'"', $welcome);
        $this->assertStringContainsString('Очень длинное описание категории.', $welcome);
    }

    public function test_short_and_empty_descriptions_keep_reserved_area_without_forced_link(): void
    {
        $short = Category::query()->updateOrCreate(
            ['slug' => 'novinki'],
            [
                'name' => 'Новинки',
                'description' => '<p>Короткий текст.</p>',
            ]
        );
        $empty = Category::query()->updateOrCreate(
            ['slug' => 'regular'],
            [
                'name' => 'Регулярные',
                'description' => '<p></p>',
            ]
        );

        $shortHtml = Livewire::test(CatalogFilter::class, [
            'currentCategory' => $short,
            'catalogInfo' => $short,
        ])->html();
        $emptyHtml = Livewire::test(CatalogFilter::class, [
            'currentCategory' => $empty,
            'catalogInfo' => $empty,
        ])->html();

        $shortWelcome = $this->welcomeSection($shortHtml);
        $emptyWelcome = $this->welcomeSection($emptyHtml);

        $this->assertStringContainsString('Короткий текст.', $shortWelcome);
        $this->assertStringContainsString('catalog-welcome-text', $shortWelcome);
        $this->assertStringContainsString('catalog-welcome-more-wrap', $shortWelcome);
        $this->assertStringNotContainsString('href="#"', $shortWelcome);

        $this->assertStringContainsString('data-empty="1"', $emptyWelcome);
        $this->assertStringContainsString('catalog-welcome-text', $emptyWelcome);
        $this->assertStringContainsString('catalog-welcome-more-wrap', $emptyWelcome);
        $this->assertStringContainsString('x-show="showMore"', $emptyWelcome);
        $this->assertDoesNotMatchRegularExpression(
            '/catalog-welcome-text[^>]*>\s*<p>\s*<\/p>/',
            $emptyWelcome
        );
    }

    public function test_switching_category_replaces_description_and_resets_welcome_state_key(): void
    {
        $first = Category::query()->updateOrCreate(
            ['slug' => 'autoflower'],
            [
                'name' => 'Автоцветущие',
                'description' => '<p>Описание автоцветущих уникальное AAA.</p>',
            ]
        );
        $second = Category::query()->updateOrCreate(
            ['slug' => 'novinki'],
            [
                'name' => 'Новинки',
                'description' => '<p>Описание новинок уникальное BBB.</p>',
            ]
        );

        $component = Livewire::test(CatalogFilter::class, [
            'currentCategory' => $first,
            'catalogInfo' => $first,
        ])
            ->assertSee('Описание автоцветущих уникальное AAA.')
            ->assertDontSee('Описание новинок уникальное BBB.')
            ->assertSeeHtml('wire:key="catalog-welcome-b-'.$first->id.'-t-s"');

        $component
            ->call('selectCategory', 'novinki')
            ->assertSee('Описание новинок уникальное BBB.')
            ->assertDontSee('Описание автоцветущих уникальное AAA.')
            ->assertSeeHtml('wire:key="catalog-welcome-b-'.$second->id.'-t-s"')
            ->assertSeeHtml('x-data="catalogWelcomeText()"')
            ->assertSeeHtml(":class=\"{ 'is-expanded': expanded }\"");
    }

    protected function welcomeSection(string $html): string
    {
        if (! preg_match('/<div\s+class="catalog-welcome catalog-welcome-body"[^>]*>([\s\S]*?)(?:<div class="catalog-controls"|$)/', $html, $matches)) {
            $this->fail('catalog-welcome block not found');
        }

        return $matches[1];
    }
}
