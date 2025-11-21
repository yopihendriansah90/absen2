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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // 1=Senin, 7=Minggu (Mengikuti standar ISO-8601 atau Carbon)
            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time'); // Jam Masuk
            $table->time('end_time');   // Jam Pulang
            $table->boolean('is_wfh')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
