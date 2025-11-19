<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('platform_commission_percent', 5, 2)->default(10.00);
            $table->integer('cancellation_window_hours')->default(24);
            $table->char('currency', 3)->default('USD');
            $table->boolean('maintenance_mode')->default(false);
            $table->timestamps();
        });

        DB::table('system_settings')->insert([
            'id' => 1,
            'platform_commission_percent' => 10.00,
            'cancellation_window_hours' => 24,
            'currency' => 'USD',
            'maintenance_mode' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};

