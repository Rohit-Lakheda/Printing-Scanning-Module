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
        Schema::create('bypassed_regid_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bypassed_regid_id')->constrained('bypassed_regids')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['bypassed_regid_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bypassed_regid_locations');
    }
};
