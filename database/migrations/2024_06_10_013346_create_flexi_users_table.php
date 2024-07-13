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
        Schema::create('flexi_users', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->string('card_id')->nullable();
            $table->string('name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('facebook')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_active')->default(true);
            $table->boolean('status')->default(true);
            $table->boolean('paid')->default(true);
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flexi_users');
    }
};
