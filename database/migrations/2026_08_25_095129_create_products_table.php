<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('brand');                // бренд
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->string('image')->nullable();    // основное изображение
            $table->string('thc')->nullable();      // например "30-33%"
            $table->enum('seed_type', ['A', 'F', 'R'])->nullable(); // A/F/R
            $table->string('height')->nullable();   // "70cm"
            $table->text('advantages')->nullable();
            $table->string('yield')->nullable();    // урожайность
            $table->float('rating', 3, 1)->default(0);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_on_sale')->default(false);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
