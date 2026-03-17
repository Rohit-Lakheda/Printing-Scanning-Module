<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Prefix' => 'nullable|string|max:10',
            'Category' => 'required|string|unique:categories,Category',
            'badge_width' => 'required|numeric|min:10|max:500',
            'badge_height' => 'required|numeric|min:10|max:500',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'Prefix' => 'nullable|string|max:10',
            'Category' => 'required|string|unique:categories,Category,' . $category->id,
            'badge_width' => 'required|numeric|min:10|max:500',
            'badge_height' => 'required|numeric|min:10|max:500',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
