<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\LeadScan;
use App\Models\LeadScanAttemptLog;
use App\Models\LeadSetting;
use App\Models\UserDetail;
use App\Models\UserCredential;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeadScannerController extends Controller
{
    protected function resolveScannerCredentialId(Request $request): ?int
    {
        $sessionCredentialId = session('lead_credential_id');
        if ($sessionCredentialId) {
            return (int) $sessionCredentialId;
        }

        $token = $request->cookie('lead_portal_token');
        if (!$token) {
            return null;
        }

        $credential = UserCredential::where('remember_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$credential) {
            return null;
        }

        session([
            'lead_credential_id' => $credential->id,
            'lead_user_detail_id' => $credential->user_detail_id,
        ]);

        return (int) $credential->id;
    }

    /**
     * Pre-check scan before opening lead-type popup.
     */
    public function precheckScan(Request $request)
    {
        $validated = $request->validate([
            'regid' => 'required|string',
        ]);

        $user = UserDetail::where('RegID', $validated['regid'])->first();
        $scannerCredentialId = $this->resolveScannerCredentialId($request);
        if (!$user) {
            LeadScanAttemptLog::create([
                'scanned_by_user_id' => $scannerCredentialId,
                'regid' => $validated['regid'],
                'status' => 'user_not_found',
                'message' => 'Data with this RegID is not found. Please check the scanned QR is a badge QR.',
                'scanned_at' => now(),
                'source' => 'precheck',
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Data with this RegID is not found. Please check the scanned QR is a badge QR.',
                'code' => 'USER_NOT_FOUND',
            ], 404);
        }

        if ($scannerCredentialId) {
            $existingScan = LeadScan::where('scanned_by_user_id', $scannerCredentialId)
                ->where('regid', $validated['regid'])
                ->orderByDesc('scanned_at')
                ->first();

            if ($existingScan) {
                LeadScanAttemptLog::create([
                    'scanned_by_user_id' => $scannerCredentialId,
                    'lead_scan_id' => $existingScan->id,
                    'regid' => $validated['regid'],
                    'status' => 'already_scanned',
                    'message' => 'This user is already scanned at ' . optional($existingScan->scanned_at)->format('Y-m-d H:i:s') . '.',
                    'scanned_at' => now(),
                    'source' => 'precheck',
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'This user is already scanned at ' . optional($existingScan->scanned_at)->format('Y-m-d H:i:s') . '.',
                    'code' => 'ALREADY_SCANNED',
                    'already_scanned_at' => optional($existingScan->scanned_at)->format('Y-m-d H:i:s'),
                ], 409);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Ready to scan.',
        ]);
    }

    /**
     * Show the lead QR scanner page (mobile-friendly, offline-capable).
     */
    public function scanPage()
    {
        return view('leads.scan');
    }

    /**
     * Receive a scanned regid and create a lead scan entry.
     * This is the API endpoint used by the lead scanner (online and after offline sync).
     */
    public function storeScan(Request $request)
    {
        $validated = $request->validate([
            'regid' => 'required|string',
            'device_id' => 'nullable|string|max:255',
            'scan_time' => 'nullable|date',
            'lead_type' => 'nullable|string|max:50',
            'comments' => 'nullable|string',
        ]);

        $user = UserDetail::where('RegID', $validated['regid'])->first();
        $scannerCredentialId = $this->resolveScannerCredentialId($request);

        if (!$user) {
            LeadScanAttemptLog::create([
                'scanned_by_user_id' => $scannerCredentialId,
                'regid' => $validated['regid'],
                'status' => 'user_not_found',
                'message' => 'Data with this RegID is not found. Please check the scanned QR is a badge QR.',
                'scanned_at' => now(),
                'source' => 'scan',
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Data with this RegID is not found. Please check the scanned QR is a badge QR.',
                'code' => 'USER_NOT_FOUND',
            ], 404);
        }

        if ($scannerCredentialId) {
            $existingScan = LeadScan::where('scanned_by_user_id', $scannerCredentialId)
                ->where('regid', $validated['regid'])
                ->orderByDesc('scanned_at')
                ->first();
            if ($existingScan) {
                LeadScanAttemptLog::create([
                    'scanned_by_user_id' => $scannerCredentialId,
                    'lead_scan_id' => $existingScan->id,
                    'regid' => $validated['regid'],
                    'status' => 'already_scanned',
                    'message' => 'This user is already scanned at ' . optional($existingScan->scanned_at)->format('Y-m-d H:i:s') . '.',
                    'scanned_at' => now(),
                    'source' => 'scan',
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'This user is already scanned at ' . optional($existingScan->scanned_at)->format('Y-m-d H:i:s') . '.',
                    'code' => 'ALREADY_SCANNED',
                    'already_scanned_at' => optional($existingScan->scanned_at)->format('Y-m-d H:i:s'),
                ], 409);
            }
        }

        $leadSetting = LeadSetting::getDefault();
        $sharedFields = $leadSetting->sharedFields();

        $scanTimestamp = isset($validated['scan_time'])
            ? Carbon::parse($validated['scan_time'])->setTimezone(config('app.timezone'))
            : now(config('app.timezone'));

        $leadScan = LeadScan::create([
            'user_detail_id' => $user->id,
            'regid' => $validated['regid'],
            'scanned_at' => $scanTimestamp,
            'device_id' => $validated['device_id'] ?? null,
            'scanned_by_user_id' => $scannerCredentialId,
            'source' => 'qr',
            'location_name' => null,
            'lead_type' => $validated['lead_type'] ?? null,
            'lead_comments' => $validated['comments'] ?? null,
        ]);

        LeadScanAttemptLog::create([
            'scanned_by_user_id' => $scannerCredentialId,
            'lead_scan_id' => $leadScan->id,
            'regid' => $validated['regid'],
            'status' => 'success',
            'message' => 'Lead scan recorded successfully.',
            'scanned_at' => $scanTimestamp,
            'source' => 'scan',
        ]);

        $userPayload = [];
        foreach ($sharedFields as $field) {
            $userPayload[$field] = $user->{$field} ?? null;
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead scan recorded.',
            'lead_scan_id' => $leadScan->id,
            'user' => $userPayload,
        ]);
    }
}

