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
        Schema::table('monthly_users', function (Blueprint $table) {
            $table->bigInteger('rate_id')->nullable()->after('uid');
        });

        // foreach(\App\Models\MonthlyUser::all() as $monthly) {
        //     $rate = \App\Models\Rate::where('type', 'Monthly')->where('status', true)->first();
        //     $monthly->rate_id = $rate->id;
        //     $monthly->save();
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_users', function (Blueprint $table) {
            $table->dropColumn('rate_id');
        });
    }
};
