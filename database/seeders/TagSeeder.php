<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Sativa',
            'Northern Lights',
            'Regular',
            'Haze',
            'Jack Herer',
            'Kush',
            'Skywalker',
            'Girl Scout Cookies',
            'White Widow',
            'Mazar',
            'OG Kush',
            'Bruce Banner',
            'LSD',
            'Gorilla',
            'Zkittles',
            'AK47',
            'Gelato',
            'Wedding Cake',
            'Blueberry',
            'Skunk',
            'Big Devil',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(
                [
                    'name' => $name,
                ],
                [
                    'slug' => Str::slug($name),
                ]
            );
        }
    }
}
