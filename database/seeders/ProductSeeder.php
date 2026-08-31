<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Auto Mazar',
                'brand' => 'Dutch Passion',
                'description' => 'Популярный автоцветущий сорт с высокой урожайностью и характерным ароматом.',
                'price' => 1490,
                'old_price' => null,
                'image' => 'products/auto-mazar.jpg',
                'thc' => '20%',
                'seed_type' => 'A',
                'height' => '70-100 см',
                'advantages' => 'Высокая урожайность, быстрый цикл',
                'yield' => '400-500 г/м²',
                'rating' => 4.9,
                'is_recommended' => true,
                'is_new' => true,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 25,
                'categories' => ['Автоцветущие', 'Наиболее популярные', 'Новинки'],
                'tags' => ['Sativa', 'Mazar'],
            ],

            [
                'name' => 'Northern Lights',
                'brand' => 'Royal Queen Seeds',
                'description' => 'Известный сорт с компактным ростом, высокой стабильностью и насыщенным ароматом.',
                'price' => 1690,
                'old_price' => null,
                'image' => 'products/northern-lights.jpg',
                'thc' => '18%',
                'seed_type' => 'F',
                'height' => '80-120 см',
                'advantages' => 'Стабильность, компактный размер',
                'yield' => '450-550 г/м²',
                'rating' => 4.8,
                'is_recommended' => true,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 18,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['Northern Lights', 'Indica'],
            ],

            [
                'name' => 'Blueberry',
                'brand' => 'Dutch Passion',
                'description' => 'Классический сорт с характерным ягодным ароматом.',
                'price' => 1790,
                'old_price' => 1990,
                'image' => 'products/blueberry.jpg',
                'thc' => '19%',
                'seed_type' => 'F',
                'height' => '90-130 см',
                'advantages' => 'Яркий аромат, высокая урожайность',
                'yield' => '450-600 г/м²',
                'rating' => 4.7,
                'is_recommended' => true,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => true,
                'is_promo' => false,
                'stock' => 20,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['Blueberry', 'Skunk'],
            ],

            [
                'name' => 'White Widow',
                'brand' => 'Green House',
                'description' => 'Легендарный сорт с высокой стабильностью и выразительным ароматом.',
                'price' => 1590,
                'old_price' => null,
                'image' => 'products/white-widow.jpg',
                'thc' => '19%',
                'seed_type' => 'F',
                'height' => '90-140 см',
                'advantages' => 'Стабильность, высокая урожайность',
                'yield' => '400-550 г/м²',
                'rating' => 4.9,
                'is_recommended' => true,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => true,
                'stock' => 15,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['White Widow'],
            ],

            [
                'name' => 'OG Kush',
                'brand' => 'DNA Genetics',
                'description' => 'Популярный сорт с насыщенным ароматом и высокой концентрацией THC.',
                'price' => 1890,
                'old_price' => null,
                'image' => 'products/og-kush.jpg',
                'thc' => '21%',
                'seed_type' => 'F',
                'height' => '100-150 см',
                'advantages' => 'Высокий THC, насыщенный аромат',
                'yield' => '450-600 г/м²',
                'rating' => 4.8,
                'is_recommended' => true,
                'is_new' => true,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 12,
                'categories' => ['Фотопериодные', 'Наиболее популярные', 'Новинки'],
                'tags' => ['OG Kush', 'Kush'],
            ],

            [
                'name' => 'AK47',
                'brand' => 'Serious Seeds',
                'description' => 'Известный стабильный сорт с высокой урожайностью.',
                'price' => 1990,
                'old_price' => null,
                'image' => 'products/ak47.jpg',
                'thc' => '20%',
                'seed_type' => 'F',
                'height' => '100-140 см',
                'advantages' => 'Высокая урожайность, стабильность',
                'yield' => '500-650 г/м²',
                'rating' => 4.9,
                'is_recommended' => true,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 10,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['AK47', 'Sativa'],
            ],

            [
                'name' => 'Jack Herer',
                'brand' => 'Sensi Seeds',
                'description' => 'Классический сорт с ярким ароматом и выраженными характеристиками.',
                'price' => 1890,
                'old_price' => null,
                'image' => 'products/jack-herer.jpg',
                'thc' => '20%',
                'seed_type' => 'F',
                'height' => '100-150 см',
                'advantages' => 'Стабильность, выразительный аромат',
                'yield' => '450-600 г/м²',
                'rating' => 4.8,
                'is_recommended' => true,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 14,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['Jack Herer', 'Sativa'],
            ],

            [
                'name' => 'Girl Scout Cookies',
                'brand' => 'Barney\'s Farm',
                'description' => 'Популярный современный сорт с насыщенным ароматом.',
                'price' => 2190,
                'old_price' => null,
                'image' => 'products/girl-scout-cookies.jpg',
                'thc' => '22%',
                'seed_type' => 'F',
                'height' => '90-140 см',
                'advantages' => 'Высокий THC, насыщенный аромат',
                'yield' => '450-600 г/м²',
                'rating' => 4.9,
                'is_recommended' => true,
                'is_new' => true,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => true,
                'stock' => 9,
                'categories' => ['Фотопериодные', 'Наиболее популярные', 'Новинки'],
                'tags' => ['Girl Scout Cookies', 'Kush'],
            ],

            [
                'name' => 'Bruce Banner',
                'brand' => 'Barney\'s Farm',
                'description' => 'Высокоурожайный сорт с мощными характеристиками.',
                'price' => 2090,
                'old_price' => null,
                'image' => 'products/bruce-banner.jpg',
                'thc' => '23%',
                'seed_type' => 'F',
                'height' => '100-150 см',
                'advantages' => 'Высокий THC, высокая урожайность',
                'yield' => '500-650 г/м²',
                'rating' => 4.8,
                'is_recommended' => false,
                'is_new' => true,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 11,
                'categories' => ['Фотопериодные', 'Наиболее популярные', 'Новинки'],
                'tags' => ['Bruce Banner', 'Haze'],
            ],

            [
                'name' => 'Wedding Cake',
                'brand' => 'Dutch Passion',
                'description' => 'Современный сорт с выразительным ароматом и высокой урожайностью.',
                'price' => 1990,
                'old_price' => null,
                'image' => 'products/wedding-cake.jpg',
                'thc' => '21%',
                'seed_type' => 'F',
                'height' => '90-130 см',
                'advantages' => 'Высокая урожайность, насыщенный аромат',
                'yield' => '450-600 г/м²',
                'rating' => 4.7,
                'is_recommended' => false,
                'is_new' => true,
                'is_popular' => false,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 17,
                'categories' => ['Фотопериодные', 'Новинки'],
                'tags' => ['Wedding Cake', 'Kush'],
            ],

            [
                'name' => 'Gorilla',
                'brand' => 'Royal Queen Seeds',
                'description' => 'Сорт с высокой концентрацией THC и мощным ароматом.',
                'price' => 1890,
                'old_price' => null,
                'image' => 'products/gorilla.jpg',
                'thc' => '22%',
                'seed_type' => 'F',
                'height' => '100-140 см',
                'advantages' => 'Высокий THC, высокая урожайность',
                'yield' => '500-600 г/м²',
                'rating' => 4.8,
                'is_recommended' => true,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 13,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['Gorilla', 'Kush'],
            ],

            [
                'name' => 'LSD',
                'brand' => 'Barney\'s Farm',
                'description' => 'Характерный сорт с высокой стабильностью и насыщенным ароматом.',
                'price' => 1790,
                'old_price' => null,
                'image' => 'products/lsd.jpg',
                'thc' => '20%',
                'seed_type' => 'F',
                'height' => '90-130 см',
                'advantages' => 'Стабильность, высокая урожайность',
                'yield' => '450-550 г/м²',
                'rating' => 4.7,
                'is_recommended' => false,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 16,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['LSD', 'Haze'],
            ],

            [
                'name' => 'Mazar',
                'brand' => 'Dutch Passion',
                'description' => 'Стабильный сорт с высокой урожайностью и насыщенным ароматом.',
                'price' => 1690,
                'old_price' => null,
                'image' => 'products/mazar.jpg',
                'thc' => '19%',
                'seed_type' => 'F',
                'height' => '90-140 см',
                'advantages' => 'Высокая урожайность, стабильность',
                'yield' => '500-650 г/м²',
                'rating' => 4.8,
                'is_recommended' => true,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 19,
                'categories' => ['Фотопериодные', 'Наиболее популярные'],
                'tags' => ['Mazar', 'Kush'],
            ],

            [
                'name' => 'Zkittles',
                'brand' => 'Ethos Seeds',
                'description' => 'Современный сорт с ярким ароматическим профилем.',
                'price' => 2090,
                'old_price' => null,
                'image' => 'products/zkittles.jpg',
                'thc' => '21%',
                'seed_type' => 'F',
                'height' => '90-130 см',
                'advantages' => 'Яркий аромат, стабильный рост',
                'yield' => '450-600 г/м²',
                'rating' => 4.8,
                'is_recommended' => false,
                'is_new' => true,
                'is_popular' => false,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 8,
                'categories' => ['Фотопериодные', 'Новинки'],
                'tags' => ['Zkittles'],
            ],

            [
                'name' => 'Big Devil',
                'brand' => 'Sweet Seeds',
                'description' => 'Автоцветущий сорт с быстрым жизненным циклом.',
                'price' => 1490,
                'old_price' => null,
                'image' => 'products/big-devil.jpg',
                'thc' => '18%',
                'seed_type' => 'A',
                'height' => '70-110 см',
                'advantages' => 'Быстрый цикл, компактный размер',
                'yield' => '350-450 г/м²',
                'rating' => 4.6,
                'is_recommended' => false,
                'is_new' => false,
                'is_popular' => false,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 21,
                'categories' => ['Автоцветущие'],
                'tags' => ['Big Devil'],
            ],

            [
                'name' => 'Auto AK47',
                'brand' => 'Serious Seeds',
                'description' => 'Автоцветущая версия популярного сорта.',
                'price' => 1590,
                'old_price' => null,
                'image' => 'products/auto-ak47.jpg',
                'thc' => '19%',
                'seed_type' => 'A',
                'height' => '70-100 см',
                'advantages' => 'Быстрый цикл, стабильность',
                'yield' => '400-500 г/м²',
                'rating' => 4.7,
                'is_recommended' => false,
                'is_new' => true,
                'is_popular' => false,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 15,
                'categories' => ['Автоцветущие', 'Новинки'],
                'tags' => ['AK47', 'Sativa'],
            ],

            [
                'name' => 'Haze',
                'brand' => 'Delicious Seeds',
                'description' => 'Классический сорт с характерным ароматическим профилем.',
                'price' => 1790,
                'old_price' => null,
                'image' => 'products/haze.jpg',
                'thc' => '20%',
                'seed_type' => 'F',
                'height' => '120-160 см',
                'advantages' => 'Яркий аромат, высокая урожайность',
                'yield' => '450-600 г/м²',
                'rating' => 4.6,
                'is_recommended' => false,
                'is_new' => false,
                'is_popular' => false,
                'is_on_sale' => false,
                'is_promo' => false,
                'stock' => 14,
                'categories' => ['Фотопериодные'],
                'tags' => ['Haze', 'Sativa'],
            ],

            [
                'name' => 'Regular Mix',
                'brand' => 'Seedsman Seeds',
                'description' => 'Микс различных регулярных сортов.',
                'price' => 1290,
                'old_price' => null,
                'image' => 'products/regular-mix.jpg',
                'thc' => '18%',
                'seed_type' => 'R',
                'height' => '100-150 см',
                'advantages' => 'Разнообразие сортов, выгодная цена',
                'yield' => '400-550 г/м²',
                'rating' => 4.5,
                'is_recommended' => false,
                'is_new' => true,
                'is_popular' => false,
                'is_on_sale' => false,
                'is_promo' => true,
                'stock' => 30,
                'categories' => ['Регулярные', 'Миксы семян', 'Новинки'],
                'tags' => ['Skunk'],
            ],

            [
                'name' => 'Large Seed Mix',
                'brand' => 'Victory Seeds',
                'description' => 'Большая упаковка семян различных сортов.',
                'price' => 2490,
                'old_price' => 2790,
                'image' => 'products/large-seed-mix.jpg',
                'thc' => '19%',
                'seed_type' => 'R',
                'height' => '100-150 см',
                'advantages' => 'Большая упаковка, выгодная цена',
                'yield' => '450-600 г/м²',
                'rating' => 4.6,
                'is_recommended' => false,
                'is_new' => false,
                'is_popular' => true,
                'is_on_sale' => true,
                'is_promo' => true,
                'stock' => 40,
                'categories' => ['Крупные пачки', 'Миксы семян', 'Наиболее популярные'],
                'tags' => ['Skunk'],
            ],
        ];

        foreach ($products as $data) {

            /*
             * Получаем производителя
             */
            $brand = Brand::where('name', $data['brand'])->firstOrFail();

            /*
             * Запоминаем категории и теги
             */
            $categoryNames = $data['categories'];
            $tagNames = $data['tags'];

            /*
             * Убираем служебные поля,
             * которые не относятся непосредственно к products
             */
            unset(
                $data['brand'],
                $data['categories'],
                $data['tags']
            );

            /*
             * Создаём slug автоматически
             */
            $data['slug'] = Str::slug($data['name']);

            /*
             * Связываем товар с брендом
             */
            $data['brand_id'] = $brand->id;

            /*
             * Создаём товар или получаем существующий
             */
            $product = Product::firstOrCreate(
                [
                    'name' => $data['name'],
                ],
                $data
            );

            /*
             * Получаем категории
             */
            $categoryIds = Category::whereIn('name', $categoryNames)
                ->pluck('id');

            /*
             * Получаем теги
             */
            $tagIds = Tag::whereIn('name', $tagNames)
                ->pluck('id');

            /*
             * Привязываем категории и теги
             */
            $product->categories()->sync($categoryIds);
            $product->tags()->sync($tagIds);
        }
    }
}
