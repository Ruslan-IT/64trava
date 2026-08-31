<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

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
                    ->columns(2),

                Section::make('SEO')
                    ->schema([

                        TextInput::make('seo_title')
                            ->label('SEO Заголовок')
                            ->maxLength(255)
                            ->nullable(),

                        Textarea::make('seo_description')
                            ->label('SEO Описание')
                            ->rows(5)
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
