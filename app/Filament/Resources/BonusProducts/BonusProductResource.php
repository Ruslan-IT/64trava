<?php

namespace App\Filament\Resources\BonusProducts;

use App\Filament\Resources\BonusProducts\Pages\CreateBonusProduct;
use App\Filament\Resources\BonusProducts\Pages\EditBonusProduct;
use App\Filament\Resources\BonusProducts\Pages\ListBonusProducts;
use App\Filament\Resources\BonusProducts\Schemas\BonusProductForm;
use App\Filament\Resources\BonusProducts\Tables\BonusProductsTable;
use App\Models\BonusProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BonusProductResource extends Resource
{
    protected static ?string $model = BonusProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BonusProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BonusProductsTable::configure($table);
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
            'index' => ListBonusProducts::route('/'),
            'create' => CreateBonusProduct::route('/create'),
            'edit' => EditBonusProduct::route('/{record}/edit'),
        ];
    }
}
