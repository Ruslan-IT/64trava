<?php

use App\Models\FooterSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->json('groups');
            $table->json('socials');
            $table->timestamps();
        });

        FooterSetting::query()->insert([
            'description' => FooterSetting::defaultDescription(),
            'groups' => json_encode(FooterSetting::defaultGroups(), JSON_UNESCAPED_UNICODE),
            'socials' => json_encode(FooterSetting::defaultSocials(), JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
