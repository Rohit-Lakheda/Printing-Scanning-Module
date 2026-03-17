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
        Schema::create('blocked_regids', function (Blueprint $table) {
            $table->id();
            $table->string('regid'); // Registration ID to block
            $table->text('reason')->nullable(); // Reason for blocking
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
        Schema::dropIfExists('blocked_regids');
    }
};
