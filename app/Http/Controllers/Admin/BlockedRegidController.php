<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedRegid;
use App\Models\Location;
use Illuminate\Http\Request;

class BlockedRegidController extends Controller
{
    /**
     * Display a listing of blocked RegIDs
     */
    public function index()
    {
        $blockedRegids = BlockedRegid::with('locations')->get();
        return view('admin.blocked-regids.index', compact('blockedRegids'));
    }

    /**
     * Show the form for creating a new blocked RegID
     */
    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        return view('admin.blocked-regids.create', compact('locations'));
    }

    /**
     * Store a newly created blocked RegID
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'regid' => 'required|string|max:255',
            'reason' => 'nullable|string',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
        ]);

        // Check if RegID exists in user_details
        $userExists = \App\Models\UserDetail::where('RegID', $validated['regid'])->exists();
        if (!$userExists) {
            return back()->withErrors(['regid' => 'RegID not found in user database.'])->withInput();
        }

        $blockedRegid = BlockedRegid::create([
            'regid' => $validated['regid'],
            'reason' => $validated['reason'] ?? null,
        ]);

        // Attach locations
        $blockedRegid->locations()->attach($validated['locations']);

        return redirect()->route('admin.blocked-regids.index')
            ->with('success', 'RegID blocked successfully.');
    }

    /**
     * Display the specified blocked RegID
     */
    public function show(BlockedRegid $blockedRegid)
    {
        $blockedRegid->load('locations');
        return view('admin.blocked-regids.show', compact('blockedRegid'));
    }

    /**
     * Show the form for editing the specified blocked RegID
     */
    public function edit(BlockedRegid $blockedRegid)
    {
        $blockedRegid->load('locations');
        $locations = Location::where('is_active', true)->get();
        $selectedLocations = $blockedRegid->locations->pluck('id')->toArray();
        
        return view('admin.blocked-regids.edit', compact('blockedRegid', 'locations', 'selectedLocations'));
    }

    /**
     * Update the specified blocked RegID
     */
    public function update(Request $request, BlockedRegid $blockedRegid)
    {
        $validated = $request->validate([
            'regid' => 'required|string|max:255',
            'reason' => 'nullable|string',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
        ]);

        $blockedRegid->update([
            'regid' => $validated['regid'],
            'reason' => $validated['reason'] ?? null,
        ]);

        // Sync locations
        $blockedRegid->locations()->sync($validated['locations']);

        return redirect()->route('admin.blocked-regids.index')
            ->with('success', 'Blocked RegID updated successfully.');
    }

    /**
     * Remove the specified blocked RegID
     */
    public function destroy(BlockedRegid $blockedRegid)
    {
        $blockedRegid->delete();
        return redirect()->route('admin.blocked-regids.index')
            ->with('success', 'Blocked RegID removed successfully.');
    }
}
