<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trainer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('trainer_profiles', 'locations')) {
                $table->jsonb('locations')->default('[]')->after('images');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('trainer_profiles', 'locations')) {
                $table->dropColumn('locations');
            }
        });
    }
};
