<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'Prefix',
        'Category',
        'badge_width',
        'badge_height',
        'unique_printing',
        'receipt_number_required',
    ];

    protected $casts = [
        'badge_width' => 'decimal:2',
        'badge_height' => 'decimal:2',
        'unique_printing' => 'boolean',
        'receipt_number_required' => 'boolean',
    ];
}
