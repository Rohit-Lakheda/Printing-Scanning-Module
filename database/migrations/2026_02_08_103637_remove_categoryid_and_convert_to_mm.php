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
        // Update categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('CategoryID');
            $table->decimal('badge_width', 8, 2)->default(85.00)->change(); // 85mm default (A6 width)
            $table->decimal('badge_height', 8, 2)->default(54.00)->change(); // 54mm default (A6 height)
        });

        // Update badge_layout_settings table
        Schema::table('badge_layout_settings', function (Blueprint $table) {
            $table->decimal('position_x', 8, 2)->default(0)->change();
            $table->decimal('position_y', 8, 2)->default(0)->change();
            $table->decimal('font_size', 6, 2)->default(4.23)->change(); // 4.23mm ≈ 12pt
            $table->decimal('width', 8, 2)->nullable()->change();
            $table->decimal('height', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('CategoryID')->unique()->after('id');
            $table->integer('badge_width')->default(300)->change();
            $table->integer('badge_height')->default(200)->change();
        });

        Schema::table('badge_layout_settings', function (Blueprint $table) {
            $table->integer('position_x')->default(0)->change();
            $table->integer('position_y')->default(0)->change();
            $table->integer('font_size')->default(12)->change();
            $table->integer('width')->nullable()->change();
            $table->integer('height')->nullable()->change();
        });
    }
};
