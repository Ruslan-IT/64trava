<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('footer_settings', function (Blueprint $table) {
            $table->string('contact_title')->nullable()->after('description');
            $table->string('contact_label')->nullable()->after('contact_title');
            $table->string('contact_url')->nullable()->after('contact_label');
        });

        DB::table('footer_settings')->update([
            'contact_title' => 'Свяжитесь с нами',
            'contact_label' => 'info@example.ru',
            'contact_url' => 'mailto:info@example.ru',
        ]);
    }

    public function down(): void
    {
        Schema::table('footer_settings', function (Blueprint $table) {
            $table->dropColumn(['contact_title', 'contact_label', 'contact_url']);
        });
    }
};
