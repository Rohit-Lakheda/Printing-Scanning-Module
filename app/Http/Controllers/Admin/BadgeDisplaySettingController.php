<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadgeDisplaySetting;
use App\Models\Category;
use Illuminate\Http\Request;

class BadgeDisplaySettingController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        // Use keyBy with trim to handle any whitespace issues
        $settings = BadgeDisplaySetting::all()->mapWithKeys(function ($item) {
            return [trim($item->Category) => $item];
        });
        return view('admin.badge-display-settings.index', compact('categories', 'settings'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.badge-display-settings.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Category' => 'required|string',
        ]);

        // Get all boolean fields and set to 0 if not present
        $booleanFields = ['RegID', 'Name', 'Email', 'Mobile', 'Designation', 'Company', 
                         'Country', 'State', 'City', 'Additional1', 'Additional2', 
                         'Additional3', 'Additional4', 'Additional5', 'IsUniquePrint', 'QRcode'];
        
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) && ($request->input($field) == '1' || $request->input($field) == 1) ? 1 : 0;
        }

        BadgeDisplaySetting::updateOrCreate(
            ['Category' => $validated['Category']],
            $validated
        );

        return redirect()->route('admin.badge-display-settings.index')
            ->with('success', 'Badge display settings saved successfully.');
    }

    public function edit($category)
    {
        $setting = BadgeDisplaySetting::where('Category', $category)->first();
        $categories = Category::all();
        
        if (!$setting) {
            $setting = new BadgeDisplaySetting(['Category' => $category]);
        }
        
        return view('admin.badge-display-settings.edit', compact('setting', 'categories'));
    }

    public function update(Request $request, $category)
    {
        $validated = $request->validate([
            'Category' => 'required|string',
        ]);

        // Ensure Category is set (from route parameter or form)
        $validated['Category'] = $request->input('Category', $category);

        // Get all boolean fields and set to 0 if not present
        $booleanFields = ['RegID', 'Name', 'Email', 'Mobile', 'Designation', 'Company', 
                         'Country', 'State', 'City', 'Additional1', 'Additional2', 
                         'Additional3', 'Additional4', 'Additional5', 'IsUniquePrint', 'QRcode'];
        
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) && ($request->input($field) == '1' || $request->input($field) == 1) ? 1 : 0;
        }

        BadgeDisplaySetting::updateOrCreate(
            ['Category' => $validated['Category']],
            $validated
        );

        return redirect()->route('admin.badge-display-settings.index')
            ->with('success', 'Badge display settings updated successfully.');
    }
}
