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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->integer('check_in_tolerance_minutes')
                ->default(15)
                ->after('radius_meters')
                ->nullable();
            $table->integer('check_out_tolerance_minutes')
                ->default(15)
                ->after('check_in_tolerance_minutes')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn('check_in_tolerance_minutes');
            $table->dropColumn('check_out_tolerance_minutes');
        });

    }
};
