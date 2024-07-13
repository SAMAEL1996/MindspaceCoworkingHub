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
        Schema::create('monthly_users', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->string('card_id');
            $table->string('name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('facebook')->nullable();
            $table->text('social_media')->nullable();
            $table->date('date_start');
            $table->date('date_finish');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_expired')->default(false);
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
        Schema::dropIfExists('monthly_users');
    }
};
