<?php

namespace App\Filament\Resources\DeliveryPages;

use App\Filament\Resources\DeliveryPages\Pages\CreateDeliveryPage;
use App\Filament\Resources\DeliveryPages\Pages\EditDeliveryPage;
use App\Filament\Resources\DeliveryPages\Pages\ListDeliveryPages;
use App\Filament\Resources\DeliveryPages\Schemas\DeliveryPageForm;
use App\Filament\Resources\DeliveryPages\Tables\DeliveryPagesTable;
use App\Models\DeliveryPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeliveryPageResource extends Resource
{


    protected static ?string $model = DeliveryPage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';


    protected static ?string $navigationLabel = 'Доставка и оплата';

    protected static ?string $modelLabel = 'Доставка и оплата';

    protected static ?string $pluralModelLabel = 'Доставка и оплата';

    //protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DeliveryPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryPagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryPages::route('/'),
            'create' => CreateDeliveryPage::route('/create'),
            'edit' => EditDeliveryPage::route('/{record}/edit'),
        ];
    }
}
