<?php

namespace App\Filament\Resources\BonusRules\Schemas;

use App\Models\BonusProduct;
use App\Models\Brand;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BonusRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название правила')
                    ->required()
                    ->maxLength(255),

                TextInput::make('min_order_amount')
                    ->label('Минимальная сумма заказа')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->suffix('₽'),

                Select::make('calculation_type')
                    ->label('Тип расчёта')
                    ->options([
                        'fixed' => 'Фиксированное количество',
                        'per_amount' => 'За каждые N рублей',
                    ])
                    ->required()
                    ->default('fixed')
                    ->live(),

                TextInput::make('bonus_quantity')
                    ->label('Количество бонусов')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    ->helperText('Для фиксированного типа — количество бонусных семян.'),

                TextInput::make('amount_step')
                    ->label('Шаг суммы')
                    ->numeric()
                    ->minValue(1)
                    ->suffix('₽')
                    ->visible(fn ($get) => $get('calculation_type') === 'per_amount')
                    ->required(fn ($get) => $get('calculation_type') === 'per_amount')
                    ->helperText('Например: 1000 ₽ = 1 бонус.'),

                Toggle::make('is_active')
                    ->label('Правило активно')
                    ->default(true),

                TextInput::make('priority')
                    ->label('Приоритет')
                    ->numeric()
                    ->default(0)
                    ->helperText('Чем выше значение, тем выше приоритет.'),

                CheckboxList::make('brands')
                    ->label('Бренды, участвующие в правиле')
                    ->relationship('brands', 'name')
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(2),

                CheckboxList::make('bonusProducts')
                    ->label('Доступные бонусные товары')
                    ->relationship('bonusProducts', 'id')
                    ->options(
                        BonusProduct::with('product')
                            ->get()
                            ->mapWithKeys(fn (BonusProduct $bonusProduct) => [
                                $bonusProduct->id => $bonusProduct->product->name ?? 'Товар #' . $bonusProduct->id,
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(2),
            ]);
    }
}
