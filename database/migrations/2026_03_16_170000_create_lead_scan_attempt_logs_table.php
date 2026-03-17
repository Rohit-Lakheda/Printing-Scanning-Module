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
        Schema::create('lead_scan_attempt_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scanned_by_user_id')->nullable(); // references user_credentials.id in this module
            $table->unsignedBigInteger('lead_scan_id')->nullable(); // links successful attempt to lead_scans.id
            $table->string('regid');
            $table->string('status', 50); // success, already_scanned, user_not_found, error
            $table->text('message')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->string('source')->nullable(); // qr/manual/offline_sync
            $table->timestamps();

            $table->index('scanned_by_user_id');
            $table->index('regid');
            $table->index('status');
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_scan_attempt_logs');
    }
};

