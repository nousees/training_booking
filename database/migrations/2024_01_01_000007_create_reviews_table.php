<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // reviewer (client)
                $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
                $table->unsignedTinyInteger('rating'); // 1-5
                $table->text('comment')->nullable();
                $table->timestamps();

                $table->unique('booking_id');
                $table->index(['trainer_id', 'rating']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
