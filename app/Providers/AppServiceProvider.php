<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default timezone for Carbon to match application timezone
        Carbon::setLocale('en');
        date_default_timezone_set(config('app.timezone'));

        // Enforce DB session timezone to IST for consistent timestamp writes/reads.
        try {
            $driver = config('database.default');
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("SET time_zone = '+05:30'");
            }
        } catch (\Throwable $e) {
            // Ignore DB timezone setup failures during bootstrap edge-cases.
        }
    }
}
