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
        Schema::create('bypassed_regid_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bypassed_regid_id')->constrained('bypassed_regids')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->timestamp('used_at');
            $table->timestamps();
            
            // Ensure we can track which bypassed RegID was used at which location
            $table->unique(['bypassed_regid_id', 'location_id']);
            $table->index(['location_id', 'used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bypassed_regid_usage_logs');
    }
};
