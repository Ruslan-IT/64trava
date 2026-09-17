<?php

namespace App\Filament\Resources\BonusProducts\Pages;

use App\Filament\Resources\BonusProducts\BonusProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBonusProduct extends EditRecord
{
    protected static string $resource = BonusProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
