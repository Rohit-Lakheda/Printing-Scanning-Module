<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScanningLog;
use App\Models\Location;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ScanningReportController extends Controller
{
    public function download(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'location_id' => 'nullable|exists:locations,id',
            'status' => 'nullable|in:allowed,denied',
            'category' => 'nullable|string',
        ]);

        $query = ScanningLog::query();

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->whereDate('scanned_at', '>=', $validated['date_from']);
        }
        if (!empty($validated['date_to'])) {
            $query->whereDate('scanned_at', '<=', $validated['date_to']);
        }
        if (!empty($validated['location_id'])) {
            $query->where('location_id', $validated['location_id']);
        }
        if (!empty($validated['status'])) {
            $query->where('is_allowed', $validated['status'] === 'allowed');
        }
        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        $logs = $query->orderBy('scanned_at', 'desc')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Scanning Report');

        // Header row
        $headers = ['S.No', 'Scanned At', 'Location', 'RegID', 'User Name', 'Category', 'Status', 'Reason'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e40af']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(25);

        // Data rows
        $row = 2;
        foreach ($logs as $index => $log) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $log->scanned_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'));
            $sheet->setCellValue('C' . $row, $log->location_name);
            $sheet->setCellValue('D' . $row, $log->regid);
            $sheet->setCellValue('E' . $row, $log->user_name);
            $sheet->setCellValue('F' . $row, $log->category);
            $sheet->setCellValue('G' . $row, $log->is_allowed ? 'Allowed' : 'Denied');
            $sheet->setCellValue('H' . $row, $log->reason);

            // Alternate row colors
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f9fafb');
            }

            // Status color
            $statusColor = $log->is_allowed ? '10b981' : 'ef4444';
            $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB($statusColor);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $lastRow = $row - 1;
        $sheet->getStyle('A1:H' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Set alignment
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Generate filename
        $filename = 'scanning_report_' . date('Ymd_His') . '.xlsx';

        // Write file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'scanning_report');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
