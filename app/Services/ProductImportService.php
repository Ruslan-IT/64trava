<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductImportService
{
    private const TYPE_LABELS = [
        'F' => 'Feminised',
        'A' => 'Autoflower',
        'R' => 'Regular',
        'AR' => 'Autoregular',
    ];

    public function import(array $row): Product
    {
        return DB::transaction(function () use ($row) {
            Validator::make($row, [
                'strain' => ['required', 'string'],
                'brand' => ['required', 'string'],
                'seed_type' => ['required', Rule::in(array_keys(self::TYPE_LABELS))],
                'price' => ['nullable', 'numeric'],
                'variants' => ['array'],
                'variants.*.package_size' => ['required', 'integer', 'min:1'],
                'variants.*.sku' => ['required', 'string', 'max:100'],
                'variants.*.price' => ['required', 'numeric'],
                'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            ])->validate();

            $brandName = trim($row['brand']);
            $brandSlug = Str::slug($brandName);

            $brand = Brand::query()->firstOrCreate(
                ['slug' => $brandSlug],
                ['name' => $brandName]
            );

            $name = $this->productName($row['strain'], $row['seed_type'], $brand->name);
            $slug = Str::slug($name);

            $product = Product::query()->where('slug', $slug)->first() ?? new Product();

            $product->fill([
                'brand_id' => $brand->id,
                'name' => $name,
                'slug' => $slug,
                'price' => $row['price'],
                'seed_type' => $row['seed_type'],
                'thc' => $this->textValue($row['thc'] ?? null),
                'cbd' => $this->cbdValue($row['cbd'] ?? null),
                'sativa_percent' => $this->percentValue($row['sativa_percent'] ?? null),
                'indica_percent' => $this->percentValue($row['indica_percent'] ?? null),
                'taste' => $this->characteristicValue($row['taste'] ?? null),
                'effect' => $this->characteristicValue($row['effect'] ?? null),
                'aroma' => $this->characteristicValue($row['aroma'] ?? null),
                'flowering' => $this->textValue($row['flowering'] ?? null),
                'indoor_height' => $this->textValue($row['indoor_height'] ?? null),
                'height' => $this->textValue($row['height'] ?? null),
                'yield' => $this->textValue($row['yield'] ?? null),
                'outdoor_yield' => $this->textValue($row['outdoor_yield'] ?? null),
                'harvest' => $this->textValue($row['harvest'] ?? null),
            ]);
            $product->save();

            foreach ($row['variants'] ?? [] as $variant) {
                $this->saveVariant($product, $variant);
            }

            return $product;
        });
    }

    private function productName(string $strain, string $seedType, string $brand): string
    {
        $name = trim($strain);
        $label = self::TYPE_LABELS[$seedType];

        if (! str_contains($name, $label)) {
            $name .= ' '.$label;
        }

        if (! str_contains($name, $brand)) {
            $name .= ' '.$brand;
        }

        return $name;
    }

    private function textValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;

        if (trim($value) === '') {
            return null;
        }

        return $value;
    }

    private function cbdValue(mixed $value): ?float
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            return null;
        }

        if (! is_numeric($value) || (float) $value <= 0) {
            return null;
        }

        return (float) $value;
    }

    private function percentValue(mixed $value): ?float
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $bound = DB::selectOne(
            'SELECT '.Product::upperBoundSql('raw_value').' AS bound FROM (SELECT ? AS raw_value) AS src',
            [(string) $value]
        );

        if ($bound === null || $bound->bound === null) {
            return null;
        }

        return (float) $bound->bound;
    }

    private function characteristicValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $tokens = [];

        $parts = is_array($value) ? $value : [$value];

        foreach ($parts as $part) {
            if ($part === null) {
                continue;
            }

            foreach (Product::splitCharacteristicTokens((string) $part) as $token) {
                $tokens[$token] = $token;
            }
        }

        if ($tokens === []) {
            return null;
        }

        return implode(', ', array_values($tokens));
    }

    private function saveVariant(Product $product, array $variant): void
    {
        $model = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('package_size', $variant['package_size'])
            ->first() ?? new ProductVariant();

        $attributes = [
            'product_id' => $product->id,
            'package_size' => $variant['package_size'],
            'sku' => $variant['sku'],
            'price' => $variant['price'],
            'stock' => $variant['stock'] ?? 0,
        ];

        if (array_key_exists('old_price', $variant)) {
            $attributes['old_price'] = $variant['old_price'];
        }

        $model->fill($attributes);
        $model->save();
    }
}
