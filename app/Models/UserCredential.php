<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCredential extends Model
{
    protected $fillable = [
        'user_detail_id',
        'username',
        'password',
        'remember_token',
        'max_devices',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class);
    }

    public function deviceLogins()
    {
        return $this->hasMany(UserDeviceLogin::class);
    }
}

