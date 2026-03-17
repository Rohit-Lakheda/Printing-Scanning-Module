<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadPasswordReset extends Model
{
    protected $fillable = [
        'user_credential_id',
        'email',
        'token_hash',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function credential()
    {
        return $this->belongsTo(UserCredential::class, 'user_credential_id');
    }
}

