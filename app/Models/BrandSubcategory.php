<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BrandSubcategory extends Model
{
    public const SEED_TYPES = [
        'F' => 'Феминизированные',
        'A' => 'Автофеминизированные',
        'R' => 'Регулярные',
    ];

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'h1',
        'seo_title',
        'seo_description',
        'seo_text',
        'seo_keywords',
        'seed_type',
        'name_token',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function heading(): string
    {
        $heading = trim((string) $this->h1);

        return $heading !== '' ? $heading : $this->name;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function applyToProductQuery(Builder $query): Builder
    {
        $query->where('brand_id', $this->brand_id);

        $hasRule = false;

        if (filled($this->seed_type)) {
            $query->where('seed_type', $this->seed_type);
            $hasRule = true;
        }

        $token = trim((string) $this->name_token);

        if ($token !== '') {
            $query->where('name', 'like', '%'.addcslashes($token, '%_\\').'%');
            $hasRule = true;
        }

        if (! $hasRule) {
            $query->whereRaw('0 = 1');
        }

        return $query;
    }

    protected static function booted(): void
    {
        static::saving(function (BrandSubcategory $subcategory) {
            if (blank($subcategory->slug) && filled($subcategory->name)) {
                $subcategory->slug = Str::slug($subcategory->name);
            }

            if (blank($subcategory->seed_type)) {
                $subcategory->seed_type = null;
            }

            if (blank($subcategory->name_token)) {
                $subcategory->name_token = null;
            }
        });
    }
}
