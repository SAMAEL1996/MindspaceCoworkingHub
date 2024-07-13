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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->bigInteger('staff_id');
            $table->boolean('sss')->default(false);
            $table->boolean('pagibig')->default(false);
            $table->boolean('philhealth')->default(false);
            $table->boolean('tin')->default(false);
            $table->boolean('psa')->default(false);
            $table->boolean('nbi')->default(false);
            $table->boolean('brgy_clearance')->default(false);
            $table->boolean('diploma')->default(false);
            $table->boolean('medical')->default(false);
            $table->boolean('coe')->default(false);
            $table->boolean('bir')->default(false);
            $table->boolean('id_picture_1')->default(false);
            $table->boolean('id_picture_2')->default(false);
            $table->date('deadline')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
