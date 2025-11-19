<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'mode')) {
                $table->string('mode')->default('offline')->after('payment_status');
                $table->index('mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'mode')) {
                $table->dropIndex(['mode']);
                $table->dropColumn('mode');
            }
        });
    }
};
