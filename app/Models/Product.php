<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Tag;

use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'brand_id',
        'brand',
        'name',
        'slug',
        'description',
        'price',
        'old_price',
        'image',

        'thc',
        'seed_type',
        'height',
        'advantages',
        'yield',
        'rating',

        'is_promo',
        'is_on_sale',
        'stock',

        'taste_aroma',
        'country',
        'effect',
        'flowering',
        'genetics',
        'gallery',
        'full_description',

        'sativa_percent',
        'indica_percent',
        'cbd',
        'taste',
        'aroma',
        'indoor_height',
        'outdoor_yield',
        'harvest',

        'seo_title',
        'seo_description',
        'seo_keywords',

        'excel_row',
        'is_visible',
    ];

    protected $casts = [
        'is_recommended' => 'boolean',
        'is_new' => 'boolean',
        'is_popular' => 'boolean',
        'is_on_sale' => 'boolean',
        'is_promo' => 'boolean',
        'is_visible' => 'boolean',

        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'rating' => 'float',

        'sativa_percent' => 'float',
        'indica_percent' => 'float',
        'cbd' => 'float',

        'gallery' => 'array',
    ];



    public static function hasPublicValue(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return false;
            }

            if (! is_numeric($value)) {
                return true;
            }
        }

        return is_numeric($value) && (float) $value != 0.0;
    }

    public function scopeVisibleInCatalog(Builder $query): Builder
    {
        return $query
            ->where('is_visible', true)
            ->whereNotNull('price');
    }

    public function isListedInCatalog(): bool
    {
        return $this->is_visible && $this->price !== null;
    }

    public function catalogListingLabel(): string
    {
        if ($this->isListedInCatalog()) {
            return 'В каталоге';
        }

        $hidden = ! $this->is_visible;
        $missingPrice = $this->price === null;

        if ($hidden && $missingPrice) {
            return 'Скрыт вручную, нет цены';
        }

        if ($hidden) {
            return 'Скрыт вручную';
        }

        return 'Нет цены';
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        $forgetCatalogFacets = static function (): void {
            Cache::forget('catalog.filter.facets.v1');
        };

        static::saved($forgetCatalogFacets);
        static::deleted($forgetCatalogFacets);
        static::restored($forgetCatalogFacets);
        static::forceDeleted($forgetCatalogFacets);

        // Soft delete must keep image paths and files intact for restore.
        // Physical cleanup runs only on permanent deletion.
        static::forceDeleting(function (Product $product): void {
            $product->deleteUnreferencedImageFiles();
        });
    }

    /**
     * Remove image files that are no longer referenced by any product
     * (including soft-deleted ones). Safe for shared URL-hash imports.
     */
    public function deleteUnreferencedImageFiles(): void
    {
        $paths = collect([$this->image])
            ->merge($this->gallery ?? [])
            ->filter(fn ($path) => filled($path) && is_string($path))
            ->unique()
            ->values();

        foreach ($paths as $path) {
            if ($this->imagePathIsReferencedByAnotherProduct($path)) {
                continue;
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    protected function imagePathIsReferencedByAnotherProduct(string $path): bool
    {
        return static::withTrashed()
            ->where('id', '!=', $this->id)
            ->where(function (Builder $query) use ($path): void {
                $query->where('image', $path)
                    ->orWhereJsonContains('gallery', $path);
            })
            ->exists();
    }

    public function getCountryFlagAttribute(): string
    {
        return match ($this->country) {
            'США' => 'us.svg',
            'Канада' => 'ca.svg',
            'Россия' => 'ru.svg',
            'Германия' => 'de.svg',
            'Нидерланды' => 'nl.svg',
            'Великобритания' => 'gb.svg',
            'Франция' => 'fr.svg',
            'Испания' => 'es.svg',
            'Италия' => 'it.svg',
            'Польша' => 'pl.svg',
            'Чехия' => 'cz.svg',
            'Швейцария' => 'ch.svg',
            'Австрия' => 'at.svg',
            'Бельгия' => 'be.svg',
            'Португалия' => 'pt.svg',
            'Бразилия' => 'br.svg',
            'Колумбия' => 'co.svg',
            'Мексика' => 'mx.svg',
            'Таиланд' => 'th.svg',
            'Япония' => 'jp.svg',
            'Китай' => 'cn.svg',
            'Индия' => 'in.svg',
            'Израиль' => 'il.svg',
            'Австралия' => 'au.svg',
            default => 'default.svg',
        };
    }

    public function bonusProduct(): HasOne
    {
        return $this->hasOne(BonusProduct::class);
    }

    /**
     * Верхняя граница диапазона: "26-30" → 30, "100-150 см" → 150, "60%" → 60.
     */
    public static function upperBoundSql(string $column): string
    {
        $column = preg_replace('/[^a-z_]/', '', $column) ?: 'id';

        return "CAST(NULLIF(SUBSTRING_INDEX(REGEXP_REPLACE({$column}, '[^0-9-]', ''), '-', -1), '') AS DECIMAL(10,2))";
    }

    /**
     * Процент сативы: новые поля, иначе разбор advantages («Сативы 70% / Индики 30%»).
     */
    public static function sativaValueSql(): string
    {
        $parsed = "CAST(NULLIF(SUBSTRING_INDEX(SUBSTRING_INDEX(advantages, 'Сативы ', -1), '%', 1), '') AS DECIMAL(10,2))";

        return "COALESCE(sativa_percent, IF(indica_percent IS NOT NULL, 100 - indica_percent, NULL), {$parsed})";
    }

    public static function splitCharacteristicTokens(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $normalized = str_replace([' и ', ';'], ',', $value);
        $parts = preg_split('/\s*,\s*/u', $normalized) ?: [];
        $tokens = [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part !== '') {
                $tokens[$part] = $part;
            }
        }

        return array_values($tokens);
    }
}
