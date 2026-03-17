<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSetting extends Model
{
    protected $fillable = [
        'logo_path',
        'scanning_type',
    ];

    /**
     * Get the current event settings (singleton pattern)
     */
    public static function getSettings()
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
