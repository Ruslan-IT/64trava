<?php

namespace App\Filament\Resources\Brands\Pages;

use App\Filament\Resources\Brands\BrandResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('В корзину'),
            RestoreAction::make()
                ->label('Восстановить'),
            ForceDeleteAction::make()
                ->label('Удалить навсегда')
                ->requiresConfirmation()
                ->modalHeading('Окончательно удалить бренд?')
                ->modalDescription('Бренд будет удалён безвозвратно. Неиспользуемый логотип может быть удалён с диска. Бренды с товарами удалить нельзя. Это действие доступно только владельцу.'),
        ];
    }
}
