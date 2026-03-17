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
        Schema::create('lead_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('default');

            // Which user_details fields are shared on scan / used in exports
            $table->boolean('share_RegID')->default(true);
            $table->boolean('share_Name')->default(true);
            $table->boolean('share_Category')->default(true);
            $table->boolean('share_Company')->default(true);
            $table->boolean('share_Email')->default(true);
            $table->boolean('share_Mobile')->default(true);
            $table->boolean('share_Designation')->default(false);
            $table->boolean('share_Country')->default(false);
            $table->boolean('share_State')->default(false);
            $table->boolean('share_City')->default(false);
            $table->boolean('share_Additional1')->default(false);
            $table->boolean('share_Additional2')->default(false);
            $table->boolean('share_Additional3')->default(false);
            $table->boolean('share_Additional4')->default(false);
            $table->boolean('share_Additional5')->default(false);

            // Credential email template (subject + HTML body) with placeholders like {{Name}}, {{Company}}, {{RegID}}, {{Username}}, {{Password}}, {{MaxDevices}}
            $table->string('credential_email_subject')->nullable();
            $table->longText('credential_email_body')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_settings');
    }
};

