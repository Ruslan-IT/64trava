<?php

namespace App\Filament\Resources\HeroBanners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HeroBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Изображения')
                    ->schema([

                        FileUpload::make('desktop_image')
                            ->label('Баннер для ПК')
                            ->image()
                            ->disk('public')
                            ->directory('images/hero-banners')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->required(),

                        FileUpload::make('mobile_image')
                            ->label('Баннер для мобильных')
                            ->image()
                            ->disk('public')
                            ->directory('images/hero-banners')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->required(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

               /* Section::make('Текст баннера')
                    ->schema([

                        TextInput::make('label')
                            ->label('Надпись')
                            ->placeholder('Бестселлер')
                            ->maxLength(255),

                        TextInput::make('title_new')
                            ->label('Первая строка заголовка')
                            ->placeholder('NEW')
                            ->maxLength(255),

                        TextInput::make('title_releases')
                            ->label('Вторая строка заголовка')
                            ->placeholder('releases')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(4)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),*/

                Section::make('Настройки')
                    ->schema([

                        TextInput::make('duration')
                            ->label('Время показа')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(5)
                            ->suffix('сек.')
                            ->required(),

                        TextInput::make('sort_order')
                            ->label('Порядок')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->helperText('Чем меньше число, тем раньше показывается баннер.'),

                        Toggle::make('is_active')
                            ->label('Баннер активен')
                            ->default(true),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

            ]);
    }
}
