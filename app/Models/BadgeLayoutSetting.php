<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeLayoutSetting extends Model
{
    protected $fillable = [
        'Category',
        'layout_type',
        'field_name',
        'static_text_key',
        'static_text_value',
        'margin_top', // Line spacing - space between elements
        'sequence', // Print order
        'text_align', // left, center, right
        'font_family', // Font name
        'font_weight', // normal, bold
        'color', // Text color
        'font_size', // Font size in mm (for non-QR elements)
        'width', // QR code width in mm (QR only)
        'height', // QR code height in mm (QR only)
    ];

    protected $casts = [
        'margin_top' => 'decimal:2',
        'sequence' => 'integer',
        'font_size' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
    ];
}
