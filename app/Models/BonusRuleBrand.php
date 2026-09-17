<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusRuleBrand extends Model
{
    public $timestamps = false;

    protected $table = 'bonus_rule_brand';

    protected $fillable = [
        'bonus_rule_id',
        'brand_id',
    ];
}
