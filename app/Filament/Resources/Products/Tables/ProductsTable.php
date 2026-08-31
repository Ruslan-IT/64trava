<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Фото')
                    ->disk('public')
                    ->circular()
                    ->size(40),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label('Производитель')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('categories.name')
                    ->label('Категории')
                    ->badge()
                    ->separator(', ')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Цена')
                    ->money('USD')
                    ->sortable(),
                IconColumn::make('is_on_sale')
                    ->boolean()
                    ->label('Акция'),


                IconColumn::make('is_promo')
                    ->boolean()
                    ->label('Промо'),

                TextColumn::make('stock')
                    ->label('Остаток')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('categories')
                    ->label('Категория')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('brand')
                    ->label('Производитель')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('seed_type')
                    ->label('Тип семян')
                    ->options([
                        'A' => 'Автоцветущие',
                        'F' => 'Фотопериодные',
                        'R' => 'Регулярные',
                    ]),
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
