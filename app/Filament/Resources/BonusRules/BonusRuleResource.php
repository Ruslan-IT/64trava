<?php

namespace App\Filament\Resources\BonusRules;

use App\Filament\Resources\BonusRules\Pages\CreateBonusRule;
use App\Filament\Resources\BonusRules\Pages\EditBonusRule;
use App\Filament\Resources\BonusRules\Pages\ListBonusRules;
use App\Filament\Resources\BonusRules\Schemas\BonusRuleForm;
use App\Filament\Resources\BonusRules\Tables\BonusRulesTable;
use App\Models\BonusRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BonusRuleResource extends Resource
{
    protected static ?string $model = BonusRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BonusRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BonusRulesTable::configure($table);
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
            'index' => ListBonusRules::route('/'),
            'create' => CreateBonusRule::route('/create'),
            'edit' => EditBonusRule::route('/{record}/edit'),
        ];
    }
}
