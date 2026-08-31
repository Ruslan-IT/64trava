<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_pages', function (Blueprint $table) {
            $table->id();

            // Основная информация
            $table->string('title');
            $table->text('intro')->nullable();

            // Способ доставки
            $table->string('delivery_title')->nullable();
            $table->text('delivery_description')->nullable();

            // Доставка №1
            $table->string('delivery_method_1_image')->nullable();
            $table->string('delivery_method_1_title')->nullable();
            $table->text('delivery_method_1_text')->nullable();

            // Доставка №2
            $table->string('delivery_method_2_image')->nullable();
            $table->string('delivery_method_2_title')->nullable();
            $table->text('delivery_method_2_text')->nullable();

            // Способ оплаты
            $table->string('payment_title')->nullable();
            $table->text('payment_description')->nullable();

            // Фотографии оплаты
            $table->string('payment_image_1')->nullable();
            $table->string('payment_image_2')->nullable();
            $table->string('payment_image_3')->nullable();

            // Информационный блок №1
            $table->string('info_1_title')->nullable();
            $table->text('info_1_text')->nullable();
            $table->string('info_1_image')->nullable();

            // Информационный блок №2
            $table->string('info_2_title')->nullable();
            $table->text('info_2_text')->nullable();
            $table->string('info_2_image')->nullable();

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_pages');
    }
};
