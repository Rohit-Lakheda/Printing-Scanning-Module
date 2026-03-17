<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSetting extends Model
{
    protected $fillable = [
        'name',
        'share_RegID',
        'share_Name',
        'share_Category',
        'share_Company',
        'share_Email',
        'share_Mobile',
        'share_Designation',
        'share_Country',
        'share_State',
        'share_City',
        'share_Additional1',
        'share_Additional2',
        'share_Additional3',
        'share_Additional4',
        'share_Additional5',
        'credential_email_subject',
        'credential_email_body',
    ];

    public static function getDefault(): self
    {
        return static::firstOrCreate(
            ['name' => 'default'],
            [
                'share_RegID' => true,
                'share_Name' => true,
                'share_Category' => true,
                'share_Company' => true,
                'share_Email' => true,
                'share_Mobile' => true,
            ]
        );
    }

    /**
     * Return the list of user_details fields that are configured to be shared.
     *
     * @return array<string>
     */
    public function sharedFields(): array
    {
        $all = [
            'RegID', 'Name', 'Category', 'Company', 'Email', 'Mobile',
            'Designation', 'Country', 'State', 'City',
            'Additional1', 'Additional2', 'Additional3', 'Additional4', 'Additional5',
        ];

        $visible = [];
        foreach ($all as $field) {
            $prop = 'share_' . $field;
            if ($this->{$prop}) {
                $visible[] = $field;
            }
        }

        return $visible;
    }
}

