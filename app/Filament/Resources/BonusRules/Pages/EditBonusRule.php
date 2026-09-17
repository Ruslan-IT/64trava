<?php

namespace App\Filament\Resources\BonusRules\Pages;

use App\Filament\Resources\BonusRules\BonusRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBonusRule extends EditRecord
{
    protected static string $resource = BonusRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
