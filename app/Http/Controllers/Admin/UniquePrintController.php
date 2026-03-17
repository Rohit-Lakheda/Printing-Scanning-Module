<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class UniquePrintController extends Controller
{
    /**
     * Display the unique print and receipt number settings page
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.unique-print.index', compact('categories'));
    }

    /**
     * Update unique print and receipt number settings for categories
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.unique_printing' => 'nullable|boolean',
            'categories.*.receipt_number_required' => 'nullable|boolean',
        ]);

        foreach ($validated['categories'] as $categoryData) {
            Category::where('id', $categoryData['id'])->update([
                'unique_printing' => isset($categoryData['unique_printing']) ? (bool)$categoryData['unique_printing'] : false,
                'receipt_number_required' => isset($categoryData['receipt_number_required']) ? (bool)$categoryData['receipt_number_required'] : false,
            ]);
        }

        return redirect()->route('admin.unique-print.index')
            ->with('success', 'Settings updated successfully.');
    }
}
