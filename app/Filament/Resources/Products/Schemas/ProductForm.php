<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Repeater;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | ОСНОВНАЯ ИНФОРМАЦИЯ
                |--------------------------------------------------------------------------
                */

                Group::make()
                    ->schema([

                        Section::make('Основная информация')
                            ->schema([

                                Select::make('brand_id')
                                    ->label('Производитель')
                                    ->options(fn () => Brand::query()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('name')
                                    ->label('Название')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn (string $state, $set) =>
                                        $set(
                                            'slug',
                                            \Illuminate\Support\Str::slug($state)
                                        )
                                    ),

                                TextInput::make('slug')
                                    ->label('URL / Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Select::make('categories')
                                    ->label('Категории')
                                    ->relationship('categories', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('tags')
                                    ->label('Теги')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),

                                RichEditor::make('description')
                                    ->label('Описание')
                                    ->columnSpanFull(),

                                RichEditor::make('full_description')
                                    ->label('Общее описание сорта')
                                    ->nullable()
                                    ->columnSpanFull(),

                            ])
                            ->columns(2),

                        /*
                        |--------------------------------------------------------------------------
                        | ЦЕНА И НАЛИЧИЕ
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Цена и наличие')
                            ->schema([

                                TextInput::make('price')
                                    ->label('Цена')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),

                                TextInput::make('old_price')
                                    ->label('Старая цена')
                                    ->numeric()
                                    ->prefix('$')
                                    ->nullable(),

                                TextInput::make('stock')
                                    ->label('Остаток')
                                    ->required()
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('rating')
                                    ->label('Рейтинг')
                                    ->numeric()
                                    ->step(0.1)
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->default(0),

                            ])
                            ->columns(2),

                        /*
                        |--------------------------------------------------------------------------
                        | Фасовка
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Фасовки')
                            ->schema([

                                Repeater::make('variants')
                                    ->relationship('variants')
                                    ->label('Варианты товара')
                                    ->schema([

                                        TextInput::make('package_size')
                                            ->label('Фасовка')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(1)
                                            ->required()
                                            ->suffix('шт.'),

                                        TextInput::make('sku')
                                            ->label('Артикул')
                                            ->required()
                                            ->maxLength(100),

                                        TextInput::make('price')
                                            ->label('Цена')
                                            ->numeric()
                                            ->required()
                                            ->prefix('$'),

                                        TextInput::make('old_price')
                                            ->label('Старая цена')
                                            ->numeric()
                                            ->nullable()
                                            ->prefix('$'),

                                        TextInput::make('stock')
                                            ->label('Остаток')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0)
                                            ->default(0)
                                            ->required(),

                                    ])
                                    ->columns(5)
                                    ->addActionLabel('Добавить фасовку')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(function (array $state): ?string {
                                        if (empty($state['package_size'])) {
                                            return 'Новая фасовка';
                                        }

                                        return $state['package_size'] . ' шт.';
                                    })
                                    ->columnSpanFull(),

                            ])
                            ->columnSpanFull()
                            ->collapsible(),


                        /*
                        |--------------------------------------------------------------------------
                        | ХАРАКТЕРИСТИКИ
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Характеристики')
                            ->schema([

                                TextInput::make('thc')
                                    ->label('ТГК')
                                    ->placeholder('30-33%')
                                    ->nullable(),

                                TextInput::make('taste_aroma')
                                    ->label('Вкус и аромат')
                                    ->placeholder('Сладкий, фруктовый, цитрусовый')
                                    ->nullable(),

                                TextInput::make('country')
                                    ->label('Страна')
                                    ->placeholder('США')
                                    ->nullable(),

                                TextInput::make('effect')
                                    ->label('Эффект')
                                    ->placeholder('Расслабляющий, бодрящий')
                                    ->nullable(),

                                TextInput::make('flowering')
                                    ->label('Цветение')
                                    ->placeholder('8-10 недель')
                                    ->nullable(),

                                TextInput::make('genetics')
                                    ->label('Генетика')
                                    ->placeholder('Indica / Sativa / Hybrid')
                                    ->nullable(),

                                Select::make('seed_type')
                                    ->label('Тип семян')
                                    ->options([
                                        'A' => 'Автоцветущие',
                                        'F' => 'Фотопериодные',
                                        'R' => 'Регулярные',
                                    ])
                                    ->required(),

                                TextInput::make('height')
                                    ->label('Высота')
                                    ->placeholder('70-100 см')
                                    ->nullable(),

                                TextInput::make('yield')
                                    ->label('Урожайность')
                                    ->placeholder('400-500 г/м²')
                                    ->nullable(),

                                Textarea::make('advantages')
                                    ->label('Преимущества')
                                    ->rows(3)
                                    ->nullable()
                                    ->columnSpanFull(),


                            ])
                            ->columns(2),

                        Section::make('Вопрос-ответ')
                            ->schema([

                                Repeater::make('faqs')
                                    ->relationship()
                                    ->label('Вопросы и ответы')
                                    ->schema([

                                        TextInput::make('question')
                                            ->label('Вопрос')
                                            ->required()
                                            ->maxLength(255),

                                        RichEditor::make('answer')
                                            ->label('Ответ')
                                            ->required()
                                            ->columnSpanFull(),

                                        TextInput::make('sort_order')
                                            ->label('Порядок')
                                            ->numeric()
                                            ->default(0)
                                            ->hidden(),

                                    ])
                                    ->columns(1)
                                    ->orderColumn('sort_order')
                                    ->addActionLabel('Добавить вопрос')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string =>
                                        $state['question'] ?? null
                                    )
                                    ->columnSpanFull(),

                            ])
                            ->columnSpanFull()
                            ->collapsible(),

                        /*
                        |--------------------------------------------------------------------------
                        | ФЛАГИ
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Флаги')
                            ->schema([

                                /*Toggle::make('is_recommended')
                                    ->label('Рекомендуем'),

                                Toggle::make('is_new')
                                    ->label('Новинка'),

                                Toggle::make('is_popular')
                                    ->label('Популярный'),*/

                                Toggle::make('is_on_sale')
                                    ->label('Акция'),

                                Toggle::make('is_promo')
                                    ->label('Показывать как промо'),

                            ])
                            ->columns(5),

                        /*
                        |--------------------------------------------------------------------------
                        | ИЗОБРАЖЕНИЕ
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Изображения')
                            ->schema([

                                FileUpload::make('image')
                                    ->label('Главное изображение')
                                    ->image()
                                    ->disk('public')
                                    ->directory('images/products')
                                    ->visibility('public')
                                    ->nullable()
                                    ->columnSpanFull(),

                                FileUpload::make('gallery')
                                    ->label('Галерея изображений')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->disk('public')
                                    ->directory('images/products/gallery')
                                    ->visibility('public')
                                    ->nullable()
                                    ->columnSpanFull(),

                            ])
                            ->columns(1),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | SEO
                |--------------------------------------------------------------------------
                */

                Section::make('SEO')
                    ->schema([

                        TextInput::make('seo_title')
                            ->label('SEO Заголовок')
                            ->maxLength(255)
                            ->nullable(),

                        Textarea::make('seo_description')
                            ->label('SEO Описание')
                            ->rows(4)
                            ->nullable(),

                        Textarea::make('seo_keywords')
                            ->label('SEO Ключевые слова')
                            ->rows(3)
                            ->nullable(),

                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->collapsible(),

            ]);
    }
}
