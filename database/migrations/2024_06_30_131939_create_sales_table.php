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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('day')->nullable();
            $table->string('month');
            $table->integer('year');
            $table->integer('total_daily_users')->default(0);
            $table->integer('total_flexi_users')->default(0);
            $table->integer('total_monthly_users')->default(0);
            $table->integer('total_conference_users')->default(0);
            $table->decimal('total_sales', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
