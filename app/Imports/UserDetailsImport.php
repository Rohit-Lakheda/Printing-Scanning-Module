<?php

namespace App\Imports;

use App\Models\UserDetail;
use App\Services\RegIdGenerator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserDetailsImport implements ToCollection, WithHeadingRow
{
    public function __construct(
        protected string $categoryName
    ) {
    }

    public function collection(Collection $rows)
    {
        $now = Carbon::now();

        foreach ($rows as $row) {
            $regId = $this->asStringOrNull($row['regid'] ?? null);

            if ($regId === null || trim($regId) === '') {
                $regId = RegIdGenerator::generateForCategory($this->categoryName);
            }

            $data = [
                'RegID' => trim($regId),
                'Category' => $this->categoryName,
                'DataFrom' => 'Import Excel',
                'ReceiptNumber' => null,
                'Name' => $this->asStringOrEmpty($row['name'] ?? null),
                'Designation' => $this->asStringOrNull($row['designation'] ?? null),
                'Company' => $this->asStringOrNull($row['company'] ?? null),
                'Country' => $this->asStringOrNull($row['country'] ?? null),
                'State' => $this->asStringOrNull($row['state'] ?? null),
                'City' => $this->asStringOrNull($row['city'] ?? null),
                'Email' => $this->asStringOrNull($row['email'] ?? null),
                'Mobile' => $this->asStringOrNull($row['mobile'] ?? null),
                'Additional1' => $this->asStringOrNull($row['additional1'] ?? null),
                'Additional2' => $this->asStringOrNull($row['additional2'] ?? null),
                'Additional3' => $this->asStringOrNull($row['additional3'] ?? null),
                'Additional4' => $this->asStringOrNull($row['additional4'] ?? null),
                'Additional5' => $this->asStringOrNull($row['additional5'] ?? null),
                'IsLunchAllowed' => $this->asBool($row['islunchallowed'] ?? null),
                'Data_Received_At' => $now,
            ];

            // Skip completely empty lines (except RegID auto-generation)
            $isCompletelyEmpty = true;
            foreach ([
                'Name', 'Designation', 'Company', 'Country', 'State', 'City', 'Email', 'Mobile',
                'Additional1', 'Additional2', 'Additional3', 'Additional4', 'Additional5',
            ] as $field) {
                if ($data[$field] !== null && $data[$field] !== '') {
                    $isCompletelyEmpty = false;
                    break;
                }
            }
            if ($isCompletelyEmpty) {
                continue;
            }

            // If RegID already exists, skip that row (avoid breaking whole import)
            if (UserDetail::where('RegID', $data['RegID'])->exists()) {
                continue;
            }

            UserDetail::create($data);
        }
    }

    private function asStringOrNull($value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value)) {
            $v = trim($value);
            return $v === '' ? null : $v;
        }
        if (is_numeric($value)) {
            return (string) $value;
        }

        return null;
    }

    private function asStringOrEmpty($value): string
    {
        $v = $this->asStringOrNull($value);
        return $v ?? '';
    }

    private function asBool($value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return ((int) $value) === 1;
        }

        if (is_string($value)) {
            $v = strtolower(trim($value));
            return in_array($v, ['1', 'true', 'yes', 'y', 'on'], true);
        }

        return false;
    }
}

