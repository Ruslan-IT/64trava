<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsForm
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

                Section::make('Основная информация')
                    ->schema([

                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {

                                $set(
                                    'slug',
                                    Str::slug($state)
                                );

                            })
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('URL')
                            ->required()
                            ->maxLength(255)
                            ->helperText(
                                'Заполняется автоматически из заголовка. При необходимости можно изменить вручную.'
                            )
                            ->columnSpanFull(),

                        TextInput::make('category')
                            ->label('Категория')
                            ->maxLength(255),

                        Textarea::make('excerpt')
                            ->label('Краткое описание')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->label('Изображение')
                            ->image()
                            ->disk('public')
                            ->directory('news')
                            ->visibility('public')
                            ->required()
                            ->imageEditor()
                            ->columnSpanFull(),

                        DateTimePicker::make('published_at')
                            ->label('Дата публикации')
                            ->default(now())
                            ->required(),

                        TextInput::make('reading_time')
                            ->label('Время чтения')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->suffix('мин.')
                            ->readOnly(),

                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_published')
                            ->label('Опубликовано')
                            ->default(true),

                    ])
                    ->columns(),

                /*
                |--------------------------------------------------------------------------
                | СОДЕРЖАНИЕ
                |--------------------------------------------------------------------------
                */

                Section::make('Содержание новости')
                    ->schema([

                        RichEditor::make('content')
                            ->label('Текст новости')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {

                                $text = strip_tags($state ?? '');

                                $words = preg_split(
                                    '/\s+/u',
                                    trim($text)
                                );

                                $wordCount = count(
                                    array_filter($words)
                                );

                                $readingTime = max(
                                    1,
                                    (int) ceil($wordCount / 200)
                                );

                                $set(
                                    'reading_time',
                                    $readingTime
                                );

                            })
                            ->columnSpanFull(),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | SEO
                |--------------------------------------------------------------------------
                */

                Section::make('SEO')
                    ->schema([

                        TextInput::make('seo_title')
                            ->label('SEO Title')
                            ->maxLength(255)
                            ->helperText(
                                'Заголовок страницы для поисковых систем.'
                            ),

                        TextInput::make('seo_keywords')
                            ->label('SEO Keywords')
                            ->maxLength(500)
                            ->helperText(
                                'Ключевые слова через запятую.'
                            ),

                        Textarea::make('seo_description')
                            ->label('SEO Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

            ]);
    }
}

