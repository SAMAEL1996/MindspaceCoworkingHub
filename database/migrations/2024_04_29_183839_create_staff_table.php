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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->bigInteger('user_id');
            $table->bigInteger('card_id');
            $table->string('personal_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('emergency_contact_person');
            $table->string('emergency_relationship')->nullable();
            $table->string('emergency_contact_no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
