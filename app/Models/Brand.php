<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'promo_percent',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function bonusProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            BonusProduct::class,
            'bonus_product_brand'
        );
    }
}
