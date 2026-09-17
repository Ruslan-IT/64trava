<?php

namespace App\Filament\Resources\BonusProducts\Schemas;

use App\Models\Brand;
use App\Models\Product;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BonusProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Бонусный товар')
                    ->options(
                        Product::query()
                            ->with('brand')
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn (Product $product) => [
                                $product->id => $product->name . ' — ' . $product->brand->name,
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('threshold')
                    ->label('За каждые')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->suffix('₽')
                    ->helperText(
                        'За каждые указанную сумму заказа клиент получает 1 такое бонусное семя.'
                    ),

                TextInput::make('stock')
                    ->label('Остаток бонусных семян')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0),

                CheckboxList::make('brands')
                    ->label('Доступен при покупке брендов')
                    ->relationship('brands', 'name')
                    ->columns(2)
                    ->searchable(),

                Toggle::make('is_active')
                    ->label('Бонус доступен')
                    ->default(true),

                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText(
                        'Меньшее число — выше в списке. Для Dutch Bulk используем 0.'
                    ),
            ]);
    }
}
