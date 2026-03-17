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
        Schema::create('printing_logs', function (Blueprint $table) {
            $table->id();
            $table->string('regid'); // Registration ID
            $table->string('user_name')->nullable(); // User's name
            $table->string('category')->nullable(); // User's category
            $table->string('print_type')->default('single'); // 'single' or 'bulk'
            $table->timestamp('printed_at'); // When the badge was printed
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('regid');
            $table->index('category');
            $table->index('printed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printing_logs');
    }
};
