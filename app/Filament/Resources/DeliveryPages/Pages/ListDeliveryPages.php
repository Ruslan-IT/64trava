<?php

namespace App\Filament\Resources\DeliveryPages\Pages;

use App\Filament\Resources\DeliveryPages\DeliveryPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryPages extends ListRecords
{
    protected static string $resource = DeliveryPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
