<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationCategory extends Model
{
    protected $fillable = [
        'location_id',
        'category',
    ];

    /**
     * Get the location that owns this category mapping
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
