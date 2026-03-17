<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use App\Models\Category;
use App\Models\ScanningLog;
use App\Models\PrintingLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Overall stats from printing logs
        $totalRegistrations = UserDetail::count();
        
        // Total prints (all print attempts)
        $totalPrints = PrintingLog::count();
        
        // Unique users who have printed (distinct RegIDs)
        $usersWhoPrinted = PrintingLog::distinct('regid')->count('regid');
        $totalNotPrinted = $totalRegistrations - $usersWhoPrinted;
        
        // Unique prints (users printed exactly once)
        $uniquePrints = PrintingLog::select('regid')
            ->groupBy('regid')
            ->havingRaw('COUNT(*) = 1')
            ->count();
        
        // Duplicate prints (users printed more than once)
        $duplicatePrints = PrintingLog::select('regid')
            ->groupBy('regid')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        // Category-wise summary from printing logs
        $allCategories = Category::pluck('Category');
        $categoryStats = collect($allCategories)->map(function($category) {
            $totalInCategory = UserDetail::where('Category', $category)->count();
            $printedInCategory = PrintingLog::where('category', $category)->distinct('regid')->count('regid');
            
            // Get unique prints (printed exactly once)
            $uniquePrinted = PrintingLog::where('category', $category)
                ->select('regid')
                ->groupBy('regid')
                ->havingRaw('COUNT(*) = 1')
                ->count();
            
            // Get duplicate prints (printed more than once)
            $duplicatePrinted = PrintingLog::where('category', $category)
                ->select('regid')
                ->groupBy('regid')
                ->havingRaw('COUNT(*) > 1')
                ->count();
            
            return (object)[
                'Category' => $category,
                'total' => $totalInCategory,
                'printed' => $printedInCategory,
                'not_printed' => $totalInCategory - $printedInCategory,
                'unique_printed' => $uniquePrinted,
                'duplicate_printed' => $duplicatePrinted,
            ];
        })->sortBy('Category')->values();

        // Day-wise printed counts (last 14 days) from printing logs with unique/duplicate breakdown
        $dayWise = PrintingLog::select(
                DB::raw('DATE(printed_at) as date'),
                DB::raw('COUNT(*) as printed_total'),
                DB::raw('COUNT(DISTINCT regid) as unique_printed'),
                DB::raw('COUNT(*) - COUNT(DISTINCT regid) as duplicate_printed')
            )
            ->where('printed_at', '>=', now()->subDays(14))
            ->groupBy(DB::raw('DATE(printed_at)'))
            ->orderBy(DB::raw('DATE(printed_at)'))
            ->get();

        // Day + category wise (heatmap data) from printing logs with unique/duplicate breakdown
        $dayCategory = PrintingLog::select(
                'category',
                DB::raw('DATE(printed_at) as date'),
                DB::raw('COUNT(*) as printed_total'),
                DB::raw('COUNT(DISTINCT regid) as unique_printed'),
                DB::raw('COUNT(*) - COUNT(DISTINCT regid) as duplicate_printed')
            )
            ->where('printed_at', '>=', now()->subDays(14))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category', DB::raw('DATE(printed_at)'))
            ->orderBy(DB::raw('DATE(printed_at)'))
            ->orderBy('category')
            ->get();

        $categories = Category::orderBy('Category')->pluck('Category');

        // Scanning Statistics
        $totalScans = ScanningLog::count();
        $totalAllowed = ScanningLog::where('is_allowed', true)->count();
        $totalDenied = ScanningLog::where('is_allowed', false)->count();
        
        // Count unique RegIDs scanned (to identify duplicates)
        $uniqueScans = ScanningLog::distinct('regid')->count('regid');
        $duplicateScans = $totalScans - $uniqueScans;
        
        // Location-wise scanning statistics
        $locationStats = ScanningLog::select(
                'location_id',
                'location_name',
                DB::raw('COUNT(*) as total_scans'),
                DB::raw('SUM(CASE WHEN is_allowed = 1 THEN 1 ELSE 0 END) as allowed'),
                DB::raw('SUM(CASE WHEN is_allowed = 0 THEN 1 ELSE 0 END) as denied'),
                DB::raw('COUNT(DISTINCT regid) as unique_regids'),
                DB::raw('COUNT(*) - COUNT(DISTINCT regid) as duplicate_scans')
            )
            ->groupBy('location_id', 'location_name')
            ->orderBy('location_name')
            ->get();

        // Day-wise scanning (last 14 days)
        $dayWiseScans = ScanningLog::select(
                DB::raw('DATE(scanned_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_allowed = 1 THEN 1 ELSE 0 END) as allowed'),
                DB::raw('SUM(CASE WHEN is_allowed = 0 THEN 1 ELSE 0 END) as denied')
            )
            ->where('scanned_at', '>=', now()->subDays(14))
            ->groupBy(DB::raw('DATE(scanned_at)'))
            ->orderBy(DB::raw('DATE(scanned_at)'))
            ->get();

        // Day + Category wise scanning (for filtering)
        $dayCategoryScans = ScanningLog::select(
                'category',
                DB::raw('DATE(scanned_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_allowed = 1 THEN 1 ELSE 0 END) as allowed'),
                DB::raw('SUM(CASE WHEN is_allowed = 0 THEN 1 ELSE 0 END) as denied')
            )
            ->where('scanned_at', '>=', now()->subDays(14))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category', DB::raw('DATE(scanned_at)'))
            ->orderBy(DB::raw('DATE(scanned_at)'))
            ->orderBy('category')
            ->get();

        // Location + Day wise scanning with approved/rejected breakdown
        $locationDayScans = ScanningLog::select(
                'location_name',
                DB::raw('DATE(scanned_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_allowed = 1 THEN 1 ELSE 0 END) as allowed'),
                DB::raw('SUM(CASE WHEN is_allowed = 0 THEN 1 ELSE 0 END) as denied')
            )
            ->where('scanned_at', '>=', now()->subDays(14))
            ->groupBy('location_name', DB::raw('DATE(scanned_at)'))
            ->orderBy(DB::raw('DATE(scanned_at)'))
            ->get();

        return view('admin.dashboard', [
            'totalRegistrations' => $totalRegistrations,
            'totalPrinted'       => $usersWhoPrinted, // Users who have printed
            'totalPrints'        => $totalPrints, // Total print attempts
            'totalNotPrinted'    => $totalNotPrinted,
            'totalUniquePrinted' => $uniquePrints, // Users printed exactly once
            'totalDuplicate'     => $duplicatePrints, // Users printed more than once
            'categoryStats'      => $categoryStats,
            'dayWise'            => $dayWise,
            'dayCategory'        => $dayCategory,
            'categoriesList'     => $categories,
            // Scanning stats
            'totalScans'         => $totalScans,
            'totalAllowed'       => $totalAllowed,
            'totalDenied'        => $totalDenied,
            'uniqueScans'        => $uniqueScans,
            'duplicateScans'     => $duplicateScans,
            'locationStats'      => $locationStats,
            'dayWiseScans'       => $dayWiseScans,
            'dayCategoryScans'   => $dayCategoryScans,
            'locationDayScans'   => $locationDayScans,
        ]);
    }
}

