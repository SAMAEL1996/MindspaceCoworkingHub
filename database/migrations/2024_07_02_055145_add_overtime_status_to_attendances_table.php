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
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('approve_overtime')->default(false);
            $table->integer('total_overtime_hours')->nullable();
            $table->boolean('restday_overtime')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('approve_overtime');
            $table->dropColumn('total_overtime_hours');
            $table->dropColumn('restday_overtime');
        });
    }
};
