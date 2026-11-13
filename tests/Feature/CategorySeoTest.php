<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Models\Category;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CategorySeoTest extends TestCase
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

    public function test_category_page_uses_saved_seo_fields(): void
    {
        $category = Category::query()->create([
            'name' => 'SEO Category Page',
            'slug' => 'seo-category-page',
            'seo_title' => 'Custom Category Title',
            'seo_description' => 'Custom category description',
            'seo_keywords' => 'custom, category, keywords',
        ]);

        $this->get(route('catalog.category', $category->slug))
            ->assertOk()
            ->assertSee('<title>Custom Category Title | Dutch Seeds</title>', false)
            ->assertSee('<meta name="description" content="Custom category description">', false)
            ->assertSee('<meta name="keywords" content="custom, category, keywords">', false)
            ->assertSee('<link rel="canonical" href="'.route('catalog.category', $category->slug).'">', false);
    }

    public function test_category_page_falls_back_when_seo_fields_are_empty(): void
    {
        $category = Category::query()->create([
            'name' => 'Fallback Category',
            'slug' => 'fallback-category',
        ]);

        $this->get(route('catalog.category', $category->slug))
            ->assertOk()
            ->assertSee('<title>Fallback Category | Dutch Seeds</title>', false)
            ->assertSee('<meta name="description" content="Каталог Dutch Seeds.">', false)
            ->assertSee('<meta name="keywords" content="Каталог, новинки, информация, Dutch Seeds">', false);
    }

    public function test_category_filament_form_exposes_seo_fields(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());

        $category = Category::query()->create([
            'name' => 'Admin SEO Category',
            'slug' => 'admin-seo-category',
            'seo_title' => 'Saved SEO Title',
            'seo_description' => 'Saved SEO Description',
            'seo_keywords' => 'saved, keywords',
        ]);

        Livewire::test(CreateCategory::class)
            ->assertFormFieldExists('seo_title')
            ->assertFormFieldExists('seo_description')
            ->assertFormFieldExists('seo_keywords');

        Livewire::test(EditCategory::class, ['record' => $category->getKey()])
            ->assertFormFieldExists('seo_title')
            ->assertFormFieldExists('seo_description')
            ->assertFormFieldExists('seo_keywords')
            ->assertFormSet([
                'seo_title' => 'Saved SEO Title',
                'seo_description' => 'Saved SEO Description',
                'seo_keywords' => 'saved, keywords',
            ]);
    }
}
