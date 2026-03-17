<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\LocationCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of locations
     */
    public function index()
    {
        $locations = Location::with('allowedCategories')->get();
        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.locations.create', compact('categories'));
    }

    /**
     * Store a newly created location
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'unique_scanning' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,Category',
        ]);

        $location = Location::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
            'unique_scanning' => $request->has('unique_scanning') ? true : false,
        ]);

        // Add allowed categories
        if (isset($validated['categories']) && is_array($validated['categories'])) {
            foreach ($validated['categories'] as $category) {
                LocationCategory::create([
                    'location_id' => $location->id,
                    'category' => $category,
                ]);
            }
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified location
     */
    public function show(Location $location)
    {
        $location->load('allowedCategories');
        $allCategories = Category::all();
        return view('admin.locations.show', compact('location', 'allCategories'));
    }

    /**
     * Show the form for editing the specified location
     */
    public function edit(Location $location)
    {
        $location->load('allowedCategories');
        $categories = Category::all();
        $allowedCategories = $location->allowedCategories->pluck('category')->toArray();
        
        return view('admin.locations.edit', compact('location', 'categories', 'allowedCategories'));
    }

    /**
     * Update the specified location
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'unique_scanning' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,Category',
        ]);

        $location->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
            'unique_scanning' => $request->has('unique_scanning') ? true : false,
        ]);

        // Remove all existing category mappings
        $location->allowedCategories()->delete();

        // Add new allowed categories
        if (isset($validated['categories']) && is_array($validated['categories'])) {
            foreach ($validated['categories'] as $category) {
                LocationCategory::create([
                    'location_id' => $location->id,
                    'category' => $category,
                ]);
            }
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified location
     */
    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}
