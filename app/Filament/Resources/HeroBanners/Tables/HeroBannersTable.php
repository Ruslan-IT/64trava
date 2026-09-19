<?php

namespace App\Filament\Resources\HeroBanners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HeroBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('desktop_image')
                    ->label('ПК')
                    ->disk('public')
                    ->square(),

                ImageColumn::make('mobile_image')
                    ->label('Мобильный')
                    ->disk('public')
                    ->square(),

                TextColumn::make('label')
                    ->label('Надпись')
                    ->searchable(),

                TextColumn::make('title_new')
                    ->label('Заголовок')
                    ->searchable(),

                TextColumn::make('duration')
                    ->label('Время')
                    ->suffix(' сек.'),

                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Активен'),

            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([])
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
