<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageFooter;
use App\Filament\Pages\Orders;
use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\DeliveryPages\DeliveryPageResource;
use App\Filament\Resources\HeroBanners\HeroBannerResource;
use App\Filament\Resources\HeroBanners\Pages\ListHeroBanners;
use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\News\Pages\ListNews;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\TagResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelLocalizationTest extends TestCase
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

        App::setLocale('ru');
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());
    }

    public function test_login_page_is_russian(): void
    {
        auth()->logout();

        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('Войдите в свой аккаунт');
        $response->assertSee('Адрес электронной почты');
        $response->assertSee('Пароль');
        $response->assertSee('Войти');
        $response->assertDontSee('Sign in');
        $response->assertDontSee('Email address');
    }

    public function test_navigation_labels_and_icons_are_russian_and_distinct(): void
    {
        $items = collect(Filament::getCurrentPanel()->getNavigation())->flatMap(
            fn ($group) => $group->getItems()
        );

        $labels = $items->map(fn ($item) => $item->getLabel())->all();
        $icons = $items->map(fn ($item) => $item->getIcon()->value)->all();

        foreach ([
            'Товары',
            'Бренды',
            'Категории',
            'Баннеры на главной',
            'Новости',
            'Теги',
            'Доставка и оплата',
            'Заказы',
            'Футер',
        ] as $label) {
            $this->assertContains($label, $labels);
        }

        $this->assertNotContains('Hero Banners', $labels);
        $this->assertNotContains('Products', $labels);
        $this->assertNotContains('New Products', $labels);
        $this->assertSame(count($icons), count(array_unique($icons)));

        $this->assertSame('o-cube', ProductResource::getNavigationIcon()->value);
        $this->assertSame('o-building-storefront', BrandResource::getNavigationIcon()->value);
        $this->assertSame('o-folder', CategoryResource::getNavigationIcon()->value);
        $this->assertSame('o-photo', HeroBannerResource::getNavigationIcon()->value);
        $this->assertSame('o-newspaper', NewsResource::getNavigationIcon()->value);
        $this->assertSame('o-tag', TagResource::getNavigationIcon()->value);
        $this->assertSame('o-truck', DeliveryPageResource::getNavigationIcon()->value);
        $this->assertSame('o-clipboard-document-list', Orders::getNavigationIcon()->value);
        $this->assertSame('o-bars-3-bottom-left', ManageFooter::getNavigationIcon()->value);
    }

    public function test_products_table_actions_use_russian_filament_strings(): void
    {
        Livewire::test(ListProducts::class)
            ->assertSee('Товары')
            ->assertSee('Поиск')
            ->assertSee('Импорт из Excel')
            ->assertActionExists('create', function (Action $action): bool {
                return $action->getLabel() === 'Создать';
            })
            ->assertActionExists('importExcel', function (Action $action): bool {
                return $action->getModalCancelActionLabel() === 'Отмена';
            });

        $this->assertSame('товар', ProductResource::getModelLabel());
        $this->assertSame('ru', app()->getLocale());
        $this->assertSame('Открыть действия', __('filament-tables::table.actions.open_bulk_actions.label'));
        $this->assertSame('Удалить отмеченное', __('filament-actions::delete.multiple.label'));
        $this->assertSame('Сохранить', __('filament-actions::edit.single.modal.actions.save.label'));
        $this->assertSame('Отменить', __('filament-actions::modal.actions.cancel.label'));
        $this->assertSame('Изменить', __('filament-actions::edit.single.label'));
        $this->assertSame('Удалить', __('filament-actions::delete.single.label'));
        $this->assertSame('Действия', __('filament-actions::group.trigger.label'));
    }

    public function test_section_pages_render_with_russian_titles(): void
    {
        Livewire::test(ListHeroBanners::class)->assertSee('Баннеры на главной');
        Livewire::test(ListBrands::class)->assertSee('Бренды');
        Livewire::test(ListCategories::class)->assertSee('Категории');
        Livewire::test(ListNews::class)->assertSee('Новости');
        Livewire::test(ListTags::class)->assertSee('Теги');
        Livewire::test(Orders::class)->assertSee('Заказы');
        Livewire::test(ManageFooter::class)->assertSee('Футер');
    }
}
