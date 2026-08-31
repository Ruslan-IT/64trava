<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPage extends Model
{
    protected $fillable = [
        'title',
        'intro',

        'delivery_title',
        'delivery_description',

        'delivery_method_1_image',
        'delivery_method_1_title',
        'delivery_method_1_text',

        'delivery_method_2_image',
        'delivery_method_2_title',
        'delivery_method_2_text',

        'payment_title',
        'payment_description',

        'payment_image_1',
        'payment_image_2',
        'payment_image_3',

        'info_1_title',
        'info_1_text',
        'info_1_image',

        'info_2_title',
        'info_2_text',
        'info_2_image',

        'seo_title',
        'seo_description',
        'seo_keywords',
    ];
}
