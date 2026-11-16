<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTrashTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label('Бренд')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('categories.name')
                    ->label('Категории')
                    ->badge()
                    ->separator(', ')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('deleted_at')
                    ->label('Удалён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->defaultSort('deleted_at', 'desc')
            ->recordActions([
                RestoreAction::make()
                    ->label('Восстановить'),
                ForceDeleteAction::make()
                    ->label('Удалить навсегда')
                    ->requiresConfirmation()
                    ->modalHeading('Окончательно удалить товар?')
                    ->modalDescription('Товар, связи и неиспользуемые фотографии будут удалены безвозвратно. Это действие доступно только владельцу.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->label('Восстановить выбранные'),
                    ForceDeleteBulkAction::make()
                        ->label('Удалить навсегда')
                        ->requiresConfirmation()
                        ->modalHeading('Окончательно удалить выбранные товары?')
                        ->modalDescription('Товары, связи и неиспользуемые фотографии будут удалены безвозвратно. Это действие доступно только владельцу.'),
                ]),
            ])
            ->emptyStateHeading('Корзина пуста')
            ->emptyStateDescription('Удалённые товары появятся здесь. Их можно восстановить вместе с фотографиями и данными.');
    }
}
