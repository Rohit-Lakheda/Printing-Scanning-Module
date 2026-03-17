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
        Schema::create('api_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // API configuration name
            $table->string('api_key')->unique(); // API key for authentication
            $table->boolean('is_active')->default(true); // Enable/disable API
            $table->text('field_mappings')->nullable(); // JSON mapping of API fields to DB columns
            $table->text('description')->nullable(); // Description of the API
            $table->string('endpoint_url')->nullable(); // Generated endpoint URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_configurations');
    }
};
