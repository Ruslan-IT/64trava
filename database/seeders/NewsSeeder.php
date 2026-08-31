<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        News::create([
            'title' => 'Новая коллекция уже доступна в нашем магазине',

            'slug' => 'novaya-kollekciya-uzhe-dostupna-v-nashem-magazine',

            'category' => 'Новости',

            'image' => 'news/news-1.jpg',

            'excerpt' => 'Мы рады представить новую коллекцию товаров, которая уже доступна в нашем магазине.',

            'content' => '
                <p>
                    Мы рады представить новую коллекцию товаров,
                    которая уже доступна в нашем магазине.
                </p>

                <p>
                    В новой коллекции мы собрали популярные позиции
                    и несколько интересных новинок. Каждый товар был
                    выбран с особым вниманием к качеству и характеристикам.
                </p>

                <p>
                    Мы постоянно работаем над расширением ассортимента,
                    чтобы вы могли находить подходящие товары и получать
                    максимум удовольствия от покупок.
                </p>

                <p>
                    Следите за обновлениями нашего каталога, чтобы не
                    пропустить новые поступления, специальные предложения
                    и другие интересные новости нашего магазина.
                </p>
            ',

            'views' => 24000,

            'reading_time' => 2,

            'published_at' => '2025-07-10 12:00:00',

            'is_published' => true,

            'sort_order' => 1,
        ]);

        News::create([
            'title' => 'Автоцветы от Humboldt Seed Company',

            'slug' => 'avtocvety-ot-humboldt-seed-company',

            'category' => 'Новости',

            'image' => 'news/news-2.jpg',

            'excerpt' => 'Рассказываем о новых автоцветущих сортах, которые появились в нашем ассортименте.',

            'content' => '
                <p>
                    В нашем магазине появились новые автоцветущие сорта
                    от Humboldt Seed Company.
                </p>

                <p>
                    Мы добавили несколько интересных позиций в каталог,
                    которые уже доступны для заказа.
                </p>

                <p>
                    Следите за обновлениями магазина — мы регулярно
                    добавляем новые товары.
                </p>
            ',

            'views' => 18500,

            'reading_time' => 2,

            'published_at' => '2025-07-10 12:00:00',

            'is_published' => true,

            'sort_order' => 2,
        ]);

        News::create([
            'title' => 'Новые поступления в каталоге',

            'slug' => 'novye-postupleniya-v-kataloge',

            'category' => 'Новости',

            'image' => 'news/news-3.jpg',

            'excerpt' => 'В каталоге появились новые товары и интересные позиции.',

            'content' => '
                <p>
                    Мы обновили каталог и добавили новые товары.
                </p>

                <p>
                    Теперь в магазине доступно ещё больше интересных
                    позиций для выбора.
                </p>
            ',

            'views' => 12300,

            'reading_time' => 1,

            'published_at' => '2025-07-08 12:00:00',

            'is_published' => true,

            'sort_order' => 3,
        ]);

        News::create([
            'title' => 'Обновление ассортимента',

            'slug' => 'obnovlenie-assortimenta',

            'category' => 'Новости',

            'image' => 'news/news-4.jpg',

            'excerpt' => 'Расширяем ассортимент и добавляем новые позиции.',

            'content' => '
                <p>
                    Мы продолжаем расширять ассортимент нашего магазина.
                </p>

                <p>
                    Новые позиции уже доступны в каталоге.
                </p>
            ',

            'views' => 9700,

            'reading_time' => 2,

            'published_at' => '2025-07-05 12:00:00',

            'is_published' => true,

            'sort_order' => 4,
        ]);
    }
}
