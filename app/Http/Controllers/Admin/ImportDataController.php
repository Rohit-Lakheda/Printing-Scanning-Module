<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserDetailsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\UserDetailsImport;
use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('Category')->get();

        return view('admin.import-data.index', compact('categories'));
    }

    public function downloadTemplate(Request $request)
    {
        return Excel::download(new UserDetailsTemplateExport(), 'user_details_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|exists:categories,Category',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new UserDetailsImport($validated['category']), $request->file('file'));

        return redirect()
            ->route('admin.import-data.index')
            ->with('success', 'Import completed successfully.');
    }
}

