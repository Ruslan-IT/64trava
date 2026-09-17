<?php

namespace App\Filament\Resources\BonusRules\Pages;

use App\Filament\Resources\BonusRules\BonusRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBonusRules extends ListRecords
{
    protected static string $resource = BonusRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
