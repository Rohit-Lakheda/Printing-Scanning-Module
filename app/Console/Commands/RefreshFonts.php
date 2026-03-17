<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshFonts extends Command
{
    protected $signature = 'font:refresh';

    protected $description = 'Remove old dompdf font cache and re-load Tw Cen MT from TTF files in storage/fonts (TCCM____.TTF, TCCB____.TTF)';

    public function handle(): int
    {
        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            $this->warn('storage/fonts directory does not exist.');
            return self::FAILURE;
        }

        $removed = [];
        $patterns = [
            'tw_cen_mt_*.ttf',
            'tw_cen_mt_*.ufm',
            'TCCM____.ufm',
            'TCCB____.ufm',
            'installed-fonts.json',
        ];
        foreach ($patterns as $pattern) {
            foreach (glob($fontDir . '/' . $pattern) ?: [] as $path) {
                if (@unlink($path)) {
                    $removed[] = basename($path);
                }
            }
        }

        if (!empty($removed)) {
            $this->info('Removed: ' . implode(', ', $removed));
        } else {
            $this->info('No cached font files to remove.');
        }

        if (!file_exists($fontDir . '/TCCM____.TTF') || !file_exists($fontDir . '/TCCB____.TTF')) {
            $this->warn('Tw Cen MT TTF files not found. Place TCCM____.TTF and TCCB____.TTF in storage/fonts/ then run: php artisan font:load-twcen');
            return self::SUCCESS;
        }

        $this->info('Re-loading Tw Cen MT...');
        return $this->call('font:load-twcen');
    }
}
