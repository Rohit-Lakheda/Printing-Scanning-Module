<?php

namespace App\Services;

use App\Models\Category;
use App\Models\UserDetail;

class RegIdGenerator
{
    public static function generateForCategory(string $categoryName): string
    {
        $category = Category::where('Category', $categoryName)->first();
        $prefix = $category?->Prefix ?? '';

        $maxRegNumber = 0;
        $regIds = UserDetail::where('Category', $categoryName)->pluck('RegID');

        foreach ($regIds as $regID) {
            if (!is_string($regID) || $regID === '') {
                continue;
            }

            if ($prefix !== '' && strpos($regID, $prefix) === 0) {
                $numericPart = substr($regID, strlen($prefix));
                if (preg_match('/^(\d+)/', $numericPart, $matches)) {
                    $num = (int) $matches[1];
                    $maxRegNumber = max($maxRegNumber, $num);
                }
            } else {
                if (preg_match('/(\d+)$/', $regID, $matches)) {
                    $num = (int) $matches[1];
                    $maxRegNumber = max($maxRegNumber, $num);
                }
            }
        }

        $regNumber = $maxRegNumber + 1;
        $newRegID = $prefix.str_pad((string) $regNumber, 4, '0', STR_PAD_LEFT);

        $attempts = 0;
        while (UserDetail::where('RegID', $newRegID)->exists() && $attempts < 100) {
            $regNumber++;
            $newRegID = $prefix.str_pad((string) $regNumber, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }

        return $newRegID;
    }
}

