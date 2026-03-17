<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BypassedRegid extends Model
{
    protected $fillable = [
        'regid',
        'reason',
        'max_uses',
    ];

    protected $casts = [
        'max_uses' => 'integer',
    ];

    /**
     * Get all locations where this RegID is bypassed
     */
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'bypassed_regid_locations')
            ->withTimestamps();
    }

    /**
     * Get usage logs for this bypassed RegID
     */
    public function usageLogs()
    {
        return $this->hasMany(BypassedRegidUsageLog::class);
    }

    /**
     * Check if RegID is bypassed at a specific location
     */
    public function isBypassedAt($locationId)
    {
        return $this->locations()->where('location_id', $locationId)->exists();
    }

    /**
     * Get the number of times this RegID has been used at a specific location
     */
    public function getUsageCountAt($locationId)
    {
        return $this->usageLogs()
            ->where('location_id', $locationId)
            ->count();
    }

    /**
     * Get the effective max uses (null means 1 use)
     */
    public function getEffectiveMaxUses()
    {
        return $this->max_uses === null ? 1 : $this->max_uses;
    }

    /**
     * Check if RegID can still be bypassed at a specific location
     */
    public function canBeBypassedAt($locationId)
    {
        // Get effective max uses (null = 1 use)
        $effectiveMaxUses = $this->getEffectiveMaxUses();

        // Check if current usage count is less than effective max uses
        $usageCount = $this->getUsageCountAt($locationId);
        return $usageCount < $effectiveMaxUses;
    }

    /**
     * Check if RegID has already been used at a specific location (for backward compatibility)
     */
    public function hasBeenUsedAt($locationId)
    {
        // Check if we've reached effective max uses
        return !$this->canBeBypassedAt($locationId);
    }

    /**
     * Mark this RegID as used at a specific location
     */
    public function markAsUsedAt($locationId)
    {
        // Always log the usage
        BypassedRegidUsageLog::create([
            'bypassed_regid_id' => $this->id,
            'location_id' => $locationId,
            'used_at' => now(),
        ]);
    }
}
