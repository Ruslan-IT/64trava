<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * Дубликаты категорий, скрытые в Filament.
     * id=1 (avtocvetushhie) — дубль «Автоцветущие»; на витрине используется id=9 (autoflower).
     */
    public const HIDDEN_IN_ADMIN_IDS = [
        1, // avtocvetushhie
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];



    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
