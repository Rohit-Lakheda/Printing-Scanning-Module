<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadScanAttemptLog extends Model
{
    protected $fillable = [
        'scanned_by_user_id',
        'lead_scan_id',
        'regid',
        'status',
        'message',
        'scanned_at',
        'source',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];
}

