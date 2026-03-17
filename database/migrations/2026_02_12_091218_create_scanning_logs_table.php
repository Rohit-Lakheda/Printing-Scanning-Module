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
        Schema::create('scanning_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->string('location_name'); // Store location name for historical records
            $table->string('regid'); // Registration ID
            $table->string('user_name')->nullable(); // User's name
            $table->string('category')->nullable(); // User's category
            $table->boolean('is_allowed')->default(false); // Whether access was granted
            $table->text('reason')->nullable(); // Reason why allowed/not allowed
            $table->timestamp('scanned_at'); // When the scan occurred
            $table->timestamps();
            
            // Index for faster queries
            $table->index('location_id');
            $table->index('regid');
            $table->index('scanned_at');
            $table->index('is_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scanning_logs');
    }
};
