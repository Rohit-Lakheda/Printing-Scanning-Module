<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_details';

    protected $fillable = [
        'RegID',
        'Category',
        'DataFrom',
        'ReceiptNumber',
        'Name',
        'Designation',
        'Company',
        'Country',
        'State',
        'City',
        'Email',
        'Mobile',
        'Additional1',
        'Additional2',
        'Additional3',
        'Additional4',
        'Additional5',
        'IsLunchAllowed',
        'Data_Received_At',
        'Badge_Printed_At',
    ];

    protected $casts = [
        'IsLunchAllowed' => 'boolean',
        'Data_Received_At' => 'datetime',
        'Badge_Printed_At' => 'datetime',
    ];
}
