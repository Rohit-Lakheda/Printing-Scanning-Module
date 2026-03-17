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
        Schema::create('bypassed_regids', function (Blueprint $table) {
            $table->id();
            $table->string('regid'); // Registration ID that gets bypassed
            $table->text('reason'); // Reason for bypass (required)
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
        Schema::dropIfExists('bypassed_regids');
    }
};
