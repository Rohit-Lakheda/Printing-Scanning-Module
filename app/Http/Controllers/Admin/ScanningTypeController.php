<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSetting;
use Illuminate\Http\Request;

class ScanningTypeController extends Controller
{
    public function edit()
    {
        $eventSettings = EventSetting::getSettings();
        return view('admin.scanning.type', compact('eventSettings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'scanning_type' => 'required|in:camera,device',
        ]);

        $eventSettings = EventSetting::getSettings();
        $eventSettings->scanning_type = $validated['scanning_type'];
        $eventSettings->save();

        return redirect()->route('admin.scanning.type.edit')
            ->with('success', 'Scanning type updated successfully.');
    }
}

