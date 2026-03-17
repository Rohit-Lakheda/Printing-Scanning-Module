<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Dompdf\Dompdf;
use Dompdf\Options;

class LoadRobotoFont extends Command
{
    protected $signature = 'font:load-roboto';

    protected $description = 'Load Roboto font (open-source) into dompdf. Place Roboto-Regular.ttf and Roboto-Bold.ttf in storage/fonts/ first.';

    public function handle(): int
    {
        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        $fontFiles = [
            ['file' => 'Roboto-Regular.ttf', 'family' => 'Roboto', 'style' => 'normal', 'weight' => 'normal'],
            ['file' => 'Roboto-Bold.ttf', 'family' => 'Roboto', 'style' => 'normal', 'weight' => 'bold'],
        ];

        $options = new Options();
        $options->set('fontDir', $fontDir);
        $options->set('fontCache', $fontDir);
        $dompdf = new Dompdf($options);
        $fontMetrics = $dompdf->getFontMetrics();

        $loaded = 0;
        foreach ($fontFiles as $fontConfig) {
            $fontPath = $fontDir . '/' . $fontConfig['file'];
            if (!file_exists($fontPath)) {
                $this->warn("Not found: {$fontConfig['file']}. Download from https://fonts.google.com/specimen/Roboto (Download family), extract into storage/fonts/.");
                continue;
            }

            try {
                $font = \FontLib\Font::load($fontPath);
                $font->parse();
                $fontName = mb_strtolower($fontConfig['family'], 'UTF-8');
                $styleString = $fontConfig['weight'] . ' ' . $fontConfig['style'];
                $prefix = str_replace(' ', '_', $fontName) . '_' . str_replace(' ', '_', $styleString);
                $prefix = preg_replace("/[^-_\w]+/", "", $prefix);
                $fontHash = md5_file($fontPath);
                $localFileName = $prefix . '_' . substr($fontHash, 0, 8);

                $dompdfFontPath = $fontDir . '/' . $localFileName . '.ttf';
                copy($fontPath, $dompdfFontPath);
                $ufmPath = $fontDir . '/' . $localFileName . '.ufm';
                $font->saveAdobeFontMetrics($ufmPath);
                $font->close();

                $reflection = new \ReflectionClass($fontMetrics);
                $userFontsProperty = $reflection->getProperty('userFonts');
                $userFontsProperty->setAccessible(true);
                $userFonts = $userFontsProperty->getValue($fontMetrics) ?: [];
                $userFonts[$fontName][$styleString] = $localFileName . '.ttf';
                $userFontsProperty->setValue($fontMetrics, $userFonts);

                $this->info("Loaded: {$fontConfig['file']} ({$fontConfig['weight']})");
                $loaded++;
            } catch (\Throwable $e) {
                $this->error("Failed {$fontConfig['file']}: " . $e->getMessage());
            }
        }

        if ($loaded > 0) {
            $fontMetrics->saveFontFamilies();
            $this->info("Roboto registered. Use font-family: 'Roboto' in layout.");
        }

        return $loaded > 0 ? self::SUCCESS : self::FAILURE;
    }
}
