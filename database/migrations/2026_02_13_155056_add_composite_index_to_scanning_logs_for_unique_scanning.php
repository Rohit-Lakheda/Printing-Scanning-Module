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
        Schema::table('scanning_logs', function (Blueprint $table) {
            // Add composite index for unique scanning queries
            // This optimizes: where('location_id', X)->where('regid', Y)->where('is_allowed', true)->orderBy('scanned_at', 'desc')
            $table->index(['location_id', 'regid', 'is_allowed', 'scanned_at'], 'scanning_logs_unique_scanning_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scanning_logs', function (Blueprint $table) {
            $table->dropIndex('scanning_logs_unique_scanning_idx');
        });
    }
};
