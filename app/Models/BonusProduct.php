<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BonusProduct extends Model
{
    protected $fillable = [
        'product_id',
        'threshold',
        'stock',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'threshold' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(
            Brand::class,
            'bonus_product_brand'
        );
    }
}
