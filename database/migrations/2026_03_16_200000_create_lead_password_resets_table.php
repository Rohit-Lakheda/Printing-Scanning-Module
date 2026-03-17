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
        Schema::create('lead_password_resets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_credential_id')->nullable();
            $table->string('email');
            $table->string('token_hash', 64);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'expires_at']);
            $table->foreign('user_credential_id')
                ->references('id')
                ->on('user_credentials')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_password_resets');
    }
};

