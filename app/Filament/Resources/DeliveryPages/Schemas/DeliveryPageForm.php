<?php

namespace App\Filament\Resources\DeliveryPages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeliveryPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок H1')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('intro')
                            ->label('Текст после H1')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Способ доставки')
                    ->schema([
                        TextInput::make('delivery_title')
                            ->label('Название раздела')
                            ->default('Способ доставки'),

                        Textarea::make('delivery_description')
                            ->label('Текст раздела')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Доставка — блок №1')
                    ->schema([
                        FileUpload::make('delivery_method_1_image')
                            ->label('Изображение')
                            ->image()
                            ->disk('public')
                            ->directory('delivery'),

                        TextInput::make('delivery_method_1_title')
                            ->label('Название'),

                        Textarea::make('delivery_method_1_text')
                            ->label('Текст')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Доставка — блок №2')
                    ->schema([
                        FileUpload::make('delivery_method_2_image')
                            ->label('Изображение')
                            ->image()
                            ->disk('public')
                            ->directory('delivery'),

                        TextInput::make('delivery_method_2_title')
                            ->label('Название'),

                        Textarea::make('delivery_method_2_text')
                            ->label('Текст')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Способ оплаты')
                    ->schema([
                        TextInput::make('payment_title')
                            ->label('Название раздела')
                            ->default('Способ оплаты'),

                        Textarea::make('payment_description')
                            ->label('Текст раздела')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Изображения способов оплаты')
                    ->schema([
                        FileUpload::make('payment_image_1')
                            ->label('Изображение №1')
                            ->image()
                            ->disk('public')
                            ->directory('delivery/payment'),

                        FileUpload::make('payment_image_2')
                            ->label('Изображение №2')
                            ->image()
                            ->disk('public')
                            ->directory('delivery/payment'),

                        FileUpload::make('payment_image_3')
                            ->label('Изображение №3')
                            ->image()
                            ->disk('public')
                            ->directory('delivery/payment'),
                    ])
                    ->columns(3),

                Section::make('Информационный блок №1')
                    ->schema([
                        TextInput::make('info_1_title')
                            ->label('Название'),

                        FileUpload::make('info_1_image')
                            ->label('Изображение')
                            ->image()
                            ->disk('public')
                            ->directory('delivery/info'),

                        Textarea::make('info_1_text')
                            ->label('Текст')
                            ->rows(7)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Информационный блок №2')
                    ->schema([
                        TextInput::make('info_2_title')
                            ->label('Название'),

                        FileUpload::make('info_2_image')
                            ->label('Изображение')
                            ->image()
                            ->disk('public')
                            ->directory('delivery/info'),

                        Textarea::make('info_2_text')
                            ->label('Текст')
                            ->rows(7)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO Title')
                            ->maxLength(255),

                        Textarea::make('seo_description')
                            ->label('SEO Description')
                            ->rows(3),

                        Textarea::make('seo_keywords')
                            ->label('SEO Keywords')
                            ->rows(3),
                    ])
                    ->columns(1),
            ]);
    }
}
