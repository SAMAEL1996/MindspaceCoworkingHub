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
            $table->dateTime('old_time_in')->nullable();
            $table->dateTime('old_time_out')->nullable();
        });

        foreach(\App\Models\DailySale::all() as $sale) {
            $timeIn = \Carbon\Carbon::parse($sale->date . ' ' . $sale->time_in);
            $timeOut = \Carbon\Carbon::parse($sale->date . ' ' . $sale->time_out);

            if($timeOut->lt($timeIn)) {
                $newTimeOut = $timeOut->copy()->addDay();
            } else {
                $newTimeOut = $timeOut->copy();
            }

            $sale->old_time_in = $timeIn->copy();
            $sale->old_time_out = $newTimeOut;
            $sale->save();
        }

        Schema::table('daily_sales', function (Blueprint $table) {
            $table->dropColumn('time_in');
            $table->dropColumn('time_out');
        });

        Schema::table('daily_sales', function (Blueprint $table) {
            $table->dateTime('time_in')->after('description')->nullable();
            $table->dateTime('time_out')->after('time_in_staff_id')->nullable();
        });

        foreach(\App\Models\DailySale::all() as $sale) {
            $sale->time_in = $sale->old_time_in;
            $sale->time_out = $sale->old_time_out;
            $sale->save();
        }

        Schema::table('daily_sales', function (Blueprint $table) {
            $table->dropColumn('old_time_in');
            $table->dropColumn('old_time_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_sales', function (Blueprint $table) {
            //
        });
    }
};
