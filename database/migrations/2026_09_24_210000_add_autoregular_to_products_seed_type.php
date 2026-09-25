<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY seed_type ENUM('A', 'F', 'R', 'AR') NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE products SET seed_type = NULL WHERE seed_type = 'AR'");
        DB::statement("ALTER TABLE products MODIFY seed_type ENUM('A', 'F', 'R') NULL");
    }
};
