<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BypassedRegid;
use App\Models\Location;
use Illuminate\Http\Request;

class BypassedRegidController extends Controller
{
    /**
     * Display a listing of bypassed RegIDs
     */
    public function index()
    {
        $bypassedRegids = BypassedRegid::with('locations')->get();
        return view('admin.bypassed-regids.index', compact('bypassedRegids'));
    }

    /**
     * Show the form for creating a new bypassed RegID
     */
    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        return view('admin.bypassed-regids.create', compact('locations'));
    }

    /**
     * Store a newly created bypassed RegID
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'regid' => 'required|string|max:255',
            'reason' => 'required|string', // Reason is compulsory
            'max_uses' => 'nullable|integer|min:1',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
        ]);

        // Check if RegID exists in user_details
        $userExists = \App\Models\UserDetail::where('RegID', $validated['regid'])->exists();
        if (!$userExists) {
            return back()->withErrors(['regid' => 'RegID not found in user database.'])->withInput();
        }

        $bypassedRegid = BypassedRegid::create([
            'regid' => $validated['regid'],
            'reason' => $validated['reason'],
            'max_uses' => $validated['max_uses'] ?? null, // null means unlimited
        ]);

        // Attach locations
        $bypassedRegid->locations()->attach($validated['locations']);

        return redirect()->route('admin.bypassed-regids.index')
            ->with('success', 'Bypassed RegID created successfully.');
    }

    /**
     * Display the specified bypassed RegID
     */
    public function show(BypassedRegid $bypassedRegid)
    {
        $bypassedRegid->load('locations', 'usageLogs.location');
        return view('admin.bypassed-regids.show', compact('bypassedRegid'));
    }

    /**
     * Show the form for editing the specified bypassed RegID
     */
    public function edit(BypassedRegid $bypassedRegid)
    {
        $bypassedRegid->load('locations');
        $locations = Location::where('is_active', true)->get();
        $selectedLocations = $bypassedRegid->locations->pluck('id')->toArray();
        
        return view('admin.bypassed-regids.edit', compact('bypassedRegid', 'locations', 'selectedLocations'));
    }

    /**
     * Update the specified bypassed RegID
     */
    public function update(Request $request, BypassedRegid $bypassedRegid)
    {
        $validated = $request->validate([
            'regid' => 'required|string|max:255',
            'reason' => 'required|string', // Reason is compulsory
            'max_uses' => 'nullable|integer|min:1',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
        ]);

        $bypassedRegid->update([
            'regid' => $validated['regid'],
            'reason' => $validated['reason'],
            'max_uses' => $validated['max_uses'] ?? null, // null means unlimited
        ]);

        // Sync locations
        $bypassedRegid->locations()->sync($validated['locations']);

        return redirect()->route('admin.bypassed-regids.index')
            ->with('success', 'Bypassed RegID updated successfully.');
    }

    /**
     * Remove the specified bypassed RegID
     */
    public function destroy(BypassedRegid $bypassedRegid)
    {
        $bypassedRegid->delete();
        return redirect()->route('admin.bypassed-regids.index')
            ->with('success', 'Bypassed RegID removed successfully.');
    }
}
