<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadScan extends Model
{
    protected $fillable = [
        'user_detail_id',
        'regid',
        'scanned_at',
        'device_id',
        'scanned_by_user_id',
        'source',
        'location_name',
        'lead_type',
        'lead_comments',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class);
    }
}

