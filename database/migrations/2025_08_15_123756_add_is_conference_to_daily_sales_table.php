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
        Schema::table('daily_sales', function (Blueprint $table) {
            $table->boolean('is_conference')->default(false)->after('is_monthly');
        });

        foreach(\App\Models\DailySale::where('description', 'Conference')->get() as $dailySale) {
            $dailySale->is_conference = true;
            $dailySale->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_sales', function (Blueprint $table) {
            $table->dropColumn('is_conference');
        });
    }
};
