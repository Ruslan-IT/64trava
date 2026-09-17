<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BonusRule extends Model
{
    protected $fillable = [
        'name',
        'min_order_amount',
        'calculation_type',
        'bonus_quantity',
        'amount_step',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'min_order_amount' => 'decimal:2',
        'amount_step' => 'decimal:2',
        'bonus_quantity' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(
            Brand::class,
            'bonus_rule_brand'
        );
    }

    public function bonusProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            BonusProduct::class,
            'bonus_rule_product'
        );
    }
}
