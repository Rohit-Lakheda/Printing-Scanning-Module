<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('badge_layout_settings', function (Blueprint $table) {
            $table->string('layout_type')->default('normal')->after('Category');
            $table->index(['Category', 'layout_type']);
        });
    }

    public function down(): void
    {
        Schema::table('badge_layout_settings', function (Blueprint $table) {
            $table->dropIndex(['Category', 'layout_type']);
            $table->dropColumn('layout_type');
        });
    }
};

