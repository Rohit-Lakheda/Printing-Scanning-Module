<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeviceLogin extends Model
{
    protected $fillable = [
        'user_credential_id',
        'device_id',
        'device_name',
        'device_type',
        'ip_address',
        'last_login_at',
        'is_active',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function credential()
    {
        return $this->belongsTo(UserCredential::class, 'user_credential_id');
    }
}

