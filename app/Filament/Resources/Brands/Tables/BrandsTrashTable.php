<?php

namespace App\Filament\Resources\Brands\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BrandsTrashTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

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
                    ->modalHeading('Окончательно удалить бренд?')
                    ->modalDescription('Бренд будет удалён безвозвратно. Неиспользуемый логотип может быть удалён с диска. Бренды с товарами удалить нельзя. Это действие доступно только владельцу.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->label('Восстановить выбранные'),
                    ForceDeleteBulkAction::make()
                        ->label('Удалить навсегда')
                        ->requiresConfirmation()
                        ->modalHeading('Окончательно удалить выбранные бренды?')
                        ->modalDescription('Бренды будут удалены безвозвратно. Неиспользуемые логотипы могут быть удалены с диска. Бренды с товарами удалить нельзя. Это действие доступно только владельцу.'),
                ]),
            ])
            ->emptyStateHeading('Корзина пуста')
            ->emptyStateDescription('Удалённые бренды появятся здесь. Их можно восстановить вместе с логотипом и связями.');
    }
}
