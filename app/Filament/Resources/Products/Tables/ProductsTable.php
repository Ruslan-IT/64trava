<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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

                IconColumn::make('is_visible')
                    ->label('В каталоге')
                    ->getStateUsing(fn (Product $record): bool => $record->isListedInCatalog())
                    ->boolean()
                    ->tooltip(fn (Product $record): string => $record->catalogListingLabel()),

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
                    ->money('RUB')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Остаток')
                    ->sortable(),
            ])

            ->filters([
                TrashedFilter::make()
                    ->label('Корзина'),

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
                    DeleteBulkAction::make()
                        ->label('В корзину'),
                    RestoreBulkAction::make()
                        ->label('Восстановить'),
                    ForceDeleteBulkAction::make()
                        ->label('Удалить навсегда')
                        ->requiresConfirmation()
                        ->modalHeading('Окончательно удалить выбранные товары?')
                        ->modalDescription('Товары, связи и неиспользуемые фотографии будут удалены безвозвратно. Это действие доступно только владельцу.'),
                ]),
            ])

            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
