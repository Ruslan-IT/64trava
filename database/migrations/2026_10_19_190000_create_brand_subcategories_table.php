<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('h1')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->longText('seo_text')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->string('seed_type', 8)->nullable();
            $table->string('name_token', 50)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['brand_id', 'slug']);
            $table->index(['brand_id', 'is_published', 'sort_order']);
        });

        $brandId = DB::table('brands')->where('slug', 'barneys-farm')->value('id');

        if (! $brandId) {
            return;
        }

        $now = now();

        foreach ($this->barneysFarmPages() as $page) {
            DB::table('brand_subcategories')->updateOrInsert(
                [
                    'brand_id' => $brandId,
                    'slug' => $page['slug'],
                ],
                $page + [
                    'brand_id' => $brandId,
                    'is_published' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_subcategories');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function barneysFarmPages(): array
    {
        return [
            [
                'name' => "F1 Hybrids Barney's Farm",
                'slug' => 'f1-hybrids',
                'h1' => "F1 Hybrids Barney's Farm",
                'seo_title' => "F1 Hybrids Barney's Farm",
                'seo_description' => null,
                'seo_text' => null,
                'seo_keywords' => null,
                'seed_type' => null,
                'name_token' => 'F1',
                'sort_order' => 1,
            ],
            [
                'name' => "Феминизированные Barney's Farm",
                'slug' => 'feminized',
                'h1' => "Феминизированные Barney's Farm",
                'seo_title' => "Феминизированные Barney's Farm",
                'seo_description' => null,
                'seo_text' => null,
                'seo_keywords' => null,
                'seed_type' => 'F',
                'name_token' => null,
                'sort_order' => 2,
            ],
            [
                'name' => "Автофеминизированные Barney's Farm",
                'slug' => 'autoflowering',
                'h1' => "Автофеминизированные Barney's Farm",
                'seo_title' => "Автофеминизированные Barney's Farm",
                'seo_description' => null,
                'seo_text' => null,
                'seo_keywords' => null,
                'seed_type' => 'A',
                'name_token' => null,
                'sort_order' => 3,
            ],
            [
                'name' => "Регулярные Barney's Farm",
                'slug' => 'regular',
                'h1' => "Регулярные Barney's Farm",
                'seo_title' => "Регулярные Barney's Farm",
                'seo_description' => null,
                'seo_text' => null,
                'seo_keywords' => null,
                'seed_type' => 'R',
                'name_token' => null,
                'sort_order' => 4,
            ],
        ];
    }
};
