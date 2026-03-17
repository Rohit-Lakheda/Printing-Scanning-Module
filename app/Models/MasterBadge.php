<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterBadge extends Model
{
    protected $fillable = [
        'regid',
        'reason',
    ];

    /**
     * Get all locations where this master badge is allowed
     */
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'master_badge_locations')
            ->withTimestamps();
    }

    /**
     * Check if master badge is allowed at a specific location
     */
    public function isAllowedAt($locationId)
    {
        return $this->locations()->where('location_id', $locationId)->exists();
    }
}
