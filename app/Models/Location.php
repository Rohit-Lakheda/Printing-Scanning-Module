<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'unique_scanning',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'unique_scanning' => 'boolean',
    ];

    /**
     * Get all categories allowed at this location
     */
    public function allowedCategories()
    {
        return $this->hasMany(LocationCategory::class);
    }

    /**
     * Check if a category is allowed at this location
     */
    public function isCategoryAllowed($category)
    {
        return $this->allowedCategories()->where('category', $category)->exists();
    }

    /**
     * Get all blocked RegIDs for this location
     */
    public function blockedRegids()
    {
        return $this->belongsToMany(BlockedRegid::class, 'blocked_regid_locations')
            ->withTimestamps();
    }

    /**
     * Get all master badges for this location
     */
    public function masterBadges()
    {
        return $this->belongsToMany(MasterBadge::class, 'master_badge_locations')
            ->withTimestamps();
    }
}
