<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanningLog extends Model
{
    protected $fillable = [
        'location_id',
        'location_name',
        'regid',
        'user_name',
        'category',
        'is_allowed',
        'reason',
        'scanned_at',
    ];

    protected $casts = [
        'is_allowed' => 'boolean',
        'scanned_at' => 'datetime',
    ];

    /**
     * Get the location that this log belongs to
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
