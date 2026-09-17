<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus_product_brand', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bonus_product_id')
                ->constrained('bonus_products')
                ->cascadeOnDelete();

            $table->foreignId('brand_id')
                ->constrained('brands')
                ->cascadeOnDelete();

            $table->unique([
                'bonus_product_id',
                'brand_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_product_brand');
    }
};
