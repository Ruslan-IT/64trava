<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            "Barney's Farm",
            'Dutch Bulk',
            'Dutch Passion',
            'Delicious Seeds',
            'DNA Genetics',
            'Doctor Choice',
            'Ethos Seeds',
            'Green House',
            'Humboldt Seeds',
            'Mandala Seeds',
            'Nirvana',
            'Paradise Seeds',
            'Pyramid Seeds',
            'Royal Queen Seeds',
            'Seedsman Seeds',
            'Serious Seeds',
            'Sweet Seeds',
            'Victory Seeds',
            'Sensi Seeds',
        ];

        foreach ($brands as $name) {
            Brand::firstOrCreate(
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
