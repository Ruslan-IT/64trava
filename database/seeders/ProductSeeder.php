<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // БРЕНД
        // ============================================

        $barneys = Brand::firstOrCreate(
            ['slug' => 'barneys-farm'],
            ['name' => "Barney's Farm"]
        );

        // ============================================
        // КАТЕГОРИИ
        // ============================================

        $cats = [
            'feminised' => Category::firstOrCreate(
                ['slug' => 'feminised'],
                ['name' => 'Феминизированные']
            ),

            'autoflower' => Category::firstOrCreate(
                ['slug' => 'autoflower'],
                ['name' => 'Автоцветущие']
            ),

            'regular' => Category::firstOrCreate(
                ['slug' => 'regular'],
                ['name' => 'Регулярные']
            ),

            'new' => Category::firstOrCreate(
                ['slug' => 'novinki'],
                ['name' => 'Новинки']
            ),

            'popular' => Category::firstOrCreate(
                ['slug' => 'populyarnye'],
                ['name' => 'Популярные']
            ),

            'sale' => Category::firstOrCreate(
                ['slug' => 'akcii'],
                ['name' => 'Акции']
            ),
        ];

        // ============================================
        // ЦЕНЫ ПО УПАКОВКАМ
        // ============================================

        $packPrices = [
            1 => [
                'price' => 1500,
                'old' => null,
            ],

            20 => [
                'price' => 4700,
                'old' => null,
            ],

            50 => [
                'price' => 10200,
                'old' => null,
            ],

            100 => [
                'price' => 18700,
                'old' => null,
            ],
        ];

        // ============================================
        // ДАННЫЕ ПРОДУКТОВ
        //
        // name|thc|type|sat|ind|genetics|h_in|y_in|flower|h_out|y_out|harvest|is_new|is_popular
        // ============================================

        $csv = <<<'CSV'
