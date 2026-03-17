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
        Schema::table('lead_scans', function (Blueprint $table) {
            $table->string('lead_type')->nullable()->after('source');
            $table->text('lead_comments')->nullable()->after('lead_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_scans', function (Blueprint $table) {
            $table->dropColumn(['lead_type', 'lead_comments']);
        });
    }
};

