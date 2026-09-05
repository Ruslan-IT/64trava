<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Tag;

use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    use HasFactory;

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

        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'is_recommended' => 'boolean',
        'is_new' => 'boolean',
        'is_popular' => 'boolean',
        'is_on_sale' => 'boolean',
        'is_promo' => 'boolean',

        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'rating' => 'float',

        'gallery' => 'array',
    ];



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


}
