<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'logo',
        'description',
        'contact_title',
        'contact_label',
        'contact_url',
        'groups',
        'socials',
    ];

    protected $casts = [
        'groups' => 'array',
        'socials' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'description' => static::defaultDescription(),
            'contact_title' => 'Свяжитесь с нами',
            'contact_label' => 'info@example.ru',
            'contact_url' => 'mailto:info@example.ru',
            'groups' => static::defaultGroups(),
            'socials' => static::defaultSocials(),
        ]);
    }

    public static function defaultDescription(): string
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Наш магазин — это качество, внимание и забота о каждом клиенте.';
    }

    public static function defaultGroups(): array
    {
        return [
            [
                'title' => 'Информация',
                'links' => [
                    ['title' => 'О компании', 'url' => '#'],
                    ['title' => 'Доставка и оплата', 'url' => '#'],
                    ['title' => 'Контакты', 'url' => '#'],
                ],
            ],
            [
                'title' => '',
                'links' => [
                    ['title' => 'Новости', 'url' => '#'],
                    ['title' => 'Каталог', 'url' => '#'],
                    ['title' => 'Помощь', 'url' => '#'],
                ],
            ],
            [
                'title' => 'Информация',
                'links' => [
                    ['title' => 'О компании', 'url' => '#'],
                    ['title' => 'Доставка и оплата', 'url' => '#'],
                ],
            ],
            [
                'title' => '',
                'links' => [
                    ['title' => 'Новости', 'url' => '#'],
                    ['title' => 'Каталог', 'url' => '#'],
                ],
            ],
        ];
    }

    public static function defaultSocials(): array
    {
        return [
            'telegram' => '#',
            'vk' => '#',
            'youtube' => '#',
        ];
    }
}
