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
        Schema::create('lead_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_detail_id')->constrained('user_details')->onDelete('cascade');
            $table->string('regid');
            $table->timestamp('scanned_at')->nullable();
            $table->string('device_id')->nullable();
            $table->unsignedBigInteger('scanned_by_user_id')->nullable(); // operator / scanner user
            $table->string('source')->nullable(); // qr, manual, api, etc.
            $table->string('location_name')->nullable();
            $table->timestamps();

            $table->index('regid');
            $table->index('scanned_at');
            $table->index('device_id');
        });

        Schema::create('mail_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default');
            $table->string('host');
            $table->unsignedInteger('port')->default(587);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('encryption')->nullable(); // ssl, tls, null
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->boolean('use_auth')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_detail_id')->constrained('user_details')->onDelete('cascade');
            $table->string('username')->unique();
            $table->string('password'); // hashed
            $table->unsignedInteger('max_devices')->nullable(); // null = unlimited
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_device_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_credential_id')->constrained('user_credentials')->onDelete('cascade');
            $table->string('device_id');
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_credential_id', 'device_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_device_logins');
        Schema::dropIfExists('user_credentials');
        Schema::dropIfExists('mail_configurations');
        Schema::dropIfExists('lead_scans');
    }
};

