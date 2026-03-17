<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventLogoController extends Controller
{
    /**
     * Show the form for uploading/updating event logo
     */
    public function index()
    {
        $settings = EventSetting::getSettings();
        return view('admin.event-logo.index', compact('settings'));
    }

    /**
     * Upload/Update event logo
     */
    public function upload(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = EventSetting::getSettings();

        // Delete old logo if exists
        if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
        }

        // Store new logo
        $logoPath = $request->file('logo')->store('event-logo', 'public');
        
        $settings->update(['logo_path' => $logoPath]);

        return redirect()->route('admin.event-logo.index')
            ->with('success', 'Event logo uploaded successfully.');
    }

    /**
     * Delete event logo
     */
    public function delete()
    {
        $settings = EventSetting::getSettings();

        if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
        }

        $settings->update(['logo_path' => null]);

        return redirect()->route('admin.event-logo.index')
            ->with('success', 'Event logo deleted successfully.');
    }
}
