<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusRuleProduct extends Model
{
    public $timestamps = false;

    protected $table = 'bonus_rule_product';

    protected $fillable = [
        'bonus_rule_id',
        'bonus_product_id',
    ];
}
