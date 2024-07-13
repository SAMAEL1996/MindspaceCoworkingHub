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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->bigInteger('staff_id');
            $table->bigInteger('attendance_id');
            $table->date('date')->nullable();
            $table->decimal('staff_sales', 8, 2)->default(0.00);
            $table->decimal('total_sales', 8, 2)->default(0.00);
            $table->string('filename')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
