<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearDomainData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-domain-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all domain data (categories, user details, logs, layout/display settings, locations, blocked/bypassed/master regids, etc.) while keeping users and auth tables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('This will DELETE all event/registration/printing/scanning data.');
        $this->warn('Users and core auth tables will be kept.');

        if (! $this->confirm('Are you sure you want to continue?')) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        // Disable foreign key checks for MySQL/MariaDB
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'user_details',
            'categories',
            'printing_logs',
            'scanning_logs',
            'badge_layout_settings',
            'badge_display_settings',
            'locations',
            'location_categories',
            'blocked_regids',
            'bypassed_regids',
            'bypassed_regid_usage_logs',
            'master_badges',
            'api_configurations',
        ];

        foreach ($tables as $table) {
            try {
                DB::table($table)->truncate();
                $this->info("Truncated table: {$table}");
            } catch (\Throwable $e) {
                $this->warn("Could not truncate {$table}: {$e->getMessage()}");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Domain data cleared successfully.');

        return self::SUCCESS;
    }
}

