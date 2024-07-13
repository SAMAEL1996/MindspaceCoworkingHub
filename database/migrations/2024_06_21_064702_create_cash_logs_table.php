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
        Schema::create('cash_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->decimal('cash_in', 8, 2)->default(0.00);
            $table->dateTime('date_cash_in')->nullable();
            $table->decimal('cash_out', 8, 2)->default(0.00);
            $table->dateTime('date_cash_out')->nullable();
            $table->decimal('total_sales', 8, 2)->default(0.00);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_logs');
    }
};
