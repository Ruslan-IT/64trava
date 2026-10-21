<?php

namespace App\Filament\Resources\Brands\RelationManagers;

use App\Models\BrandSubcategory;
use Closure;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class SubcategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'subcategories';

    protected static ?string $title = 'Подкатегории';

    protected static ?string $modelLabel = 'подкатегория';

    protected static ?string $pluralModelLabel = 'подкатегории';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, Set $set, Get $get) {
                        if (blank($get('slug'))) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        table: 'brand_subcategories',
                        column: 'slug',
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule) {
                            return $rule->where('brand_id', $this->getOwnerRecord()->getKey());
                        },
                    ),

                TextInput::make('h1')
                    ->label('H1')
                    ->maxLength(255),

                TextInput::make('seo_title')
                    ->label('SEO Title')
                    ->maxLength(255),

                Textarea::make('seo_description')
                    ->label('SEO Description')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('seo_text')
                    ->label('SEO-текст')
                    ->rows(6)
                    ->columnSpanFull(),

                Textarea::make('seo_keywords')
                    ->label('SEO ключевые слова')
                    ->rows(2)
                    ->columnSpanFull(),

                Select::make('seed_type')
                    ->label('Тип семян')
                    ->options(BrandSubcategory::SEED_TYPES)
                    ->placeholder('Не выбран')
                    ->native(false),

                TextInput::make('name_token')
                    ->label('Фрагмент названия')
                    ->maxLength(50)
                    ->helperText('Для F1 укажите F1 и оставьте тип семян пустым: отдельного поля F1 у товара нет, признак хранится в названии. Тип семян и фрагмент можно задать вместе — тогда оба условия обязательны.')
                    ->rule(function (Get $get) {
                        return function (string $attribute, mixed $value, Closure $fail) use ($get) {
                            if (blank($get('seed_type')) && blank($value)) {
                                $fail('Укажите тип семян или фрагмент названия.');
                            }
                        };
                    }),

                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Toggle::make('is_published')
                    ->label('Опубликована')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                TextColumn::make('seed_type')
                    ->label('Тип')
                    ->formatStateUsing(fn (?string $state) => BrandSubcategory::SEED_TYPES[$state] ?? '—'),

                TextColumn::make('name_token')
                    ->label('Фрагмент')
                    ->placeholder('—'),

                IconColumn::make('is_published')
                    ->label('Опубликована')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }
}
