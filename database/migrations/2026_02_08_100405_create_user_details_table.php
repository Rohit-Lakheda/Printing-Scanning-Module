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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->string('RegID')->unique();
            $table->string('Category');
            $table->string('Name');
            $table->string('Designation')->nullable();
            $table->string('Company')->nullable();
            $table->string('Country')->nullable();
            $table->string('State')->nullable();
            $table->string('City')->nullable();
            $table->string('Email')->nullable();
            $table->string('Mobile')->nullable();
            $table->string('Additional1')->nullable();
            $table->string('Additional2')->nullable();
            $table->string('Additional3')->nullable();
            $table->string('Additional4')->nullable();
            $table->string('Additional5')->nullable();
            $table->integer('PrintCount')->default(0);
            $table->boolean('IsLunchAllowed')->default(false);
            $table->timestamp('Data_Received_At')->nullable();
            $table->timestamp('Badge_Printed_At')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
