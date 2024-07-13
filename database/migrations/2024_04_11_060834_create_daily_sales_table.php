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
        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->date('date')->nullable();
            $table->bigInteger('card_id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->time('time_in');
            $table->bigInteger('time_in_staff_id')->nullable();
            $table->time('time_out')->nullable();
            $table->bigInteger('time_out_staff_id')->nullable();
            $table->boolean('default_amount')->default(false);
            $table->integer('total_time')->nullable();
            $table->decimal('amount_paid', 8, 2)->default(0.00);
            $table->boolean('apply_discount')->default(false);
            $table->integer('discount')->default(0);
            $table->boolean('is_flexi')->default(false);
            $table->boolean('is_monthly')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales');
    }
};
