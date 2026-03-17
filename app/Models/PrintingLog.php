<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintingLog extends Model
{
    protected $fillable = [
        'regid',
        'user_name',
        'category',
        'print_type',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];
}
