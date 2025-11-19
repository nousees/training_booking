<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trainer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('trainer_profiles', 'supports_online')) {
                $table->boolean('supports_online')->default(false)->after('images');
            }
            if (!Schema::hasColumn('trainer_profiles', 'online_link')) {
                $table->string('online_link')->nullable()->after('supports_online');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('trainer_profiles', 'online_link')) {
                $table->dropColumn('online_link');
            }
            if (Schema::hasColumn('trainer_profiles', 'supports_online')) {
                $table->dropColumn('supports_online');
            }
        });
    }
};
