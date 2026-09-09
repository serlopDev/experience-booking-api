<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('experience_id')
                ->constrained('experiences');
            $table->dateTime('starts_at');
            $table->date('session_day')
                ->storedAs('DATE(starts_at)');
            $table->unsignedInteger('max_capacity');
            $table->unsignedInteger('reserved_seats')->default(0);
            $table->unsignedInteger('price_in_cents')->default(0);
            $table->timestamps();

            $table->unique(
                ['experience_id', 'session_day'],
                'sessions_experience_day_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
