<?php

namespace App\Filament\Resources\DeliveryPages\Pages;

use App\Filament\Resources\DeliveryPages\DeliveryPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryPage extends EditRecord
{
    protected static string $resource = DeliveryPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
