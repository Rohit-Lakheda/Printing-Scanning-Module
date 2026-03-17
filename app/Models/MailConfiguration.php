<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailConfiguration extends Model
{
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'use_auth',
        'is_active',
    ];

    protected $casts = [
        'use_auth' => 'boolean',
        'is_active' => 'boolean',
    ];
}

