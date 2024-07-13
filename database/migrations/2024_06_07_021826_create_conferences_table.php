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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable();
            $table->bigInteger('book_by')->nullable();
            $table->dateTime('start_at');
            $table->integer('duration');
            $table->string('event');
            $table->integer('members');
            $table->string('host');
            $table->string('email')->nullable();
            $table->string('contact_no');
            $table->string('status')->nullable();
            $table->decimal('amount', 8, 2)->default(0.00);
            $table->boolean('down_payment')->default(false);
            $table->decimal('payment', 8, 2)->default(0.00);
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
