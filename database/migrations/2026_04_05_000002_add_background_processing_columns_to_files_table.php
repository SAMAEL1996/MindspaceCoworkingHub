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
        Schema::table('files', function (Blueprint $table) {
            $table->string('temp_path')->nullable()->after('path');
            $table->string('pending_delete_path')->nullable()->after('temp_path');
            $table->string('mime_type')->nullable()->after('type');
            $table->string('status')->default('queued')->after('size');
            $table->text('error_message')->nullable()->after('status');
            $table->timestamp('processing_started_at')->nullable()->after('uploaded_at');
            $table->timestamp('processing_completed_at')->nullable()->after('processing_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn([
                'temp_path',
                'pending_delete_path',
                'mime_type',
                'status',
                'error_message',
                'processing_started_at',
                'processing_completed_at',
            ]);
        });
    }
};
