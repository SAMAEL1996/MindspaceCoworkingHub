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
        Schema::table('flexi_users', function (Blueprint $table) {
            $table->integer('remaining')->nullable()->after('end_at');
        });

        foreach(\App\Models\FlexiUser::all() as $flexi) {
            $flexi->remaining = $flexi->start_at_carbon->diffInMinutes($flexi->end_at_carbon);
            $flexi->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flexi_users', function (Blueprint $table) {
            $table->dropColumn('remaining');
        });
    }
};
