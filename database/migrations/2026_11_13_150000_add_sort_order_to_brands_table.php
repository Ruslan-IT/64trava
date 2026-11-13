<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('id');
        });

        $doctorsChoiceId = DB::table('brands')
            ->where('id', 28)
            ->orWhere('slug', 'doctors-choice')
            ->orWhere('name', "Doctor's Choice")
            ->orderByRaw('CASE WHEN id = 28 THEN 0 ELSE 1 END')
            ->value('id');

        $orderedIds = [];

        if ($doctorsChoiceId !== null) {
            $orderedIds[] = (int) $doctorsChoiceId;
        }

        $remainingIds = DB::table('brands')
            ->when($doctorsChoiceId !== null, fn ($query) => $query->where('id', '!=', $doctorsChoiceId))
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach (array_merge($orderedIds, $remainingIds) as $index => $id) {
            DB::table('brands')
                ->where('id', $id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
