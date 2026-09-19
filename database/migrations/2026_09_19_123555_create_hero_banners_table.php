<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();

            $table->string('desktop_image');
            $table->string('mobile_image');

            $table->string('label')->nullable();
            $table->string('title_new')->nullable();
            $table->string('title_releases')->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('duration')->default(5);

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_banners');
    }
};
