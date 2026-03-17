<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GetDataApiConfiguration;
use Illuminate\Http\Request;

class GetDataApiConfigurationController extends Controller
{
    private function availableUserFields(): array
    {
        return [
            'RegID',
            'Category',
            'Name',
            'Designation',
            'Company',
            'Country',
            'State',
            'City',
            'Email',
            'Mobile',
            'Additional1',
            'Additional2',
            'Additional3',
            'Additional4',
            'Additional5',
            'ReceiptNumber',
            'IsLunchAllowed',
        ];
    }

    public function index()
    {
        $configurations = GetDataApiConfiguration::orderByDesc('created_at')->get();
        return view('admin.get-data-api-configurations.index', compact('configurations'));
    }

    public function create()
    {
        $availableFields = $this->availableUserFields();
        return view('admin.get-data-api-configurations.create', compact('availableFields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
            'input_fields' => 'required|array|min:1',
            'input_fields.*' => 'string',
            'response_fields' => 'required|array|min:1',
            'response_fields.*' => 'string',
        ]);

        $available = $this->availableUserFields();
        $inputFields = array_values(array_unique(array_intersect($validated['input_fields'], $available)));
        $responseFields = array_values(array_unique(array_intersect($validated['response_fields'], $available)));

        if (empty($inputFields) || empty($responseFields)) {
            return back()->withInput()->withErrors([
                'error' => 'Please select valid input and response fields.',
            ]);
        }

        $configuration = GetDataApiConfiguration::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'api_key' => GetDataApiConfiguration::generateApiKey(),
            'is_active' => $request->has('is_active'),
            'input_fields' => $inputFields,
            'response_fields' => $responseFields,
        ]);

        return redirect()
            ->route('admin.get-data-api-configurations.show', $configuration)
            ->with('success', 'Get data API configuration created successfully.');
    }

    public function show(GetDataApiConfiguration $getDataApiConfiguration)
    {
        $endpointUrl = url('/api/user-data/' . $getDataApiConfiguration->api_key);
        return view('admin.get-data-api-configurations.show', compact('getDataApiConfiguration', 'endpointUrl'));
    }

    public function edit(GetDataApiConfiguration $getDataApiConfiguration)
    {
        $availableFields = $this->availableUserFields();
        return view('admin.get-data-api-configurations.edit', compact('getDataApiConfiguration', 'availableFields'));
    }

    public function update(Request $request, GetDataApiConfiguration $getDataApiConfiguration)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
            'input_fields' => 'required|array|min:1',
            'input_fields.*' => 'string',
            'response_fields' => 'required|array|min:1',
            'response_fields.*' => 'string',
        ]);

        $available = $this->availableUserFields();
        $inputFields = array_values(array_unique(array_intersect($validated['input_fields'], $available)));
        $responseFields = array_values(array_unique(array_intersect($validated['response_fields'], $available)));

        if (empty($inputFields) || empty($responseFields)) {
            return back()->withInput()->withErrors([
                'error' => 'Please select valid input and response fields.',
            ]);
        }

        $getDataApiConfiguration->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'input_fields' => $inputFields,
            'response_fields' => $responseFields,
        ]);

        return redirect()
            ->route('admin.get-data-api-configurations.show', $getDataApiConfiguration)
            ->with('success', 'Get data API configuration updated successfully.');
    }

    public function destroy(GetDataApiConfiguration $getDataApiConfiguration)
    {
        $getDataApiConfiguration->delete();
        return redirect()
            ->route('admin.get-data-api-configurations.index')
            ->with('success', 'Get data API configuration deleted successfully.');
    }
}

