<?php

namespace App\Filament\Resources\Brands\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('logo')
                    ->label('Логотип')
                    ->disk('public')
                    ->circular()
                    ->size(45),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Адрес страницы')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label('Товаров')
                    ->counts('products')
                    ->sortable(),

                TextInputColumn::make('sort_order')
                    ->label('Порядок')
                    ->type('number')
                    ->inputMode('numeric')
                    ->step(1)
                    ->rules([
                        'required',
                        'integer',
                        'min:0',
                    ])
                    ->validationAttribute('порядок')
                    ->validationMessages([
                        'required' => 'Укажите порядок отображения.',
                        'integer' => 'Порядок должен быть целым числом.',
                        'min' => 'Порядок не может быть отрицательным.',
                    ])
                    ->sortable()
                    ->afterStateUpdated(function ($livewire): void {
                        $livewire->resetTable();
                    }),

                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

            ])

            ->defaultSort('sort_order')

            ->filters([])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
