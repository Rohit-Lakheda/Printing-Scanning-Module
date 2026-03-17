<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadgeLayoutSetting;
use App\Models\Category;
use App\Models\BadgeDisplaySetting;
use Illuminate\Http\Request;

class BadgeLayoutController extends Controller
{
    private function normalizeLayoutType(?string $type): string
    {
        $type = strtolower(trim((string) $type));
        return in_array($type, ['normal', 'bulk'], true) ? $type : 'normal';
    }

    public function index()
    {
        $categories = Category::all();
        return view('admin.badge-layout.index', compact('categories'));
    }

    public function edit(Request $request, $category)
    {
        $layoutType = $this->normalizeLayoutType($request->query('type'));

        $categoryModel = Category::where('Category', $category)->first();
        if (!$categoryModel) {
            return redirect()->route('admin.badge-layout.index')
                ->with('error', 'Category not found.');
        }

        $displaySettings = BadgeDisplaySetting::where('Category', trim($category))
            ->where('layout_type', $layoutType)
            ->first();

        $layoutSettings = BadgeLayoutSetting::where('Category', trim($category))
            ->where('layout_type', $layoutType)
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
                // Check both boolean cast and raw value
                $value = $displaySettings->getAttribute($checkField);
                if ($value == 1 || $value === true || $value == '1') {
                    $visibleFields[] = $field;
                }
            }
            
            // Add static text labels if they exist
            for ($i = 1; $i <= 5; $i++) {
                $staticKey = 'Instruction' . $i;
                $staticLayout = $layoutSettings->get($staticKey);
                if ($staticLayout && $staticLayout->static_text_value) {
                    $visibleFields[] = $staticKey;
                }
            }
        } else {
            // If no display settings, show default fields
            $visibleFields = ['RegID', 'Name'];
        }

        return view('admin.badge-layout.edit', compact('categoryModel', 'layoutSettings', 'visibleFields', 'displaySettings', 'layoutType'));
    }

    public function update(Request $request, $category)
    {
        $layoutType = $this->normalizeLayoutType($request->input('layout_type') ?? $request->query('type'));

        // Handle JSON string input
        $layoutsInput = $request->input('layouts');
        if (is_string($layoutsInput)) {
            $layoutsInput = json_decode($layoutsInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['layouts' => 'Invalid layout data format.']);
            }
        }
        
        // Merge decoded layouts back into request for validation
        $request->merge(['layouts' => $layoutsInput]);
        
        $validated = $request->validate([
            'layouts' => 'required|array',
            'layouts.*.field_name' => 'required|string',
            'layouts.*.static_text_key' => 'nullable|string', // For static labels (Instruction1-5)
            'layouts.*.static_text_value' => 'nullable|string', // Text value for static labels
            'layouts.*.margin_top' => 'nullable|numeric|min:0',
            'layouts.*.sequence' => 'required|integer|min:0',
            'layouts.*.text_align' => 'required|string|in:left,center,right',
            'layouts.*.font_family' => 'required|string',
            'layouts.*.font_weight' => 'required|string|in:normal,bold',
            'layouts.*.color' => 'required|string',
            'layouts.*.font_size' => 'nullable|numeric|min:1|max:50', // For non-QR elements
            'layouts.*.width' => 'nullable|numeric|min:5|max:100', // For QR code only
            'layouts.*.height' => 'nullable|numeric|min:5|max:100', // For QR code only
        ]);

        // Delete existing layouts for this category
        BadgeLayoutSetting::where('Category', $category)
            ->where('layout_type', $layoutType)
            ->delete();

        // Create new layouts
        foreach ($validated['layouts'] as $layoutData) {
            $layoutData['Category'] = $category;
            // Only include fields that exist in the model
            $cleanData = [
                'Category' => $category,
                'layout_type' => $layoutType,
                'field_name' => $layoutData['field_name'],
                'margin_top' => $layoutData['margin_top'] ?? 0,
                'sequence' => $layoutData['sequence'] ?? 0,
                'text_align' => $layoutData['text_align'],
                'font_family' => $layoutData['font_family'],
                'font_weight' => $layoutData['font_weight'],
                'color' => $layoutData['color'],
            ];
            
            // Add static text fields if present
            if (isset($layoutData['static_text_key'])) {
                $cleanData['static_text_key'] = $layoutData['static_text_key'];
            }
            if (isset($layoutData['static_text_value'])) {
                $cleanData['static_text_value'] = $layoutData['static_text_value'];
            }
            
            // Add font_size for non-QR elements
            if (isset($layoutData['font_size'])) {
                $cleanData['font_size'] = $layoutData['font_size'];
            }
            
            // Add width/height for QR code
            if (isset($layoutData['width'])) {
                $cleanData['width'] = $layoutData['width'];
            }
            if (isset($layoutData['height'])) {
                $cleanData['height'] = $layoutData['height'];
            }
            
            BadgeLayoutSetting::create($cleanData);
        }

        // --- Update badge display settings from the same form (checkboxes) ---
        $displaySettingsInput = $request->input('display_settings', []);

        if (is_array($displaySettingsInput)) {
            $booleanFields = [
                'ShowCategory', 'RegID', 'Name', 'Email', 'Mobile', 'Designation', 'Company',
                'Country', 'State', 'City',
                'Additional1', 'Additional2', 'Additional3', 'Additional4', 'Additional5',
                'IsUniquePrint', 'QRcode',
            ];

            $displayData = ['Category' => $category, 'layout_type' => $layoutType];
            foreach ($booleanFields as $field) {
                $displayData[$field] = array_key_exists($field, $displaySettingsInput) ? 1 : 0;
            }

            BadgeDisplaySetting::updateOrCreate(
                ['Category' => $category, 'layout_type' => $layoutType],
                $displayData
            );
        }

        return redirect()
            ->route('admin.badge-layout.edit', ['category' => $category, 'type' => $layoutType])
            ->with('success', 'Badge layout saved successfully.');
    }
}
