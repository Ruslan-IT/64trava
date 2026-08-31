<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'image',
        'excerpt',
        'content',
        'views',
        'reading_time',
        'published_at',
        'is_published',
        'sort_order',

        // SEO
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {

            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }

            $news->reading_time = static::calculateReadingTime($news->content);
        });

        static::updating(function ($news) {

            if ($news->isDirty('content')) {
                $news->reading_time = static::calculateReadingTime($news->content);
            }

        });
    }

    protected static function calculateReadingTime(?string $content): int
    {
        $text = strip_tags($content ?? '');

        $words = preg_split('/\s+/u', trim($text));

        $wordCount = count(array_filter($words));

        return max(1, (int) ceil($wordCount / 60));
    }
}
