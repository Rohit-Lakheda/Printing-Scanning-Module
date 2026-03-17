<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeDisplaySetting extends Model
{
    protected $fillable = [
        'Category',
        'layout_type',
        'ShowCategory', // Field to display category name on badge
        'RegID',
        'Name',
        'Email',
        'Mobile',
        'Designation',
        'Company',
        'Country',
        'State',
        'City',
        'Additional1',
        'Additional2',
        'Additional3',
        'Additional4',
        'Additional5',
        'IsUniquePrint',
        'QRcode',
    ];

    protected $casts = [
        'ShowCategory' => 'boolean', // Field to display category name on badge
        'RegID' => 'boolean',
        'Name' => 'boolean',
        'Email' => 'boolean',
        'Mobile' => 'boolean',
        'Designation' => 'boolean',
        'Company' => 'boolean',
        'Country' => 'boolean',
        'State' => 'boolean',
        'City' => 'boolean',
        'Additional1' => 'boolean',
        'Additional2' => 'boolean',
        'Additional3' => 'boolean',
        'Additional4' => 'boolean',
        'Additional5' => 'boolean',
        'IsUniquePrint' => 'boolean',
        'QRcode' => 'boolean',
    ];
}