Acapulco Gold|26|F|70|30|Central American|100-110|600-700|60-70|150-200|1500|October 2nd-3rd week|0|1
AK|26|F|70|30|Mexican x Colombian x Thai x Afghan|100-140|500|60-70|120-160|650|October 2nd-3rd week|0|1
Amnesia Haze|24|F|80|20|Jamaican x Thai x South Asia x Cambodia|100-140|650|70-85|200|1000|October 3rd-4th week|0|1
Amnesia Lemon|26|F|60|40|Amnesia Haze x Lemon Skunk|100-110|500-600|60-65|120-150|800|October 1st-2nd week|0|1
Apple Fritter|30|F|50|50|Animal Cookies x Sour Apple|90-110|650|65-70|150-180|1000|October 2nd-3rd week|1|0
Ayahuasca Purple|24|F|0|100|Red River Delta x Master Kush|90-110|500-600|55-65|120-150|800|September 3rd-4th week|0|0
Banana Runtz F1|30|F|50|50|Runtz x Banana OG|100-120|550|60-65|150-220|700|October 1st-2nd week|1|1
Biscotti|29|F|20|80|Gelato x Girl Scout Cookies x OG Kush|90-110|600-700|65-70|150-200|1500|October 2nd-3rd week|0|1
Biscotti Mintz|30|F|20|80|Biscotti x Mintz|80-110|600-700|56-63|120-200|1500|October 1st-2nd week|0|1
Black Cherry Gushers|32|F|40|60|Acai x Black Cherry Funk|100-120|650|70-75|120-180|2000|October 3rd-4th week|1|0
Blue Cheese|26|F|20|80|Original Cheese x Blueberry|80-110|500-600|60-65|120-150|800|October 1st-2nd week|0|1
Blue Dream|28|F|60|40|Blueberry x Super Silver Haze|100-120|650|65-70|400|3000|October 2nd-3rd week|0|1
Blue Gelato 41|28|F|40|60|Blueberry x Thin Mint GSC x Sunset Sherbert|110-150|700-800|63-70|150-200|2500-3000|October 2nd-3rd week|0|1
Blue Sunset Sherbert|28|F|35|65|Sunset Sherbet x Purple Punch|100-120|550-650|59-63|150-200|1000|October 1st-2nd week|0|1
Bruce Banner|32|F|60|40|Strawberry Diesel x OG Kush|100-150|650|65-70|250|1500|October 2nd-3rd week|0|1
Bubba Kush|29|F|30|70|Afghan Kush Landrace x OG Kush|100-120|600-700|56-63|150-200|1500|October 1st-2nd week|0|1
Bubblegum Gelato|32|F|40|60|Gelato 45 x Indiana Bubble Gum|120|550|60-70|225|1000|October 1st-2nd week|1|0
Cherry Poppers|31|F|60|40|Lemon Kush x Cherry Z|100-120|650|60-65|200|2000|October 1st-2nd week|1|0
Cookie Casket|30|F|30|70|The Hearse x Thin Mint Cookies|120|650|63-70|250|2000|October 2nd-3rd week|0|0
Critical Kush|26|F|0|100|Critical Mass x OG Kush|100-110|550-650|55-60|120-150|1000|September 3rd-4th week|0|1
Dos Si Dos 33|28|F|40|60|Dos Si Dos x Gelato #33|90-110|650-750|60-65|150-200|1500-2000|October 1st-2nd week|0|1
Durban Poison F1|27|F|100|0|South African Landrace|150-200|600|56-65|200-350|700|October 1st-2nd week|1|0
Durban Z F1|27|F|70|30|Durban Poison x Original Z|110-150|550|56-65|180-250|750|October 1st-2nd week|1|0
G13 Haze|23|F|80|20|G13 x Hawaiian Sativa|100-110|500-600|65-70|120-150|800|October 2nd-3rd week|0|0
Garlic Cookies|30|F|10|90|Girl Scout Cookies x Chemdawg|100-120|600|63-70|150-250|1500|October 2nd-3rd week|0|1
Gary Payton|27|F|55|45|Y Griega x Snowman|80-110|600-700|65-70|150-180|1500|October 2nd-3rd week|0|1
Gelato|28|F|40|60|Blue Sunset Sherbert x Thin Mint GSC|100-120|550-650|65-70|180-250|1500|October 2nd-3rd week|0|1
Gelato #45|27|F|40|60|Sunset Sherbet x Thin Mint GSC|100-120|650-750|65-70|180-250|1500-2000|October 1st-2nd week|0|1
Girl Scout Cookies|28|F|30|70|OG Kush x Durban Poison|100-120|600-700|60-65|150-200|1500|October 1st-2nd week|0|1
Glookies|32|F|30|70|Gorilla Glue x Thin Mint GSC|100-120|700-800|60-70|120-180|2000-3000|October 1st-2nd week|0|1
Gorilla Z|32|F|40|60|Gorilla Glue x Original Z Strain|100-140|700-800|55-65|140-180|2000-2500|September 3rd-4th week|0|1
GrandDaddy Purple|25|F|30|70|Purple Urkle x Big Bud|100|600|55-65|180|1000|October 2nd-3rd week|0|1
Hindu Kush|26|F|0|100|Hindu Kush Mountains landrace|80-120|550-600|55-65|200|1000|October 1st-2nd week|0|1
Ice Cream Cake|27|F|30|70|Gelato #33 x Wedding Cake|90-110|600-700|55-65|150-200|1500-2000|September 3rd-4th week|0|0
Insane OG|32|F|25|75|OG Kush x Bubba Kush x Granddaddy Purple|100-120|600|55-65|150-250|2000|September 3rd-4th week|1|1
Jealousy|28|F|50|50|Sherbert Bx1 x Gelato 41|120-150|700|65-70|220|2000|October 2nd-3rd week|0|0
Jelly Cake|27|F|20|80|Biscotti x Sunset Sherbet|100-120|400-500|56-63|180-220|700|September 2nd-3rd week|1|0
Laughing Buddha|24|F|80|20|Thai x Jamaican|100-110|500-600|70-75|120-150|800|October 3rd-4th week|0|0
Lemon Cherry Gelato|33|F|40|60|Sunset Sherbet x Girl Scout Cookies|90-110|600|60-65|140|1000|October 2nd-3rd week|1|0
Lemon Tree|26|F|45|55|Lemon Skunk x Sour Diesel|90-100|600-700|65-70|150-200|1500|October 3rd-4th week|0|1
LFG|32|F|40|60|Lemon Cherry Gelato x Gelato 41|100-110|550|60-65|120-150|1000|October 2nd-3rd week|1|0
Liberty Haze|26|F|60|40|G13 x ChemDawg 91|100-110|500-600|60-65|120-150|800|October 1st-2nd week|0|0
Limoncello|28|F|80|20|Original Lemonnade x Cherry Pie|80-120|600|65-70|140-200|1500-2000|October 2nd-3rd week|0|0
London Pound Cake F1|30|F|30|70|Rainbow Sherbet x GSC Indica Pheno|120-150|650|55-65|150-180|700|September 2nd-3rd week|1|0
LSD|28|F|30|70|Original Skunk #1 x Afghan Indica|90-110|550-650|60-65|120-150|800|October 1st-2nd week|0|1
MAC 1 F1|27|F|50|50|Alien Cookies x Miracle 15|120-160|600|63-70|180-250|700|October 1st-2nd week|1|0
Mimosa EVO|32|F|40|60|Mimosa x Orange Punch|100-120|700-800|65-70|180-220|2000|October 3rd-4th week|0|1
Mimosa x Orange Punch|30|F|35|65|Mimosa Evo x Orange Punch|90-110|650-750|55-60|150-180|1500|September 3rd-4th week|0|1
Moby Dick|27|F|75|25|G13 Haze x White Widow|120-140|650-750|63-70|250-350|1500-2000|October 3rd-4th week|0|1
Northern Lights|26|F|0|100|Thai landrace x Afghani|90-110|550|55-60|150-200|750|September 3rd-4th week|0|1
OG Kush|26|F|30|70|Chemdawg x Lemon Thai x Hindu Kush|100-120|600-700|55-65|150-200|1500-2000|September 3rd-4th week|0|1
Papaya Frosting|28|F|30|70|Papayamosa x Banana Frosting|100-110|600|60-65|150-180|1500|October 1st-2nd week|1|0
Pineapple Chunk|28|F|20|80|Pineapple x Skunk #1 x Cheese|90-110|550-650|55-60|120-150|1000|September 3rd-4th week|0|1
Pineapple Express|28|F|60|40|Hawaiian Landrace Sativa x Trainwreck|120-140|600-700|60-65|180-250|1500-2000|October 1st-2nd week|0|1
Purple Punch|30|F|10|90|Granddaddy Purple x Larry OG|100-110|650-750|50-60|120-150|2000|September 3rd-4th week|0|1
Purple Punch x Lemon Drizzle|28|F|45|55|Purple Punch x Lemon Drizzle|100-120|550-650|60-65|150-180|1500|October 1st-2nd week|0|0
Rainbow Sherbet|28|F|50|50|Champagne x Blackberry|100-120|600|56-63|150|1000|September 3rd-4th week|0|0
RS11 F1|27|F|30|70|Pink Guava x OZK|100-120|650|56-65|150-200|800|September 3rd-4th week|1|0
RS11 x Banana OG|32|F|30|70|RS11 x Banana OG|120-150|600|65-70|150-180|1500|October 3rd-4th week|1|0
Runtz|29|F|50|50|Original Z x Gelato|90-110|550-650|55-60|150-180|1500|September 3rd-4th week|0|1
Runtz Muffin|29|F|30|70|Original Z x Gelato #33 x Orange Punch|100-120|550-650|56-63|150-200|1000|October 1st-2nd week|0|1
Runtz x Layer Cake|28|F|50|50|Runtz x Layer Cake|100-130|700-800|65-70|150-200|2500|October 1st-2nd week|0|0
Shiskaberry|28|F|0|100|Blueberry x Afghan|80-110|600-700|50-55|120-180|1500-2000|September 2nd-3rd week|0|0
Skunk #1|26|F|30|70|Afghan x Acapulco Gold x Colombian Gold|120-150|500|65-75|150-200|800|October 3rd-4th week|0|1
Sour Diesel|26|F|70|30|Super Skunk x Chemdawg|100-120|600-700|70-75|150-200|1500-2000|October 2nd-3rd week|0|1
Sour Strawberry|27|F|60|40|Strawberry Kush x Sour Diesel|100-120|600-700|65-70|150-200|1500-2000|October 1st-2nd week|0|0
Strawberry Lemonade|26|F|60|40|Strawberry x Lemon OG|120-150|700-800|60-70|150-250|2500|October 1st-2nd week|0|0
Super Boof|32|F|50|50|Black Cherry Punch x Tropicana Cookies|100-120|500|56-63|150-180|1000|October 1st-2nd week|1|0
Tangerine Dream|23|F|60|40|G13 x Afghan x Neville's A5 Haze|90-110|500-600|60-65|120-150|800|October 1st-2nd week|0|0
Thin Mint Frosting F1|30|F|30|70|Thin Mint x Banana Frosting|100-140|550|56-63|150-180|700|October 1st-2nd week|1|0
Trainwreck|28|F|80|20|Mexican Sativa x Thai Sativa x Afghani Indica|100-150|650|60-65|150-300|2000|September 3rd-4th week|0|1
Tropicana Cherry|28|F|60|40|Tropicana Cookies x Tropicanna Cherry|100-140|550-650|60-65|120-180|1000|October 1st-2nd week|0|1
Tropicanna Banana|26|F|60|40|Tropicanna x Banana Kush|100-110|650-750|65-70|120-180|2000|October 2nd-3rd week|0|0
Watermelon Z|28|F|45|55|Watermelon x Original Z Strain|120-140|500-600|63|150-200|1000|September 3rd-4th week|0|0
Wedding Cake|27|F|20|80|Cherry Pie x Girl Scout Cookies x OG Kush|90-100|600-700|55-60|180-200|1500|September 3rd-4th week|0|1
White Runtz|29|F|45|55|Gelato x Original Z Strain|120-140|550|56-70|225|1500|October 2nd-3rd week|0|0
White Widow XXL|28|F|25|75|Brazilian x South Indian|100-120|600-700|60-65|150-200|2000|October 1st-2nd week|0|1
Zillions|32|F|45|55|Original Zkittlez x Lemon Cherry Gelato|100-120|650|60-65|150-180|2500|October 1st-2nd week|1|0
Zoap|29|F|50|50|Pink Guava #16 x Rainbow Sherbet|120|600|63-70|200|1000|October 2nd-3rd week|0|0
Afghan Hash Plant Auto|22|A|10|90|Afghan x BF Super Auto #1|80-100|400-450|75-80|100-120|180-250|75-80 days from seed|0|0
Amnesia Haze Auto|23|A|80|20|Amnesia Haze x BF Super Auto #1|90-110|500-600|65-70|100-140|230-300|65-70 days from seed|0|1
Bruce Banner Auto|25|A|60|40|Bruce Banner x BF Super Auto #1|100-150|450-550|80-90|120-150|150-220|80-90 days from seed|0|0
Bubblegum Auto F1|21|A|15|85|Bubblegum x BF Super Auto #1|80-130|550-650|70-75|110-150|150-250|70-75 days from seed|1|0
Cherry Cola Auto|25|A|35|65|Cherry Cola x BF Super Auto #1|90-130|500-600|70-80|100-150|150-250|70-80 days from seed|1|0
Dos Si Dos Auto|23|A|35|65|Dos Si Dos x BF Super Auto #1|80-110|550-650|70-75|100-140|280-350|70-75 days from seed|0|1
Frosted Zinn x 3 Bears OG Auto|24|A|30|70|Frosted Zinn Auto x 3 Bears Auto|90-120|450-500|70-75|120-140|280-350|70-75 days from seed|0|0
Frosted Zinn x Cookie Dog Auto|25|A|30|70|Frosted Zinn Auto x Cookie Dog Auto|90-110|500-550|70-75|100-120|230-300|70-75 days from seed|0|0
GG4 Auto|26|A|45|55|GG4 x BF Super Auto #1|80-100|500-600|70-75|100-130|230-300|70-75 days from seed|0|1
Glue Gelato Auto|26|A|40|60|Gorilla Glue x Gelato x BF Super Auto #1|90-120|450-500|65-70|100-130|130-200|65-70 days from seed|0|1
Gorilla Z Auto|25|A|25|75|Gorilla Glue x Original Z x BF Super Auto #1|80-100|450-500|75-80|90-120|130-200|75-80 days from seed|0|0
Lemon Cherry Cookies Auto|28|A|30|70|Lemon Cherry Cookies x BF Super Auto #1|80-110|500-600|65-75|100-120|130-220|65-75 days from seed|1|0
Lemon Haze Auto|24|A|70|30|Lemon Haze x BF Super Auto #1|90-110|450-550|65-70|100-120|180-250|65-70 days from seed|0|0
London Pound Cake Auto F1|28|A|70|30|London Pound Cake x BF Super Auto #1|90-130|550-650|75-80|110-160|280-350|75-80 days from seed|1|0
LSD Auto|24|A|30|70|LSD x Super Magnum Auto|80-100|550-650|65-70|100-130|280-350|65-70 days from seed|0|1
Mimosa x Orange Punch Auto|24|A|40|60|Mimosa Evo x Orange Punch x BF Super Auto #1|80-100|550-650|65-70|100-140|280-350|65-70 days from seed|0|0
Moby Dick Auto|23|A|70|30|Moby Dick x BF Super Auto #1|90-110|500-600|65-70|100-130|230-300|65-70 days from seed|0|1
OG Kush Auto|24|A|20|80|OG Kush x BF Super Auto #1|90-110|450-550|70-75|100-120|180-250|70-75 days from seed|0|1
Original Z OG Auto|24|A|50|50|Original Z x OG Kush x BF Super Auto #1|80-100|550-650|70-75|100-130|280-350|70-75 days from seed|0|0
Pineapple Express Auto|24|A|30|70|Pineapple Chunk x BF Super Auto #1|90-110|500-600|65-70|100-120|230-300|65-75 days from seed|0|0
Purple Punch Auto|22|A|20|80|Purple Punch x BF Auto Critical|80-100|450-550|65-70|90-120|180-250|65-70 days from seed|0|0
Runtz Auto|27|A|30|70|Runtz x BF Super Auto #1|80-100|500-600|65-70|110-140|230-300|65-70 days from seed|0|1
Skywalker OG Auto|24|A|25|75|Skywalker OG x BF Super Auto #1|80-100|450-500|70-75|90-120|130-200|70-75 days from seed|0|0
Sour Diesel Auto|24|A|60|40|Sour Diesel x BF Super Auto #1|90-120|500-600|65-70|100-140|230-300|65-70 days from seed|0|0
Strawberry Cheesecake Auto|23|A|25|75|Strawberry Pie x Afghan OG x BF Super Auto #1|90-110|550-650|75-80|100-140|280-350|75-80 days from seed|0|0
Tangerine Dream Auto|21|A|60|40|Tangerine Dream x Autoflower #1|70-90|450-500|70-75|80-110|130-200|70-75 days from seed|0|0
Thin Mint x Sour Pinot Auto|26|A|60|40|Thin Mint Auto x Sour Pinot Auto|90-120|450-500|70-75|110-140|180-250|70-75 days from seed|0|0
Trainwreck Auto|24|A|80|20|Trainwreck x BF Super Auto #1|80-120|450-550|75-85|120-150|150-220|75-85 days from seed|0|0
Tropicana Cookies Auto F1|27|A|60|40|Tropicana Cookies x BF Super Auto #1|90-110|400-500|70-75|100-120|150-250|70-75 days from seed|1|0
Watermelon Z Auto|26|A|40|60|Watermelon Z x BF Super Auto #1|90-110|450-550|70-75|120-140|180-250|70-75 days from seed|0|0
Wedding Cake Auto|26|A|35|65|Wedding Cake x BF Super Auto #1|80-100|450-550|70-75|100-120|180-250|70-75 days from seed|0|1
White Widow XXL Auto|23|A|30|70|Brazilian x South Indian x BF Super Auto #1|80-100|450-550|70-75|100-120|180-250|70-75 days from seed|0|0
Acapulco Gold Regular|22|R|70|30|Central American|100-110|600-700|60-70|150-200|1500|October 2nd-3rd week|0|0
Afghan Hash Plant Regular|21|R|0|100|Afghan|80-100|500|50-60|150-180|700|September 3rd-4th week|0|0
G13 Haze Regular|23|R|80|20|G13 x Hawaiian Sativa|100-110|600|65-75|150-180|800|October 2nd-3rd week|0|0
Hindu Kush Regular|26|R|0|100|Hindu Kush Mountains landrace|100-120|550-600|55-65|150-200|1000|October 1st-2nd week|0|0
Master Kush Regular|24|R|0|100|Hindu Kush Mountain Range|80-120|450-550|55-65|150-200|750|October 1st-2nd week|0|0
Northern Lights Regular|26|R|0|100|Thai Landrace x Afghan|90-110|550|55-60|150-200|750|September 3rd-4th week|0|0
Skunk #1 Regular|26|R|30|70|Afghan x Acapulco Gold x Colombian Gold|120-150|500|65-75|150-200|800|October 3rd-4th week|0|0
White Widow Regular|26|R|25|75|Brazilian x South Indian|100-120|600-700|60-65|150-200|2000|September 3rd-4th week|0|0
CSV;

        // ============================================
        // ОБРАБОТКА ТОВАРОВ
        // ============================================

        $lines = explode("\n", trim($csv));

        $loadedProducts = 0;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $p = explode('|', $line);

            if (count($p) < 14) {
                $this->command->warn(
                    'Пропущена строка: ' . $line
                );

                continue;
            }

            [
                $name,
                $thc,
                $type,
                $sat,
                $ind,
                $genetics,
                $hIn,
                $yIn,
                $flower,
                $hOut,
                $yOut,
                $harvest,
                $isNew,
                $isPopular
            ] = $p;

            // ============================================
            // SLUG
            // ============================================

            $slug = Str::slug($name);

            // ============================================
            // ТИП СЕМЯН
            // ============================================

            $seedType = match ($type) {
                'A' => 'A',
                'F' => 'F',
                'R' => 'R',
                default => 'F',
            };

            $seedTypeName = match ($seedType) {
                'A' => 'Автоцветущий',
                'F' => 'Феминизированный',
                'R' => 'Регулярный',
                default => 'Феминизированный',
            };

            // ============================================
            // ОПИСАНИЕ
            // ============================================

            $description =
                "Сорт {$name} от Barney's Farm.\n\n"
                . "Генетика: {$genetics}.\n"
                . "Тип: {$seedTypeName}.\n"
                . "Сативы: {$sat}% / Индики: {$ind}%.\n"
                . "ТГК: {$thc}%.\n\n"
                . "Высота в помещении: {$hIn} см. "
                . "Урожайность: {$yIn} г/м².\n"
                . "Цветение: {$flower}.\n"
                . "На открытом воздухе: {$hOut} см, "
                . "до {$yOut} г с растения.\n"
                . "Сбор урожая: {$harvest}.";

            $advantages = "Сативы {$sat}% / Индики {$ind}%";

            // ============================================
            // ПОЛУЧАЕМ ИЛИ СОЗДАЁМ ТОВАР
            // ============================================

            $product = Product::where('slug', $slug)->first();

            if (!$product) {
                $product = new Product();
                $product->slug = $slug;
            }

            // ============================================
            // ОСНОВНЫЕ ДАННЫЕ
            // ============================================

            $product->brand_id = $barneys->id;
            $product->name = $name;
            $product->description = $description;

            $product->price = 1500;
            $product->old_price = null;

            $product->thc = $thc . '%';
            $product->seed_type = $seedType;

            $product->height = $hIn . ' см';
            $product->advantages = $advantages;
            $product->yield = $yIn . ' г/м²';

            // Дополнительные поля
            $product->genetics = $genetics;
            $product->flowering = $flower;

            $product->rating = 4.5;
            $product->stock = 100;

            $product->is_promo = false;
            $product->is_on_sale = false;

            $product->seo_title =
                "{$name} купить семена | Barney's Farm";

            $product->seo_description =
                "Купить семена {$name} от Barney's Farm. "
                . "ТГК {$thc}%. Доставка по России.";

            $product->seo_keywords =
                "{$name}, Barney's Farm, семена конопли, купить семена";

            // ============================================
            // ИЗОБРАЖЕНИЕ НЕ ТРОГАЕМ
            // ============================================
            //
            // Если оно уже есть — оно сохраняется.
            // Если его нет — остаётся NULL.
            //
            // gallery тоже специально не трогаем.
            //
            // ============================================

            $product->save();

            // ============================================
            // ОСНОВНАЯ КАТЕГОРИЯ
            // ============================================

            $catKey = match ($seedType) {
                'A' => 'autoflower',
                'F' => 'feminised',
                'R' => 'regular',
            };

            $product->categories()->syncWithoutDetaching([
                $cats[$catKey]->id
            ]);

            // ============================================
            // НОВИНКИ
            // ============================================

            if ((string) $isNew === '1') {
                $product->categories()->syncWithoutDetaching([
                    $cats['new']->id
                ]);
            }

            // ============================================
            // ПОПУЛЯРНЫЕ
            // ============================================

            if ((string) $isPopular === '1') {
                $product->categories()->syncWithoutDetaching([
                    $cats['popular']->id
                ]);
            }

            // ============================================
            // ВАРИАНТЫ УПАКОВОК
            // ============================================

            foreach ($packPrices as $seedsCount => $pack) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id'  => $product->id,
                        'package_size' => $seedsCount,
                    ],
                    [
                        'sku'       => strtoupper($slug) . '-' . $seedsCount,
                        'price'     => $pack['price'],
                        'old_price' => $pack['old'],
                        'stock'     => 50,
                    ]
                );
            }

            $loadedProducts++;
        }

        // ============================================
        // РЕЗУЛЬТАТ
        // ============================================

        $this->command->info(
            '✅ Загружено/обновлено продуктов: ' . $loadedProducts
        );
    }

    /**
     * Склонение слова "семя".
     */
    private function pluralSeeds(int $n): string
    {
        $mod10 = $n % 10;
        $mod100 = $n % 100;

        if ($mod10 === 1 && $mod100 !== 11) {
            return 'семя';
        }

        if (
            $mod10 >= 2
            && $mod10 <= 4
            && ($mod100 < 12 || $mod100 > 14)
        ) {
            return 'семени';
        }

        return 'семян';
    }
}
