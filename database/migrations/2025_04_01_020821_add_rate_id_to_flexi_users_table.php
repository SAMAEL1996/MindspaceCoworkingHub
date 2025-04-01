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
            $table->bigInteger('rate_id')->nullable()->after('uid');
            $table->dateTime('expired_at')->nullable()->after('end_at');
        });

        Schema::table('flexi_users', function (Blueprint $table) {
            $table->dropColumn('facebook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flexi_users', callback: function (Blueprint $table) {
            $table->dropColumn('rate_id');
            $table->dropColumn('expired_at');
        });
        Schema::table('flexi_users', callback: function (Blueprint $table) {
            $table->string('facebook')->nullable();
        });
    }
};
