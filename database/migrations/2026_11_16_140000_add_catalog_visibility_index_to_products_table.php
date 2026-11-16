<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Covers Product::visibleInCatalog() and facet/count queries:
     * WHERE is_visible = 1 AND price IS NOT NULL [AND seed_type = ?].
     *
     * Existing indexes (PRIMARY, slug, brand_id, excel_row) do not help that filter.
     * Do not run on hosting from this chat — deploy/migrate separately after review.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_visible', 'price', 'seed_type'], 'products_catalog_visibility_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_catalog_visibility_index');
        });
    }
};
