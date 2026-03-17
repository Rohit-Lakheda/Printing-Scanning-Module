<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds 5 hours and 30 minutes to all existing timestamps
     * to correct the timezone from UTC to IST (UTC+5:30)
     */
    public function up(): void
    {
        // Update user_details table timestamps
        DB::statement("UPDATE user_details SET Data_Received_At = DATE_ADD(Data_Received_At, INTERVAL '5:30' HOUR_MINUTE) WHERE Data_Received_At IS NOT NULL");
        DB::statement("UPDATE user_details SET Badge_Printed_At = DATE_ADD(Badge_Printed_At, INTERVAL '5:30' HOUR_MINUTE) WHERE Badge_Printed_At IS NOT NULL");
        DB::statement("UPDATE user_details SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE user_details SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Update scanning_logs table timestamps
        DB::statement("UPDATE scanning_logs SET scanned_at = DATE_ADD(scanned_at, INTERVAL '5:30' HOUR_MINUTE) WHERE scanned_at IS NOT NULL");
        DB::statement("UPDATE scanning_logs SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE scanning_logs SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Update printing_logs table timestamps
        DB::statement("UPDATE printing_logs SET printed_at = DATE_ADD(printed_at, INTERVAL '5:30' HOUR_MINUTE) WHERE printed_at IS NOT NULL");
        DB::statement("UPDATE printing_logs SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE printing_logs SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Update locations table timestamps (if any exist)
        DB::statement("UPDATE locations SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE locations SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Update location_categories table timestamps (if any exist)
        DB::statement("UPDATE location_categories SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE location_categories SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Update categories table timestamps (if any exist)
        DB::statement("UPDATE categories SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE categories SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Update badge_layout_settings table timestamps (if any exist)
        if (Schema::hasTable('badge_layout_settings')) {
            DB::statement("UPDATE badge_layout_settings SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
            DB::statement("UPDATE badge_layout_settings SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");
        }

        // Update badge_display_settings table timestamps (if any exist)
        if (Schema::hasTable('badge_display_settings')) {
            DB::statement("UPDATE badge_display_settings SET created_at = DATE_ADD(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
            DB::statement("UPDATE badge_display_settings SET updated_at = DATE_ADD(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     * 
     * This will subtract 5 hours and 30 minutes from timestamps
     */
    public function down(): void
    {
        // Revert user_details table timestamps
        DB::statement("UPDATE user_details SET Data_Received_At = DATE_SUB(Data_Received_At, INTERVAL '5:30' HOUR_MINUTE) WHERE Data_Received_At IS NOT NULL");
        DB::statement("UPDATE user_details SET Badge_Printed_At = DATE_SUB(Badge_Printed_At, INTERVAL '5:30' HOUR_MINUTE) WHERE Badge_Printed_At IS NOT NULL");
        DB::statement("UPDATE user_details SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE user_details SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Revert scanning_logs table timestamps
        DB::statement("UPDATE scanning_logs SET scanned_at = DATE_SUB(scanned_at, INTERVAL '5:30' HOUR_MINUTE) WHERE scanned_at IS NOT NULL");
        DB::statement("UPDATE scanning_logs SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE scanning_logs SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Revert printing_logs table timestamps
        DB::statement("UPDATE printing_logs SET printed_at = DATE_SUB(printed_at, INTERVAL '5:30' HOUR_MINUTE) WHERE printed_at IS NOT NULL");
        DB::statement("UPDATE printing_logs SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE printing_logs SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Revert locations table timestamps
        DB::statement("UPDATE locations SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE locations SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Revert location_categories table timestamps
        DB::statement("UPDATE location_categories SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE location_categories SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Revert categories table timestamps
        DB::statement("UPDATE categories SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE categories SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");

        // Revert badge_layout_settings table timestamps
        if (Schema::hasTable('badge_layout_settings')) {
            DB::statement("UPDATE badge_layout_settings SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
            DB::statement("UPDATE badge_layout_settings SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");
        }

        // Revert badge_display_settings table timestamps
        if (Schema::hasTable('badge_display_settings')) {
            DB::statement("UPDATE badge_display_settings SET created_at = DATE_SUB(created_at, INTERVAL '5:30' HOUR_MINUTE) WHERE created_at IS NOT NULL");
            DB::statement("UPDATE badge_display_settings SET updated_at = DATE_SUB(updated_at, INTERVAL '5:30' HOUR_MINUTE) WHERE updated_at IS NOT NULL");
        }
    }
};
