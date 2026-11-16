<?php

namespace App\Filament\Resources\Brands\Pages;

use App\Filament\Resources\Brands\BrandResource;
use App\Models\Brand;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->trashAction(),
            CreateAction::make(),
        ];
    }

    private function trashAction(): Action
    {
        return Action::make('trash')
            ->label(fn (): string => 'Корзина ('.$this->trashedBrandsCount().')')
            ->icon(Heroicon::OutlinedTrash)
            ->color('gray')
            ->url(BrandResource::getUrl('trash'));
    }

    private function trashedBrandsCount(): int
    {
        return Brand::onlyTrashed()->count();
    }
}
