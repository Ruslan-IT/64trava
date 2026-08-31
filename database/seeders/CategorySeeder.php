<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Автоцветущие',
                'description' => 'Автоцветущие сорта семян.',
            ],
            [
                'name' => 'Фотопериодные',
                'description' => 'Фотопериодные сорта семян.',
            ],
            [
                'name' => 'Регулярные',
                'description' => 'Регулярные сорта семян.',
            ],
            [
                'name' => 'Миксы семян',
                'description' => 'Миксы семян различных сортов.',
            ],
            [
                'name' => 'Крупные пачки',
                'description' => 'Товары с увеличенным количеством семян в упаковке.',
            ],
            [
                'name' => 'Наиболее популярные',
                'description' => 'Наиболее популярные товары каталога.',
            ],
            [
                'name' => 'Новинки',
                'description' => 'Новые товары в каталоге.',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                [
                    'name' => $category['name'],
                ],
                [
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                ]
            );
        }
    }
}
