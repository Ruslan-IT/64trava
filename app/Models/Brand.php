<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'promo_percent',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(BrandSubcategory::class)->orderBy('sort_order')->orderBy('id');
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

            if (! isset($brand->sort_order) || $brand->sort_order === null) {
                $brand->sort_order = (int) static::query()->max('sort_order') + 1;
            }
        });

        // Soft delete keeps logo path/file and all relations (products stay linked).
        // Physical logo cleanup runs only on permanent deletion.
        static::forceDeleting(function (Brand $brand): void {
            if ($brand->products()->withTrashed()->exists()) {
                throw new \RuntimeException(
                    'Нельзя окончательно удалить бренд, у которого есть товары. Сначала перенесите или удалите товары.'
                );
            }

            $brand->deleteUnreferencedLogoFile();
        });
    }

    public function deleteUnreferencedLogoFile(): void
    {
        $path = $this->logo;

        if (! filled($path) || ! is_string($path)) {
            return;
        }

        if ($this->logoPathIsReferencedByAnotherBrand($path)) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function logoPathIsReferencedByAnotherBrand(string $path): bool
    {
        return static::withTrashed()
            ->where('id', '!=', $this->id)
            ->where('logo', $path)
            ->exists();
    }

    public function bonusProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            BonusProduct::class,
            'bonus_product_brand'
        );
    }
}
