<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Tables\ProductsTrashTable;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListTrashedProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected static ?string $breadcrumb = 'Корзина';

    public function getTitle(): string | Htmlable
    {
        return 'Корзина товаров';
    }

    public function table(Table $table): Table
    {
        return ProductsTrashTable::configure($table);
    }

    protected function getTableQuery(): Builder
    {
        return Product::query()->onlyTrashed();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToProducts')
                ->label('К товарам')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url(ProductResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
