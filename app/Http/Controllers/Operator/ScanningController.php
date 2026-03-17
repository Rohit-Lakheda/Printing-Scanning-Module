<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\UserDetail;
use App\Models\ScanningLog;
use App\Models\BlockedRegid;
use App\Models\MasterBadge;
use App\Models\EventSetting;
use Illuminate\Http\Request;

class ScanningController extends Controller
{
    /**
     * Show location selection page
     */
    public function selectLocation()
    {
        $locations = Location::where('is_active', true)->get();
        return view('operator.scanning.select-location', compact('locations'));
    }

    /**
     * Store selected location in session and redirect to scanning page
     */
    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        $location = Location::findOrFail($validated['location_id']);
        session(['scanning_location_id' => $location->id]);
        session(['scanning_location_name' => $location->name]);

        return redirect()->route('operator.scanning.scan');
    }

    /**
     * Show the scanning page with RegID input
     */
    public function scan()
    {
        $locationId = session('scanning_location_id');
        
        if (!$locationId) {
            return redirect()->route('operator.scanning.select-location')
                ->with('error', 'Please select a location first.');
        }

        $location = Location::findOrFail($locationId);
        
        // Get today's scan counts for this location (using IST timezone)
        $todayStart = now(config('app.timezone'))->startOfDay();
        $todayEnd = now(config('app.timezone'))->endOfDay();
        
        $todayScanCount = ScanningLog::where('location_id', $locationId)
            ->whereBetween('scanned_at', [$todayStart, $todayEnd])
            ->count();
        
        $todayApprovedCount = ScanningLog::where('location_id', $locationId)
            ->whereBetween('scanned_at', [$todayStart, $todayEnd])
            ->where('is_allowed', true)
            ->count();
        
        $todayRejectedCount = ScanningLog::where('location_id', $locationId)
            ->whereBetween('scanned_at', [$todayStart, $todayEnd])
            ->where('is_allowed', false)
            ->count();
        
        $eventSettings = EventSetting::getSettings();
        $scanningType = $eventSettings->scanning_type ?: 'camera';

        return view('operator.scanning.scan', compact('location', 'todayScanCount', 'todayApprovedCount', 'todayRejectedCount', 'scanningType'));
    }

    /**
     * Check if user is allowed at the location
     */
    public function checkUser(Request $request)
    {
        $validated = $request->validate([
            'regid' => 'required|string',
        ]);

        $locationId = session('scanning_location_id');
        
        if (!$locationId) {
            return response()->json([
                'success' => false,
                'message' => 'Location not selected',
            ], 400);
        }

        $location = Location::findOrFail($locationId);
        
        // Find user by RegID
        $user = UserDetail::where('RegID', $validated['regid'])->first();

        $isAllowed = false;
        $reason = '';
        $userName = '';
        $userCategory = '';

        if (!$user) {
            $reason = 'User not found in database';
            
            // Log the scan attempt
            ScanningLog::create([
                'location_id' => $location->id,
                'location_name' => $location->name,
                'regid' => $validated['regid'],
                'user_name' => null,
                'category' => null,
                'is_allowed' => false,
                'reason' => $reason,
                'scanned_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'allowed' => false,
                'message' => 'User not found',
                'name' => '',
                'category' => '',
                'reason' => $reason,
            ]);
        }

        $userName = $user->Name ?? '';
        $userCategory = $user->Category ?? '';

        // Priority 1: Check if RegID is bypassed at this location
        $bypassedRegid = \App\Models\BypassedRegid::where('regid', $validated['regid'])->first();
        $isBypassed = false;
        $bypassedUsed = false;
        $bypassedUsageCount = 0;
        
        if ($bypassedRegid && $bypassedRegid->isBypassedAt($location->id)) {
            // Get current usage count before marking as used
            $bypassedUsageCount = $bypassedRegid->getUsageCountAt($location->id);
            
            // Check if this RegID can still be bypassed (based on max_uses)
            if ($bypassedRegid->canBeBypassedAt($location->id)) {
                // Can still be bypassed - allow it and mark as used
                $isBypassed = true;
                $bypassedRegid->markAsUsedAt($location->id);
                $isAllowed = true;
            } else {
                // Max uses reached - check category normally
                $isBypassed = false;
                $bypassedUsed = true;
            }
        }

        // Priority 2: Check if RegID is a master badge (allowed at all selected locations regardless of category)
        $masterBadge = MasterBadge::where('regid', $validated['regid'])->first();
        $isMasterBadge = false;
        $blockedRegid = null;
        
        if (!$isBypassed || $bypassedUsed) {
            if ($masterBadge && $masterBadge->isAllowedAt($location->id)) {
                $isMasterBadge = true;
                $isAllowed = true;
            } else {
                // Priority 3: Check if RegID is blocked at this location
                $blockedRegid = BlockedRegid::where('regid', $validated['regid'])->first();
                if ($blockedRegid && $blockedRegid->isBlockedAt($location->id)) {
                    $isAllowed = false;
                } else {
                    // Priority 4: Check if user's category is allowed at this location
                    $isAllowed = $location->isCategoryAllowed($userCategory);
                }
            }
        }
        
        // Priority 5: Check if unique scanning is enabled and user was already scanned
        $alreadyScanned = false;
        $previousScanTime = null;
        
        if ($location->unique_scanning && $isAllowed && !$isMasterBadge && !$isBypassed) {
            $previousScan = ScanningLog::where('location_id', $location->id)
                ->where('regid', $validated['regid'])
                ->where('is_allowed', true)
                ->orderBy('scanned_at', 'desc')
                ->first();
            
            if ($previousScan) {
                $alreadyScanned = true;
                $previousScanTime = $previousScan->scanned_at;
                $isAllowed = false; // Set to false to show red screen
            }
        }

        // Generate reason
        if ($isBypassed && !$bypassedUsed) {
            // Usage count is before marking as used, so add 1 for display
            $displayCount = $bypassedUsageCount + 1;
            $effectiveMaxUses = $bypassedRegid->getEffectiveMaxUses();
            $maxUsesText = "{$displayCount}/{$effectiveMaxUses} uses";
            $reason = "Bypassed RegID ({$maxUsesText}) - " . ($bypassedRegid->reason ?? 'Access granted');
        } elseif ($isMasterBadge) {
            $reason = "Master RegID - allowed at all selected locations";
        } elseif ($blockedRegid && $blockedRegid->isBlockedAt($location->id)) {
            $reason = "RegID is blocked at this location" . ($blockedRegid->reason ? " - " . $blockedRegid->reason : "");
        } elseif ($alreadyScanned) {
            // Format time in application timezone (IST)
            $formattedTime = $previousScanTime->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
            $reason = "Already scanned at this location on " . $formattedTime;
        } elseif ($isAllowed) {
            $reason = "Category '{$userCategory}' is allowed at location '{$location->name}'";
        } else {
            $reason = "Category '{$userCategory}' is not allowed at location '{$location->name}'";
        }

        // Log the scan attempt
        ScanningLog::create([
            'location_id' => $location->id,
            'location_name' => $location->name,
            'regid' => $validated['regid'],
            'user_name' => $userName,
            'category' => $userCategory,
            'is_allowed' => $isAllowed && !$alreadyScanned,
            'reason' => $reason,
            'scanned_at' => now(),
        ]);

        // Get updated today's scan counts for this location (using IST timezone)
        $todayStart = now(config('app.timezone'))->startOfDay();
        $todayEnd = now(config('app.timezone'))->endOfDay();
        
        $todayScanCount = ScanningLog::where('location_id', $location->id)
            ->whereBetween('scanned_at', [$todayStart, $todayEnd])
            ->count();
        
        $todayApprovedCount = ScanningLog::where('location_id', $location->id)
            ->whereBetween('scanned_at', [$todayStart, $todayEnd])
            ->where('is_allowed', true)
            ->count();
        
        $todayRejectedCount = ScanningLog::where('location_id', $location->id)
            ->whereBetween('scanned_at', [$todayStart, $todayEnd])
            ->where('is_allowed', false)
            ->count();

        return response()->json([
            'success' => true,
            'allowed' => $isAllowed && !$alreadyScanned,
            'already_scanned' => $alreadyScanned,
            'previous_scan_time' => $previousScanTime ? $previousScanTime->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') : null,
            'name' => $userName,
            'category' => $userCategory,
            'regid' => $user->RegID ?? '',
            'reason' => $reason,
            'today_scan_count' => $todayScanCount,
            'today_approved_count' => $todayApprovedCount,
            'today_rejected_count' => $todayRejectedCount,
        ]);
    }

    /**
     * Clear location from session
     */
    public function clearLocation()
    {
        session()->forget(['scanning_location_id', 'scanning_location_name']);
        return redirect()->route('operator.scanning.select-location');
    }
}
