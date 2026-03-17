<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiConfiguration;
use Illuminate\Http\Request;

class ApiConfigurationController extends Controller
{
    /**
     * Display a listing of API configurations
     */
    public function index()
    {
        $configurations = ApiConfiguration::orderBy('created_at', 'desc')->get();
        return view('admin.api-configurations.index', compact('configurations'));
    }

    /**
     * Show the form for creating a new API configuration
     */
    public function create()
    {
        // Get all available database columns
        $dbColumns = [
            'RegID' => 'RegID (Registration ID - auto-generated if not provided)',
            'Category' => 'Category (Required)',
            'Name' => 'Name (Required)',
            'Designation' => 'Designation',
            'Company' => 'Company',
            'Country' => 'Country',
            'State' => 'State',
            'City' => 'City',
            'Email' => 'Email',
            'Mobile' => 'Mobile',
            'Additional1' => 'Additional1',
            'Additional2' => 'Additional2',
            'Additional3' => 'Additional3',
            'Additional4' => 'Additional4',
            'Additional5' => 'Additional5',
            'ReceiptNumber' => 'ReceiptNumber',
            'IsLunchAllowed' => 'IsLunchAllowed (true/false)',
        ];

        return view('admin.api-configurations.create', compact('dbColumns'));
    }

    /**
     * Store a newly created API configuration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'field_mappings' => 'nullable', // Accept any format (JSON string or array)
        ]);

        // Generate API key
        $apiKey = ApiConfiguration::generateApiKey();

        // Process field mappings
        $fieldMappings = null;
        
        if ($request->has('field_mappings') && !empty($request->input('field_mappings'))) {
            $mappingsInput = $request->input('field_mappings');
            
            // If it's a JSON string, decode it
            if (is_string($mappingsInput)) {
                $decoded = json_decode($mappingsInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                    $fieldMappings = $decoded;
                }
            } elseif (is_array($mappingsInput)) {
                // If it's already an array, use it directly
                $fieldMappings = [];
                foreach ($mappingsInput as $apiField => $dbColumn) {
                    if (!empty($apiField) && !empty($dbColumn)) {
                        $fieldMappings[$apiField] = $dbColumn;
                    }
                }
                // If empty after processing, set to null
                if (empty($fieldMappings)) {
                    $fieldMappings = null;
                }
            }
        }

        try {
            // Handle is_active checkbox - convert to boolean
            $isActive = false;
            if ($request->has('is_active')) {
                $isActiveValue = $request->input('is_active');
                $isActive = in_array($isActiveValue, [true, '1', 'on', 1], true);
            }

            $configuration = ApiConfiguration::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'api_key' => $apiKey,
                'is_active' => $isActive,
                'field_mappings' => $fieldMappings,
            ]);

            return redirect()->route('admin.api-configurations.show', $configuration)
                ->with('success', 'Post data API configuration created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create post data API configuration: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified API configuration with sharing details
     */
    public function show(ApiConfiguration $apiConfiguration)
    {
        $baseUrl = url('/');
        $endpointUrl = url('/api/user-registration/' . $apiConfiguration->api_key);
        
        // Get default field mappings for reference
        $defaultMappings = [
            'regid' => 'RegID',
            'category' => 'Category',
            'name' => 'Name',
            'designation' => 'Designation',
            'company' => 'Company',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'email' => 'Email',
            'mobile' => 'Mobile',
            'additional1' => 'Additional1',
            'additional2' => 'Additional2',
            'additional3' => 'Additional3',
            'additional4' => 'Additional4',
            'additional5' => 'Additional5',
            'receipt_number' => 'ReceiptNumber',
            'is_lunch_allowed' => 'IsLunchAllowed',
        ];

        $fieldMappings = $apiConfiguration->field_mappings ?? $defaultMappings;

        return view('admin.api-configurations.show', compact('apiConfiguration', 'endpointUrl', 'baseUrl', 'fieldMappings', 'defaultMappings'));
    }

    /**
     * Show the form for editing the specified API configuration
     */
    public function edit(ApiConfiguration $apiConfiguration)
    {
        $dbColumns = [
            'RegID' => 'RegID (Registration ID - auto-generated if not provided)',
            'Category' => 'Category (Required)',
            'Name' => 'Name (Required)',
            'Designation' => 'Designation',
            'Company' => 'Company',
            'Country' => 'Country',
            'State' => 'State',
            'City' => 'City',
            'Email' => 'Email',
            'Mobile' => 'Mobile',
            'Additional1' => 'Additional1',
            'Additional2' => 'Additional2',
            'Additional3' => 'Additional3',
            'Additional4' => 'Additional4',
            'Additional5' => 'Additional5',
            'ReceiptNumber' => 'ReceiptNumber',
            'IsLunchAllowed' => 'IsLunchAllowed (true/false)',
        ];

        return view('admin.api-configurations.edit', compact('apiConfiguration', 'dbColumns'));
    }

    /**
     * Update the specified API configuration
     */
    public function update(Request $request, ApiConfiguration $apiConfiguration)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'field_mappings' => 'nullable', // Accept any format (JSON string or array)
        ]);

        // Process field mappings
        $fieldMappings = null;
        
        if ($request->has('field_mappings') && !empty($request->input('field_mappings'))) {
            $mappingsInput = $request->input('field_mappings');
            
            // If it's a JSON string, decode it
            if (is_string($mappingsInput)) {
                $decoded = json_decode($mappingsInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                    $fieldMappings = $decoded;
                }
            } elseif (is_array($mappingsInput)) {
                // If it's already an array, use it directly
                $fieldMappings = [];
                foreach ($mappingsInput as $apiField => $dbColumn) {
                    if (!empty($apiField) && !empty($dbColumn)) {
                        $fieldMappings[$apiField] = $dbColumn;
                    }
                }
                // If empty after processing, set to null
                if (empty($fieldMappings)) {
                    $fieldMappings = null;
                }
            }
        }

        // Handle is_active checkbox - convert to boolean
        $isActive = false;
        if ($request->has('is_active')) {
            $isActiveValue = $request->input('is_active');
            $isActive = in_array($isActiveValue, [true, '1', 'on', 1], true);
        }

        $apiConfiguration->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $isActive,
            'field_mappings' => $fieldMappings,
        ]);

        return redirect()->route('admin.api-configurations.show', $apiConfiguration)
            ->with('success', 'Post data API configuration updated successfully.');
    }

    /**
     * Remove the specified API configuration
     */
    public function destroy(ApiConfiguration $apiConfiguration)
    {
        $apiConfiguration->delete();
        return redirect()->route('admin.api-configurations.index')
            ->with('success', 'Post data API configuration deleted successfully.');
    }
}
