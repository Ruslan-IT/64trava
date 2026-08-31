<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();

            // Основная информация
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();

            // Изображение
            $table->string('image')->nullable();

            // Краткое описание для карточки
            $table->text('excerpt')->nullable();

            // Полный текст новости
            $table->longText('content');

            // Статистика
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedSmallInteger('reading_time')->default(2);

            // Публикация
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(true);

            // Сортировка
            $table->unsignedInteger('sort_order')->default(0);

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
