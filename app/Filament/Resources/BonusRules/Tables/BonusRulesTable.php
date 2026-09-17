<?php

namespace App\Filament\Resources\BonusRules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BonusRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('min_order_amount')
                    ->label('От суммы')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' ₽')
                    ->sortable(),

                TextColumn::make('calculation_type')
                    ->label('Расчёт')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'fixed' => 'Фиксированный',
                        'per_amount' => 'За каждые N ₽',
                        default => $state,
                    }),

                TextColumn::make('bonus_quantity')
                    ->label('Бонусов')
                    ->sortable(),

                TextColumn::make('amount_step')
                    ->label('Шаг')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' ₽'),

                IconColumn::make('is_active')
                    ->label('Активно')
                    ->boolean(),

                TextColumn::make('priority')
                    ->label('Приоритет')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
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
