# How to Install Tw Cen MT Font for PDF Generation

## Step 1: Get the Font Files

You need to obtain the Tw Cen MT font files (.ttf format). The font files you need are:
- `TCCM____.TTF` (Tw Cen MT Regular)
- `TCCB____.TTF` (Tw Cen MT Bold) - if you need bold variant

**Note:** Make sure you have the legal right to use these font files.

## Step 2: Save Font Files

Save the font files (.ttf) in the following directory:
```
storage/fonts/
```

So the path should be:
- `storage/fonts/TCCM____.TTF` (Regular)
- `storage/fonts/TCCB____.TTF` (Bold)

## Step 3: Load Font into dompdf

Use the Artisan command that has been created for you:

```bash
php artisan font:load-twcen
```

This command will:
- Check if the font files exist in `storage/fonts/`
- Load them into dompdf's font system
- Generate the necessary font metric files (.ufm)

**Important:** Make sure the font files are named exactly:
- `TCCM____.TTF` for regular
- `TCCB____.TTF` for bold

If your font files have different names, you can edit `app/Console/Commands/LoadTwCenFont.php` and update the `$fontFiles` array.

## Step 4: Verify Font is Available

After loading, check if the font files are in `storage/fonts/` and that the font metrics files (.ufm) are generated.

## Step 5: Use in PDF

The font should now be available in your PDF templates. Make sure your CSS uses:
```css
font-family: 'Tw Cen MT', sans-serif;
```

## Roboto (open-source, works in PDF without licensing)

1. Download **Roboto** from Google Fonts: https://fonts.google.com/specimen/Roboto → click **Download family** (ZIP).
2. Extract from the ZIP and copy into `storage/fonts/`:
   - `Roboto-Regular.ttf`
   - `Roboto-Bold.ttf`
3. Run:
   ```bash
   php artisan font:load-roboto
   ```
4. In Layout Editor, choose **Roboto** from the font dropdown. It will render correctly in bulk and single PDFs.

## If Tw Cen MT stops working (refresh cache)

Clear dompdf’s font cache and re-load Tw Cen MT from your TTF files:

```bash
php artisan font:refresh
```

This removes cached `tw_cen_mt_*` and `installed-fonts.json`, then runs `font:load-twcen` again. Ensure `TCCM____.TTF` and `TCCB____.TTF` are in `storage/fonts/` (e.g. copy from `C:\Windows\Fonts` if you have them).

## Troubleshooting

1. **Font not appearing**: Make sure the font files are in `storage/fonts/` directory
2. **Permission errors**: Ensure `storage/fonts/` is writable by the web server
3. **Font fallback**: If Tw Cen MT is not found, dompdf will fall back to the default font (serif/Times-Roman)
4. **Tw Cen MT in PDF**: Run `php artisan font:refresh` then try again

## Alternative: Use Font Subsetting

If you want to reduce PDF file size, you can enable font subsetting in `vendor/barryvdh/laravel-dompdf/config/dompdf.php`:
```php
'enable_font_subsetting' => true,
```
