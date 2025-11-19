<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->integer('min_booking_hours_before_start')->default(2)->after('cancellation_window_hours');
            $table->integer('max_booking_days_ahead')->default(14)->after('min_booking_hours_before_start');
            $table->boolean('auto_confirm_bookings')->default(false)->after('maintenance_mode');
        });

        DB::table('system_settings')->where('id', 1)->update([
            'min_booking_hours_before_start' => 2,
            'max_booking_days_ahead' => 14,
            'auto_confirm_bookings' => false,
        ]);
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'min_booking_hours_before_start',
                'max_booking_days_ahead',
                'auto_confirm_bookings',
            ]);
        });
    }
};
