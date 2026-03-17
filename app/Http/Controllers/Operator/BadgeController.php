<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use App\Models\BadgeDisplaySetting;
use App\Models\BadgeLayoutSetting;
use App\Models\Category;
use App\Models\PrintingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class BadgeController extends Controller
{
    public function menu()
    {
        return view('operator.badge.menu');
    }

    public function scanPrint()
    {
        return view('operator.badge.scan-print');
    }

    public function searchPrint(Request $request)
    {
        $query = UserDetail::query();

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('Category', $request->category);
        }

        // Apply global search across all columns
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('RegID', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Mobile', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Company', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Designation', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Country', 'like', '%' . $searchTerm . '%')
                  ->orWhere('State', 'like', '%' . $searchTerm . '%')
                  ->orWhere('City', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Category', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Additional1', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Additional2', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Additional3', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Additional4', 'like', '%' . $searchTerm . '%')
                  ->orWhere('Additional5', 'like', '%' . $searchTerm . '%')
                  ->orWhere('ReceiptNumber', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->orderBy('RegID')->paginate(50)->withQueryString();

        return view('operator.badge.search-print', compact('users'));
    }

    public function bulkForm()
    {
        $category = request()->query('Category');
        $users = collect();

        if ($category) {
            $users = UserDetail::where('Category', $category)
                ->orderBy('RegID')
                ->get([
                    'RegID',
                    'Category',
                    'Name',
                    'Designation',
                    'Company',
                    'Country',
                    'State',
                    'City',
                    'Additional1',
                    'Additional2',
                    'Additional3',
                    'Additional4',
                    'Additional5',
                ]);
            
            // Add print counts from printing logs
            $printCounts = PrintingLog::where('category', $category)
                ->select('regid', DB::raw('COUNT(*) as print_count'))
                ->groupBy('regid')
                ->pluck('print_count', 'regid');
            
            $users = $users->map(function($user) use ($printCounts) {
                $user->print_count = $printCounts[$user->RegID] ?? 0;
                return $user;
            });
        }

        return view('operator.badge.bulk', [
            'selectedCategory' => $category,
            'users'            => $users,
        ]);
    }

    public function print(Request $request)
    {
        $regid = $request->input('regid') ?? $request->query('regid');
        
        if (!$regid) {
            return redirect()->route('operator.badge.scan-print')
                ->with('error', 'Please provide a registration ID.');
        }

        $userDetail = UserDetail::where('RegID', $regid)->first();

        if (!$userDetail) {
            return redirect()->route('operator.badge.scan-print')
                ->with('error', 'Registration ID not found.');
        }

        $category = Category::where('Category', $userDetail->Category)->first();
        
        // Check if unique printing is enabled for this category
        if ($category && $category->unique_printing) {
            $alreadyPrinted = PrintingLog::where('regid', $userDetail->RegID)
                ->where('category', $userDetail->Category)
                ->exists();
            
            if ($alreadyPrinted) {
                $firstPrintTime = PrintingLog::where('regid', $userDetail->RegID)
                    ->where('category', $userDetail->Category)
                    ->orderBy('printed_at', 'asc')
                    ->first()
                    ->printed_at;
                
                return redirect()->route('operator.badge.scan-print')
                    ->with('error', 'This badge is already printed for this category. First printed at: ' . $firstPrintTime->format('Y-m-d H:i:s'));
            }
        }
        
        $displaySettings = BadgeDisplaySetting::where('Category', $userDetail->Category)
            ->where('layout_type', 'normal')
            ->first();
        $layoutSettings = BadgeLayoutSetting::where('Category', $userDetail->Category)
            ->where('layout_type', 'normal')
            ->orderBy('sequence')
            ->get()
            ->keyBy('field_name');

        // Get visible fields
        $visibleFields = [];
        if ($displaySettings) {
            $fields = ['Category', 'RegID', 'Name', 'Email', 'Mobile', 'Designation', 'Company', 
                      'Country', 'State', 'City', 'Additional1', 'Additional2', 
                      'Additional3', 'Additional4', 'Additional5', 'QRcode'];
            
            foreach ($fields as $field) {
                // Handle ShowCategory -> Category mapping
                $checkField = ($field === 'Category') ? 'ShowCategory' : $field;
                if (isset($displaySettings->$checkField) && $displaySettings->$checkField) {
                    $visibleFields[] = $field;
                }
            }
            
            // Add static text labels if they exist
            $staticLayouts = BadgeLayoutSetting::where('Category', $userDetail->Category)
                ->where('layout_type', 'normal')
                ->whereNotNull('static_text_value')
                ->where('static_text_value', '!=', '')
                ->get();
            
            foreach ($staticLayouts as $staticLayout) {
                if (!in_array($staticLayout->field_name, $visibleFields)) {
                    $visibleFields[] = $staticLayout->field_name;
                }
            }
        }

        // Generate QR code for browser print (SVG preferred, PNG fallback)
        $qrCode = null;
        $qrCodeType = null;
        if ($displaySettings && $displaySettings->QRcode) {
            try {
                // Try SVG first (no extension required)
                $qrCode = QrCode::format('svg')->size(200)->generate($userDetail->RegID);
                $qrCodeType = 'svg';
            } catch (\Exception $e) {
                // Fallback to PNG if SVG fails
                try {
                    $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($userDetail->RegID));
                    $qrCodeType = 'png';
                } catch (\Exception $e2) {
                    // If both fail, set to null
                    $qrCode = null;
                    $qrCodeType = null;
                }
            }
        }

        // Log the print
        PrintingLog::create([
            'regid' => $userDetail->RegID,
            'user_name' => $userDetail->Name,
            'category' => $userDetail->Category,
            'print_type' => 'single',
            'printed_at' => now(),
        ]);

        // Update timestamp
        $userDetail->update(['Badge_Printed_At' => now()]);

        // Determine redirect URL based on referrer
        $redirectUrl = route('operator.badge.scan-print');
        $referer = request()->header('referer');
        if ($referer) {
            if (str_contains($referer, 'registration')) {
                $redirectUrl = route('operator.registration.create');
            } elseif (str_contains($referer, 'search-print')) {
                $query = parse_url($referer, PHP_URL_QUERY);
                $redirectUrl = route('operator.badge.search-print') . ($query ? ('?' . $query) : '');
            }
        }

        return view('operator.badge.print', compact(
            'userDetail',
            'category',
            'layoutSettings',
            'visibleFields',
            'qrCode',
            'qrCodeType',
            'redirectUrl'
        ));
    }

    /**
     * Bulk print badges as a single PDF for a category.
     */
    public function bulkPrint(Request $request)
    {
        $validated = $request->validate([
            'Category'   => 'required|string|exists:categories,Category',
            'regid_from' => 'nullable|string',
            'regid_to'   => 'nullable|string',
            'selected_regids' => 'nullable|array',
            'selected_regids.*' => 'string',
        ]);

        $category = Category::where('Category', $validated['Category'])->firstOrFail();
        $displaySettings = BadgeDisplaySetting::where('Category', $category->Category)
            ->where('layout_type', 'bulk')
            ->first();
        $layoutSettings = BadgeLayoutSetting::where('Category', $category->Category)
            ->where('layout_type', 'bulk')
            ->orderBy('sequence')
            ->get()
            ->keyBy('field_name');

        // Visible fields
        $visibleFields = [];
        if ($displaySettings) {
            $fields = ['Category', 'RegID', 'Name', 'Email', 'Mobile', 'Designation', 'Company',
                       'Country', 'State', 'City', 'Additional1', 'Additional2',
                       'Additional3', 'Additional4', 'Additional5', 'QRcode'];

            foreach ($fields as $field) {
                // Handle ShowCategory -> Category mapping
                $checkField = ($field === 'Category') ? 'ShowCategory' : $field;
                if (isset($displaySettings->$checkField) && $displaySettings->$checkField) {
                    $visibleFields[] = $field;
                }
            }
            
            // Add static text labels if they exist
            $staticLayouts = BadgeLayoutSetting::where('Category', $category->Category)
                ->where('layout_type', 'bulk')
                ->whereNotNull('static_text_value')
                ->where('static_text_value', '!=', '')
                ->get();
            
            foreach ($staticLayouts as $staticLayout) {
                if (!in_array($staticLayout->field_name, $visibleFields)) {
                    $visibleFields[] = $staticLayout->field_name;
                }
            }
        }

        // Build user query
        $query = UserDetail::where('Category', $category->Category)->orderBy('id');

        // If specific RegIDs were selected from grid, use them
        if (!empty($validated['selected_regids'])) {
            $query->whereIn('RegID', $validated['selected_regids']);
        } else {
            if (!empty($validated['regid_from'])) {
                $query->where('RegID', '>=', $validated['regid_from']);
            }
            if (!empty($validated['regid_to'])) {
                $query->where('RegID', '<=', $validated['regid_to']);
            }
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            return redirect()->route('operator.badge.search-print')
                ->with('error', 'No badges found for the selected criteria.');
        }

        // Filter out already printed badges if unique printing is enabled
        $skippedCount = 0;
        if ($category && $category->unique_printing) {
            $alreadyPrintedRegids = PrintingLog::where('category', $category->Category)
                ->whereIn('regid', $users->pluck('RegID'))
                ->pluck('regid')
                ->unique()
                ->toArray();
            
            $users = $users->filter(function($user) use ($alreadyPrintedRegids) {
                return !in_array($user->RegID, $alreadyPrintedRegids);
            });
            
            $skippedCount = count($alreadyPrintedRegids);
        }

        if ($users->isEmpty()) {
            return redirect()->route('operator.badge.bulk.form', ['Category' => $category->Category])
                ->with('error', 'All selected badges have already been printed for this category.');
        }

        // Precompute QR codes as data URIs (PNG preferred, SVG fallback)
        $qrCodes = [];
        if ($displaySettings && $displaySettings->QRcode) {
            foreach ($users as $user) {
                try {
                    // Try PNG first
                    $qrPng = QrCode::format('png')
                        ->size(200)
                        ->errorCorrection('H')
                        ->generate($user->RegID);
                    $qrBase64 = base64_encode($qrPng);
                    $qrCodes[$user->RegID] = 'data:image/png;base64,' . $qrBase64;
                } catch (\Exception $e) {
                    try {
                        // Fallback to SVG if PNG fails
                        $qrSvg = QrCode::format('svg')
                            ->size(200)
                            ->errorCorrection('H')
                            ->generate($user->RegID);
                        $qrSvgBase64 = base64_encode($qrSvg);
                        $qrCodes[$user->RegID] = 'data:image/svg+xml;base64,' . $qrSvgBase64;
                    } catch (\Exception $e2) {
                        \Log::error('QR Code generation failed for RegID: ' . $user->RegID . ' - PNG: ' . $e->getMessage() . ' SVG: ' . $e2->getMessage());
                        $qrCodes[$user->RegID] = null;
                    }
                }
            }
        }

        // Log all prints before generating PDF
        $printLogs = [];
        foreach ($users as $user) {
            PrintingLog::create([
                'regid' => $user->RegID,
                'user_name' => $user->Name,
                'category' => $user->Category,
                'print_type' => 'bulk',
                'printed_at' => now(),
            ]);
            
            // Update timestamp
            $user->update(['Badge_Printed_At' => now()]);
        }

        $pdf = Pdf::loadView('operator.badge.bulk-pdf', [
            'users'          => $users,
            'category'       => $category,
            'layoutSettings' => $layoutSettings,
            'visibleFields'  => $visibleFields,
            'qrCodes'        => $qrCodes,
        ])->setPaper([
            0,
            0,
            $category->badge_width * 2.83465,  // mm to points
            $category->badge_height * 2.83465, // mm to points
        ], 'portrait')
        ->setOption('margin-top', 0)
        ->setOption('margin-right', 0)
        ->setOption('margin-bottom', 0)
        ->setOption('margin-left', 0)
        ->setOption('enable-local-file-access', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true)
        ->setOption('fontDir', storage_path('fonts'))
        ->setOption('fontCache', storage_path('fonts'))
        ->setOption('enable-font-subsetting', true)
        ->setOption('defaultFont', 'Helvetica');

        $fileName = 'badges_' . $category->Category . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
