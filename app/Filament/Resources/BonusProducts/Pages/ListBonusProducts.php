<?php

namespace App\Filament\Resources\BonusProducts\Pages;

use App\Filament\Resources\BonusProducts\BonusProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBonusProducts extends ListRecords
{
    protected static string $resource = BonusProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
