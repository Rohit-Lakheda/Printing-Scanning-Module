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
            // Static text fields for category-wide labels (Instruction1..5)
            $table->string('static_text_key')->nullable()->after('field_name'); // e.g. Instruction1..5
            $table->text('static_text_value')->nullable()->after('static_text_key'); // text shown on all badges
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badge_layout_settings', function (Blueprint $table) {
            $table->dropColumn(['static_text_key', 'static_text_value']);
        });
    }
};
