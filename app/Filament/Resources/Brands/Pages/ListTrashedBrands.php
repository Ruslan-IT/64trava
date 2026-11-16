<?php

namespace App\Filament\Resources\Brands\Pages;

use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Brands\Tables\BrandsTrashTable;
use App\Models\Brand;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListTrashedBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected static ?string $breadcrumb = 'Корзина';

    public function getTitle(): string | Htmlable
    {
        return 'Корзина брендов';
    }

    public function table(Table $table): Table
    {
        return BrandsTrashTable::configure($table);
    }

    protected function getTableQuery(): Builder
    {
        return Brand::query()->onlyTrashed();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToBrands')
                ->label('К брендам')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url(BrandResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
