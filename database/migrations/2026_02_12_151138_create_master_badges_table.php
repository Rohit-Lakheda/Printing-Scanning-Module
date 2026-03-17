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
        Schema::create('master_badges', function (Blueprint $table) {
            $table->id();
            $table->string('regid'); // Registration ID for master badge
            $table->text('reason')->nullable(); // Reason/notes for master badge
            $table->timestamps();
            
            // Index for faster lookups
            $table->index('regid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_badges');
    }
};
