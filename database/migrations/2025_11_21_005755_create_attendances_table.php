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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();

            $table->date('date'); // Tanggal Absen

            // Data Masuk
            $table->time('check_in_time')->nullable();
            $table->decimal('check_in_lat', 12, 8)->nullable();
            $table->decimal('check_in_long', 12, 8)->nullable();

            // Data Pulang
            $table->time('check_out_time')->nullable();
            $table->decimal('check_out_lat', 12, 8)->nullable();
            $table->decimal('check_out_long', 12, 8)->nullable();

            // Status & Note
            $table->enum('status', ['present', 'late', 'alpha', 'permission', 'sick'])->default('alpha');
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
