<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('location');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['available', 'booked', 'canceled'])->default('available');
            $table->timestamps();

            $table->index(['trainer_id', 'date', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};

