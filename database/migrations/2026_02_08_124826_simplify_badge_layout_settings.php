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
        Schema::table('badge_layout_settings', function (Blueprint $table) {
            // Remove columns we don't need
            $table->dropColumn(['position_x', 'position_y', 'z_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badge_layout_settings', function (Blueprint $table) {
            $table->decimal('position_x', 8, 2)->default(0)->after('field_name');
            $table->decimal('position_y', 8, 2)->default(0)->after('position_x');
            $table->integer('z_index')->default(1)->after('sequence');
        });
    }
};
