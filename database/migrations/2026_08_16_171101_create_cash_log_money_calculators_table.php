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
        Schema::create('cash_log_money_calculators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_log_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->unsignedInteger('note_20')->default(0);
            $table->unsignedInteger('note_50')->default(0);
            $table->unsignedInteger('note_100')->default(0);
            $table->unsignedInteger('note_200')->default(0);
            $table->unsignedInteger('note_500')->default(0);
            $table->unsignedInteger('note_1000')->default(0);
            $table->unsignedInteger('coin_1')->default(0);
            $table->unsignedInteger('coin_5')->default(0);
            $table->unsignedInteger('coin_10')->default(0);
            $table->unsignedInteger('coin_20')->default(0);
            $table->timestamps();

            $table->unique(['cash_log_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_log_money_calculators');
    }
};
