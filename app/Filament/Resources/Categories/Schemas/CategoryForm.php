<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
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

                        TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn (string $state, Set $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label('URL / Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        RichEditor::make('description')
                            ->label('Описание')
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | ИЗОБРАЖЕНИЕ
                |--------------------------------------------------------------------------
                */

                Section::make('Изображение')
                    ->schema([

                        FileUpload::make('image')
                            ->label('Изображение категории')
                            ->image()
                            ->disk('public')
                            ->directory('images/categories')
                            ->visibility('public')
                            ->nullable()
                            ->columnSpanFull(),

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
                            ->placeholder('Например: Семена каннабиса — купить в магазине')
                            ->maxLength(255)
                            ->nullable(),

                        Textarea::make('seo_description')
                            ->label('SEO Описание')
                            ->placeholder('Краткое описание категории для поисковых систем')
                            ->rows(4)
                            ->nullable(),

                        Textarea::make('seo_keywords')
                            ->label('SEO Ключевые слова')
                            ->placeholder('семена, сорта, купить, каталог')
                            ->rows(3)
                            ->nullable(),

                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->collapsible(),

            ]);
    }
}
