<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->jsonb('specializations')->default('[]');
            $table->smallInteger('experience_years')->default(0);
            $table->decimal('price_per_hour', 10, 2);
            $table->float('rating')->default(0);
            $table->jsonb('images')->default('[]');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_profiles');
    }
};

