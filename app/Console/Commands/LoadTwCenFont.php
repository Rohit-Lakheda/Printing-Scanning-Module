<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Dompdf\Dompdf;
use Dompdf\Options;
use Dompdf\FontMetrics;

class LoadTwCenFont extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'font:load-twcen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Tw Cen MT font into dompdf for PDF generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Loading Tw Cen MT font into dompdf...');

        $fontDir = storage_path('fonts');
        
        // Ensure fonts directory exists
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        // Font file names - mapping to dompdf font naming convention
        $fontFiles = [
            ['file' => 'TCCM____.TTF', 'family' => 'Tw Cen MT', 'style' => 'normal', 'weight' => 'normal'],
            ['file' => 'TCCB____.TTF', 'family' => 'Tw Cen MT', 'style' => 'normal', 'weight' => 'bold'],
        ];

        $options = new Options();
        $options->set('fontDir', $fontDir);
        $options->set('fontCache', $fontDir);

        $dompdf = new Dompdf($options);
        $fontMetrics = $dompdf->getFontMetrics();

        $loaded = 0;
        $failed = 0;

        foreach ($fontFiles as $fontConfig) {
            $fontPath = $fontDir . '/' . $fontConfig['file'];
            
            if (!file_exists($fontPath)) {
                $this->warn("Font file not found: {$fontPath}");
                $this->info("Please place the font file at: {$fontPath}");
                $failed++;
                continue;
            }

            try {
                // Load the TTF font file
                $font = \FontLib\Font::load($fontPath);
                $font->parse();
                
                // Generate font name prefix (dompdf format: family_style_weight_hash)
                $fontName = mb_strtolower($fontConfig['family'], "UTF-8");
                $styleString = $fontConfig['weight'] . ' ' . $fontConfig['style'];
                $prefix = str_replace(' ', '_', $fontName) . '_' . str_replace(' ', '_', $styleString);
                $prefix = preg_replace("/[^-_\w]+/", "", $prefix);
                $fontHash = md5_file($fontPath);
                $localFileName = $prefix . '_' . substr($fontHash, 0, 8);
                
                // Copy font to dompdf expected location
                $dompdfFontPath = $fontDir . '/' . $localFileName . '.ttf';
                copy($fontPath, $dompdfFontPath);
                
                // Generate the font metric file (.ufm) with dompdf naming
                $ufmPath = $fontDir . '/' . $localFileName . '.ufm';
                $font->saveAdobeFontMetrics($ufmPath);
                $font->close();
                
                // Verify files were created
                if (!file_exists($dompdfFontPath) || !file_exists($ufmPath)) {
                    throw new \Exception("Failed to create font files");
                }
                
                // Manually add to font registry
                $fontFamilies = $fontMetrics->getFontFamilies();
                $fontNameLower = mb_strtolower($fontConfig['family'], "UTF-8");
                if (!isset($fontFamilies[$fontNameLower])) {
                    $fontFamilies[$fontNameLower] = [];
                }
                $fontFamilies[$fontNameLower][$styleString] = $localFileName . '.ttf';
                
                // Update userFonts in FontMetrics
                $reflection = new \ReflectionClass($fontMetrics);
                $userFontsProperty = $reflection->getProperty('userFonts');
                $userFontsProperty->setAccessible(true);
                $userFonts = $userFontsProperty->getValue($fontMetrics);
                $userFonts[$fontNameLower][$styleString] = $localFileName . '.ttf';
                $userFontsProperty->setValue($fontMetrics, $userFonts);
                
                $this->info("✓ Loaded: {$fontConfig['file']} ({$fontConfig['weight']})");
                $this->info("  Registered as: {$localFileName}.ttf");
                $loaded++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to load {$fontConfig['file']}: " . $e->getMessage());
                $failed++;
            }
        }

        if ($loaded > 0) {
            // Save font families to registry
            try {
                $fontMetrics->saveFontFamilies();
                $this->info("\nFont loading completed! {$loaded} font(s) loaded successfully.");
                $this->info("Font families registry updated.");
            } catch (\Exception $e) {
                $this->warn("\nFont loaded but registry update failed: " . $e->getMessage());
            }
            $this->info("Font metric files generated in: {$fontDir}");
        }

        if ($failed > 0) {
            $this->warn("\n{$failed} font file(s) could not be loaded. Please check the file paths.");
        }

        // Check if .ufm files were generated
        $this->info("\nChecking for generated font metric files...");
        foreach ($fontFiles as $fontConfig) {
            $baseName = pathinfo($fontConfig['file'], PATHINFO_FILENAME);
            $ufmFile = $fontDir . '/' . $baseName . '.ufm';
            if (file_exists($ufmFile)) {
                $this->info("✓ Found metric file: " . basename($ufmFile));
            } else {
                $this->warn("✗ Metric file not found: " . basename($ufmFile));
            }
        }

        return 0;
    }
}
