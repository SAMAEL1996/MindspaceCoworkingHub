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
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn('down_payment');
            $table->dropColumn('payment');
            $table->dropColumn('paid');
        });

        Schema::table('conferences', function (Blueprint $table) {
            $table->integer('package_id')->after('uid')->default(1);
            $table->boolean('has_reservation_fee')->default(false)->after('amount');
            $table->decimal('payment', 8, 2)->default(0.00)->after('has_reservation_fee');
            $table->boolean('is_paid')->default(false)->after('payment');
            $table->string('mode_of_payment')->nullable()->after('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn('has_reservation_fee');
            $table->dropColumn('payment');
            $table->dropColumn('is_paid');
            $table->dropColumn('mode_of_payment');
        });

        Schema::table('conferences', function (Blueprint $table) {
            $table->boolean('down_payment')->default(false);
            $table->decimal('payment', 8, 2)->default(0.00);
            $table->boolean('paid')->default(false);
        });
    }
};
