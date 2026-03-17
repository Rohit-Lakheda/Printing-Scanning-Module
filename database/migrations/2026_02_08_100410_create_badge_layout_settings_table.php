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
        Schema::create('badge_layout_settings', function (Blueprint $table) {
            $table->id();
            $table->string('Category');
            $table->string('field_name'); // RegID, Name, Email, etc.
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('font_size')->default(12);
            $table->string('font_family')->default('Comfortaa');
            $table->string('font_weight')->default('normal'); // normal, bold
            $table->string('text_align')->default('left'); // left, center, right
            $table->string('color')->default('#000000');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('z_index')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_layout_settings');
    }
};
