<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterBadge;
use App\Models\Location;
use Illuminate\Http\Request;

class MasterBadgeController extends Controller
{
    /**
     * Display a listing of master badges
     */
    public function index()
    {
        $masterBadges = MasterBadge::with('locations')->get();
        return view('admin.master-badges.index', compact('masterBadges'));
    }

    /**
     * Show the form for creating a new master badge
     */
    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        return view('admin.master-badges.create', compact('locations'));
    }

    /**
     * Store a newly created master badge
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

        $masterBadge = MasterBadge::create([
            'regid' => $validated['regid'],
            'reason' => $validated['reason'] ?? null,
        ]);

        // Attach locations
        $masterBadge->locations()->attach($validated['locations']);

        return redirect()->route('admin.master-regids.index')
            ->with('success', 'Master RegID created successfully.');
    }

    /**
     * Display the specified master badge
     */
    public function show(MasterBadge $master_regid)
    {
        $master_regid->load('locations');
        return view('admin.master-badges.show', compact('master_regid'));
    }

    /**
     * Show the form for editing the specified master badge
     */
    public function edit(MasterBadge $master_regid)
    {
        $master_regid->load('locations');
        $locations = Location::where('is_active', true)->get();
        $selectedLocations = $master_regid->locations->pluck('id')->toArray();
        
        return view('admin.master-badges.edit', compact('master_regid', 'locations', 'selectedLocations'));
    }

    /**
     * Update the specified master badge
     */
    public function update(Request $request, MasterBadge $master_regid)
    {
        $validated = $request->validate([
            'regid' => 'required|string|max:255',
            'reason' => 'nullable|string',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
        ]);

        $master_regid->update([
            'regid' => $validated['regid'],
            'reason' => $validated['reason'] ?? null,
        ]);

        // Sync locations
        $master_regid->locations()->sync($validated['locations']);

        return redirect()->route('admin.master-regids.index')
            ->with('success', 'Master RegID updated successfully.');
    }

    /**
     * Remove the specified master badge
     */
    public function destroy(MasterBadge $master_regid)
    {
        $master_regid->delete();
        return redirect()->route('admin.master-regids.index')
            ->with('success', 'Master RegID removed successfully.');
    }
}
