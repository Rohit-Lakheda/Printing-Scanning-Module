<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintingLog;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PrintingReportController extends Controller
{
    public function download(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'category' => 'nullable|string',
            'print_type' => 'nullable|in:single,bulk',
        ]);

        $query = PrintingLog::query();

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->whereDate('printed_at', '>=', $validated['date_from']);
        }
        if (!empty($validated['date_to'])) {
            $query->whereDate('printed_at', '<=', $validated['date_to']);
        }
        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }
        if (!empty($validated['print_type'])) {
            $query->where('print_type', $validated['print_type']);
        }

        $logs = $query->orderBy('printed_at', 'desc')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Printing Report');

        // Header row
        $headers = ['S.No', 'Printed At', 'RegID', 'User Name', 'Category', 'Print Type'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10b981']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(25);

        // Data rows
        $row = 2;
        foreach ($logs as $index => $log) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $log->printed_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'));
            $sheet->setCellValue('C' . $row, $log->regid);
            $sheet->setCellValue('D' . $row, $log->user_name);
            $sheet->setCellValue('E' . $row, $log->category);
            $sheet->setCellValue('F' . $row, ucfirst($log->print_type));

            // Alternate row colors
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f9fafb');
            }

            // Print type color
            $typeColor = $log->print_type === 'bulk' ? '6366f1' : '3b82f6';
            $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB($typeColor);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $lastRow = $row - 1;
        $sheet->getStyle('A1:F' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Set alignment
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Generate filename
        $filename = 'printing_report_' . date('Ymd_His') . '.xlsx';

        // Write file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'printing_report');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
