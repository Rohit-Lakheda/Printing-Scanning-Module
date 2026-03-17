<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserDetailsTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'RegID',
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
            'ReceiptNumber',
        ];
    }

    public function array(): array
    {
        return [
            [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
        ];
    }
}

