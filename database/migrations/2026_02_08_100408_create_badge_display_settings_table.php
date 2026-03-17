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
        Schema::create('badge_display_settings', function (Blueprint $table) {
            $table->id();
            $table->string('Category');
            $table->boolean('RegID')->default(0);
            $table->boolean('Name')->default(0);
            $table->boolean('Email')->default(0);
            $table->boolean('Mobile')->default(0);
            $table->boolean('Designation')->default(0);
            $table->boolean('Company')->default(0);
            $table->boolean('Country')->default(0);
            $table->boolean('State')->default(0);
            $table->boolean('City')->default(0);
            $table->boolean('Additional1')->default(0);
            $table->boolean('Additional2')->default(0);
            $table->boolean('Additional3')->default(0);
            $table->boolean('Additional4')->default(0);
            $table->boolean('Additional5')->default(0);
            $table->boolean('IsUniquePrint')->default(0);
            $table->boolean('QRcode')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_display_settings');
    }
};
