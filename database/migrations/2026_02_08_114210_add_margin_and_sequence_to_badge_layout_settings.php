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
            $table->decimal('margin_top', 6, 2)->default(0)->after('position_y');
            $table->integer('sequence')->default(0)->after('z_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badge_layout_settings', function (Blueprint $table) {
            $table->dropColumn(['margin_top', 'sequence']);
        });
    }
};
