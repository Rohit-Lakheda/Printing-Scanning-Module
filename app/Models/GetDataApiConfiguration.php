<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetDataApiConfiguration extends Model
{
    protected $fillable = [
        'name',
        'api_key',
        'is_active',
        'input_fields',
        'response_fields',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'input_fields' => 'array',
        'response_fields' => 'array',
    ];

    public static function generateApiKey(): string
    {
        do {
            $key = 'get_' . bin2hex(random_bytes(16));
        } while (self::where('api_key', $key)->exists());

        return $key;
    }
}

